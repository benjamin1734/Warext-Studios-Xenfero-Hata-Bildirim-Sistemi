<?php

namespace Warext\HataBildirimi\Admin\Controller;

use XF\Admin\Controller\AbstractController;
use XF\Mvc\ParameterBag;

class Report extends AbstractController
{
    protected function preDispatchController($action, ParameterBag $params)
    {
        $this->assertAdminPermission('wrxtHataManage');
    }

    public function actionIndex()
    {
        $page = max(1, $this->filter('page', 'uint'));
        $status = trim($this->filter('status', 'str'));
        $category = trim($this->filter('category', 'str'));
        $search = trim($this->filter('q', 'str'));
        $assigneeUserId = $this->filter('assignee_user_id', 'uint');
        $perPage = 30;

        $finder = $this->finder('Warext\\HataBildirimi:Report')
            ->with(['User', 'Assignee'])
            ->order('updated_date', 'DESC')
            ->order('report_id', 'DESC');

        if ($status !== '') { $finder->where('status', $status); }
        if ($category !== '') { $finder->where('category', $category); }
        if ($assigneeUserId > 0) { $finder->where('assignee_user_id', $assigneeUserId); }

        if ($search !== '')
        {
            if (preg_match('/^BUG-[A-F0-9]{8}$/i', $search))
            {
                $finder->where('report_key', strtoupper($search));
            }
            elseif (ctype_digit($search))
            {
                $finder->where('report_id', (int)$search);
            }
            else
            {
                $searchLike = str_replace(['%', '_'], ['\\%', '\\_'], $search);
                $finder->where('username', 'LIKE', '%' . $searchLike . '%');
            }
        }

        $total = $finder->total();
        $this->assertValidPage($page, $perPage, $total, 'wrxt-hata-bildirimleri');
        $reports = $finder->limitByPage($page, $perPage)->fetch();

        $stats = $this->app->db()->fetchPairs('SELECT status, COUNT(*) FROM xf_wrxt_bug_report GROUP BY status');
        $options = \XF::options();
        $hotspotMin = isset($options->wrxtHataHotspotMin) ? (int)$options->wrxtHataHotspotMin : 3;
        $hotspotMin = max(2, min(25, $hotspotMin));

        return $this->view('Warext\\HataBildirimi:ReportList', 'wrxt_hata_admin_list', [
            'reports' => $reports,
            'stats' => $stats,
            'hotspots' => $this->repository('Warext\\HataBildirimi:Report')->getHotspots(\XF::$time - 1800, $hotspotMin, 10),
            'staff' => $this->getAssignableStaff(),
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'status' => $status,
            'category' => $category,
            'q' => $search,
            'assigneeUserId' => $assigneeUserId
        ]);
    }

    public function actionAnalytics()
    {
        $days = $this->filter('days', 'uint');
        if (!in_array($days, [7, 30, 90], true)) { $days = 30; }

        return $this->view('Warext\\HataBildirimi:Analytics', 'wrxt_hata_admin_analytics', [
            'dashboard' => $this->repository('Warext\\HataBildirimi:Analytics')->getDashboard($days),
            'days' => $days
        ]);
    }

    public function actionView()
    {
        $report = $this->assertReportExists();
        $messages = $this->finder('Warext\\HataBildirimi:ReportMessage')->where('report_id', $report->report_id)->with('User')->order('created_date')->fetch();
        $logs = $this->finder('Warext\\HataBildirimi:ReportLog')->where('report_id', $report->report_id)->order('created_date', 'DESC')->fetch();
        $style = $report->style_id ? $this->em()->find('XF:Style', (int)$report->style_id) : null;
        $styleParent = $style && $style->parent_id ? $this->em()->find('XF:Style', (int)$style->parent_id) : null;
        $language = $report->language_id ? $this->em()->find('XF:Language', (int)$report->language_id) : null;

        return $this->view('Warext\\HataBildirimi:ReportView', 'wrxt_hata_admin_view', [
            'report' => $report,
            'messages' => $messages,
            'logs' => $logs,
            'staff' => $this->getAssignableStaff(),
            'duplicateChildren' => $this->repository('Warext\\HataBildirimi:Report')->getDuplicateChildren((int)$report->report_id),
            'clientErrors' => $report->getClientErrors(),
            'networkErrors' => $report->getNetworkErrors(),
            'serverError' => $report->getServerErrorSummary(),
            'style' => $style,
            'styleParent' => $styleParent,
            'language' => $language
        ]);
    }

    public function actionUpdate()
    {
        $this->assertPostOnly();
        $report = $this->assertReportExists();
        $manager = $this->service('Warext\\HataBildirimi:ReportManager');
        $actor = \XF::visitor();

        try
        {
            $manager->changeStatus($actor, $report, $this->filter('status', 'str'));
            $manager->assign($actor, $report, $this->filter('assignee_user_id', 'uint'));
            $manager->setResolution($actor, $report, $this->filter('fixed_in_version', 'str'), $this->filter('resolution_note', 'str'));
        }
        catch (\InvalidArgumentException $e)
        {
            return $this->error($e->getMessage());
        }

        return $this->redirect($this->viewLink($report));
    }

    public function actionBulk()
    {
        $this->assertPostOnly();
        $ids = $this->filter('report_ids', 'array-uint');
        $ids = array_slice(array_values(array_unique(array_filter(array_map('intval', $ids)))), 0, 100);
        if (!$ids)
        {
            return $this->error('En az bir hata bildirimi seçmelisiniz.');
        }

        $action = $this->filter('bulk_action', 'str');
        if (!in_array($action, ['status', 'assign', 'resolve', 'archive'], true))
        {
            return $this->error('Geçersiz toplu işlem.');
        }

        $status = $this->filter('bulk_status', 'str');
        $assigneeUserId = $this->filter('bulk_assignee_user_id', 'uint');
        $fixedInVersion = $this->filter('fixed_in_version', 'str');
        $resolutionNote = $this->filter('resolution_note', 'str');
        $actor = \XF::visitor();
        $manager = $this->service('Warext\\HataBildirimi:ReportManager');
        $processed = 0;
        $db = $this->app->db();
        $db->beginTransaction();

        try
        {
            foreach ($ids as $id)
            {
                $report = $this->em()->find('Warext\\HataBildirimi:Report', $id);
                if (!$report) { continue; }

                if ($action === 'status')
                {
                    $manager->changeStatus($actor, $report, $status);
                }
                elseif ($action === 'assign')
                {
                    $manager->assign($actor, $report, $assigneeUserId);
                }
                elseif ($action === 'resolve')
                {
                    $manager->changeStatus($actor, $report, 'resolved');
                    $manager->setResolution($actor, $report, $fixedInVersion, $resolutionNote);
                }
                else
                {
                    $manager->changeStatus($actor, $report, 'archived');
                }
                $processed++;
            }
            $db->commit();
        }
        catch (\Throwable $e)
        {
            $db->rollback();
            if ($e instanceof \InvalidArgumentException)
            {
                return $this->error($e->getMessage());
            }
            throw $e;
        }

        return $this->redirect($this->buildLink('wrxt-hata-bildirimleri'), "{$processed} hata bildirimi güncellendi.");
    }

    public function actionDuplicate()
    {
        $this->assertPostOnly();
        $report = $this->assertReportExists();
        $masterReportId = $this->filter('master_report_id', 'uint') ?: (int)$report->possible_duplicate_report_id;

        try
        {
            $this->service('Warext\\HataBildirimi:ReportManager')->markDuplicate(\XF::visitor(), $report, $masterReportId);
        }
        catch (\InvalidArgumentException $e)
        {
            return $this->error($e->getMessage());
        }
        return $this->redirect($this->viewLink($report));
    }

    public function actionDismissDuplicate()
    {
        $this->assertPostOnly();
        $report = $this->assertReportExists();
        $this->service('Warext\\HataBildirimi:ReportManager')->dismissDuplicateCandidate(\XF::visitor(), $report);
        return $this->redirect($this->viewLink($report));
    }

    public function actionReply()
    {
        $this->assertPostOnly();
        $report = $this->assertReportExists();
        try
        {
            $this->service('Warext\\HataBildirimi:ReportManager')->addMessage(\XF::visitor(), $report, $this->filter('message', 'str'), 'staff');
        }
        catch (\InvalidArgumentException $e)
        {
            return $this->error($e->getMessage());
        }
        return $this->redirect($this->viewLink($report));
    }

    public function actionNote()
    {
        $this->assertPostOnly();
        $report = $this->assertReportExists();
        try
        {
            $this->service('Warext\\HataBildirimi:ReportManager')->addMessage(\XF::visitor(), $report, $this->filter('message', 'str'), 'note');
        }
        catch (\InvalidArgumentException $e)
        {
            return $this->error($e->getMessage());
        }
        return $this->redirect($this->viewLink($report));
    }

    protected function getAssignableStaff(): array
    {
        $staff = [];
        foreach ($this->finder('XF:User')->where('is_staff', 1)->order('username')->fetch() as $user)
        {
            if ($user->hasPermission('wrxtHata', 'manage'))
            {
                $staff[$user->user_id] = $user;
            }
        }
        return $staff;
    }

    protected function viewLink(\Warext\HataBildirimi\Entity\Report $report): string
    {
        return $this->buildLink('wrxt-hata-bildirimleri/view', null, ['report_id' => $report->report_id]);
    }

    protected function assertReportExists(): \Warext\HataBildirimi\Entity\Report
    {
        $reportId = $this->filter('report_id', 'uint');
        $report = $this->em()->find('Warext\\HataBildirimi:Report', $reportId, ['User', 'Assignee', 'PossibleDuplicate', 'DuplicateReport', 'Attachments']);
        if (!$report)
        {
            throw $this->exception($this->notFound('Hata bildirimi bulunamadı.'));
        }
        return $report;
    }
}
