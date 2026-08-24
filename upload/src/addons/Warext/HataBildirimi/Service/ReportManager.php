<?php

namespace Warext\HataBildirimi\Service;

use Warext\HataBildirimi\Entity\Report;
use XF\Entity\User;
use XF\Service\AbstractService;

class ReportManager extends AbstractService
{
    public const STATUSES = [
        'new', 'investigating', 'confirmed', 'in_progress', 'resolved',
        'cannot_reproduce', 'invalid', 'duplicate', 'archived'
    ];

    public function changeStatus(User $actor, Report $report, string $status): void
    {
        if (!in_array($status, self::STATUSES, true))
        {
            throw new \InvalidArgumentException('Geçersiz hata durumu.');
        }

        $oldStatus = (string)$report->status;
        if ($oldStatus === $status)
        {
            return;
        }

        $oldResolution = (string)$report->fixed_in_version;
        $hadResolution = $oldResolution !== '' || (string)$report->resolution_note !== '';
        $clearResolution = $hadResolution && !in_array($status, ['resolved', 'archived'], true);

        $report->status = $status;
        $report->updated_date = \XF::$time;
        $report->resolved_date = in_array($status, ['resolved', 'cannot_reproduce', 'duplicate', 'invalid'], true)
            ? \XF::$time
            : 0;

        if ($status !== 'duplicate')
        {
            $report->duplicate_report_id = 0;
        }

        if ($clearResolution)
        {
            $report->fixed_in_version = '';
            $report->resolution_note = null;
        }

        $report->save();

        $this->log($report, $actor, 'status', $oldStatus, $status);
        if ($clearResolution)
        {
            $this->log($report, $actor, 'resolution_cleared', $oldResolution, '');
        }
        $this->alertOwner($report, $actor, 'status', ['status' => $status]);
    }

    public function setResolution(User $actor, Report $report, string $fixedInVersion, string $resolutionNote): void
    {
        $fixedInVersion = mb_substr(trim($fixedInVersion), 0, 100);
        $resolutionNote = mb_substr(trim($resolutionNote), 0, 10000);
        $oldVersion = (string)$report->fixed_in_version;
        $oldNote = (string)$report->resolution_note;

        if ($oldVersion === $fixedInVersion && $oldNote === $resolutionNote)
        {
            return;
        }

        if (($fixedInVersion !== '' || $resolutionNote !== '') && $report->status !== 'resolved')
        {
            throw new \InvalidArgumentException('Çözüm sürümü ve çözüm notu yalnızca çözüldü durumundaki raporlara eklenebilir.');
        }

        $report->fixed_in_version = $fixedInVersion;
        $report->resolution_note = $resolutionNote !== '' ? $resolutionNote : null;
        $report->updated_date = \XF::$time;
        $report->save();

        $this->log($report, $actor, 'resolution', $oldVersion, $fixedInVersion);
    }

    public function assign(User $actor, Report $report, int $assigneeUserId): void
    {
        $oldUserId = (int)$report->assignee_user_id;
        if ($oldUserId === $assigneeUserId)
        {
            return;
        }

        if ($assigneeUserId > 0)
        {
            $target = $this->app->em()->find('XF:User', $assigneeUserId);
            if (!$target || !$target->is_staff || !$target->hasPermission('wrxtHata', 'manage'))
            {
                throw new \InvalidArgumentException('Rapor yalnızca hata bildirimlerini yönetme izni olan personele atanabilir.');
            }
        }

        $report->assignee_user_id = $assigneeUserId;
        $report->updated_date = \XF::$time;
        $report->save();

        $this->log($report, $actor, 'assignee', (string)$oldUserId, (string)$assigneeUserId);
    }

    public function markDuplicate(User $actor, Report $report, int $masterReportId): Report
    {
        if ($masterReportId <= 0 || $masterReportId === (int)$report->report_id)
        {
            throw new \InvalidArgumentException('Geçerli ve farklı bir ana hata raporu seçmelisiniz.');
        }

        $master = $this->app->em()->find('Warext\HataBildirimi:Report', $masterReportId);
        if (!$master)
        {
            throw new \InvalidArgumentException('Ana hata raporu bulunamadı.');
        }

        $depth = 0;
        while ($master->duplicate_report_id && $depth < 10)
        {
            $next = $this->app->em()->find('Warext\HataBildirimi:Report', (int)$master->duplicate_report_id);
            if (!$next || (int)$next->report_id === (int)$report->report_id)
            {
                break;
            }
            $master = $next;
            $depth++;
        }

        if ((int)$master->report_id === (int)$report->report_id)
        {
            throw new \InvalidArgumentException('Yinelenen rapor ilişkisi döngü oluşturamaz.');
        }

        $oldStatus = (string)$report->status;
        $oldDuplicate = (int)$report->duplicate_report_id;
        $oldVersion = (string)$report->fixed_in_version;
        $report->duplicate_report_id = (int)$master->report_id;
        $report->possible_duplicate_report_id = 0;
        $report->duplicate_score = 0;
        $report->status = 'duplicate';
        $report->fixed_in_version = '';
        $report->resolution_note = null;
        $report->resolved_date = \XF::$time;
        $report->updated_date = \XF::$time;
        $report->save();

        $this->log($report, $actor, 'duplicate_merge', (string)$oldDuplicate, (string)$master->report_id);
        if ($oldStatus !== 'duplicate')
        {
            $this->log($report, $actor, 'status', $oldStatus, 'duplicate');
        }
        if ($oldVersion !== '')
        {
            $this->log($report, $actor, 'resolution_cleared', $oldVersion, '');
        }
        $this->alertOwner($report, $actor, 'status', ['status' => 'duplicate']);

        return $master;
    }

    public function dismissDuplicateCandidate(User $actor, Report $report): void
    {
        if (!$report->possible_duplicate_report_id)
        {
            return;
        }

        $old = (int)$report->possible_duplicate_report_id;
        $score = (int)$report->duplicate_score;
        $report->possible_duplicate_report_id = 0;
        $report->duplicate_score = 0;
        $report->updated_date = \XF::$time;
        $report->save();

        $this->log($report, $actor, 'duplicate_candidate_dismissed', (string)$old, (string)$score);
    }

    public function addMessage(User $actor, Report $report, string $message, string $type): void
    {
        $message = trim($message);
        if ($message === '')
        {
            throw new \InvalidArgumentException('Mesaj boş bırakılamaz.');
        }
        if (mb_strlen($message) > 10000)
        {
            throw new \InvalidArgumentException('Mesaj en fazla 10.000 karakter olabilir.');
        }
        if (!in_array($type, ['user', 'staff', 'note'], true))
        {
            throw new \InvalidArgumentException('Geçersiz mesaj türü.');
        }

        $db = $this->app->db();
        $db->beginTransaction();

        try
        {
            $entity = $this->app->em()->create('Warext\HataBildirimi:ReportMessage');
            $entity->report_id = $report->report_id;
            $entity->user_id = $actor->user_id;
            $entity->username = $actor->username;
            $entity->message_type = $type;
            $entity->message = $message;
            $entity->created_date = \XF::$time;
            $entity->save();

            $report->last_message_date = \XF::$time;
            $report->updated_date = \XF::$time;
            $report->save();

            $this->log($report, $actor, $type === 'note' ? 'internal_note' : 'message', '', $type);
            $db->commit();
        }
        catch (\Throwable $e)
        {
            $db->rollback();
            throw $e;
        }

        if ($type === 'staff')
        {
            $this->alertOwner($report, $actor, 'staff_reply');
        }
    }

    protected function alertOwner(Report $report, User $actor, string $action, array $extra = []): void
    {
        $owner = $report->User ?: $this->app->em()->find('XF:User', (int)$report->user_id);
        if (!$owner || !(int)$owner->user_id || (int)$owner->user_id === (int)$actor->user_id)
        {
            return;
        }

        try
        {
            $extra['depends_on_addon_id'] = 'Warext/HataBildirimi';
            $this->app->repository('XF:UserAlert')->alertFromUser(
                $owner,
                $actor,
                'wrxt_bug_report',
                (int)$report->report_id,
                $action,
                $extra
            );
        }
        catch (\Throwable $e)
        {
            \XF::logException($e, false, 'Warext Hata Bildirimi Uyarı: ');
        }
    }

    protected function log(Report $report, User $actor, string $action, string $oldValue = '', string $newValue = ''): void
    {
        $log = $this->app->em()->create('Warext\HataBildirimi:ReportLog');
        $log->report_id = $report->report_id;
        $log->actor_user_id = $actor->user_id;
        $log->action = $action;
        $log->old_value = mb_substr($oldValue, 0, 255);
        $log->new_value = mb_substr($newValue, 0, 255);
        $log->created_date = \XF::$time;
        $log->save();
    }
}
