<?php

namespace Warext\HataBildirimi\Service;

use Warext\HataBildirimi\Util\ClientInfo;
use XF\Service\AbstractService;

class CreateReport extends AbstractService
{
    public function create(array $input): \Warext\HataBildirimi\Entity\Report
    {
        $visitor = \XF::visitor();
        $options = \XF::options();
        $repo = $this->app->repository('Warext\\HataBildirimi:Report');

        if ((isset($options->wrxtHataEnabled) && !$options->wrxtHataEnabled)
            || !$visitor->hasPermission('wrxtHata', 'submit'))
        {
            throw new \LogicException((string)\XF::phrase('wrxt_hata_no_permission'));
        }

        $limit = isset($options->wrxtHataMaxReports10Min) ? (int)$options->wrxtHataMaxReports10Min : 3;
        $limit = max(1, min(50, $limit));
        if ($repo->countRecentReportsForUser((int)$visitor->user_id, \XF::$time - 600) >= $limit)
        {
            throw new \RuntimeException((string)\XF::phrase('wrxt_hata_rate_limit'));
        }

        $description = trim((string)($input['description'] ?? ''));
        if (mb_strlen($description) < 10)
        {
            throw new \InvalidArgumentException((string)\XF::phrase('wrxt_hata_description_short'));
        }
        $description = mb_substr($description, 0, 5000);

        $diagnosticService = $this->app->service('Warext\\HataBildirimi:ClientDiagnostics');
        $currentUrl = $diagnosticService->sanitizePageUrl((string)($input['current_url'] ?? ''));
        $referrerUrl = $diagnosticService->sanitizePageUrl((string)($input['referrer_url'] ?? ''));
        $userAgent = mb_substr(trim((string)($input['user_agent'] ?? '')), 0, 512);
        $client = ClientInfo::parse($userAgent);
        $category = (string)($input['category'] ?? 'other');
        if (!in_array($category, ['page', 'visual', 'feature', 'mobile', 'performance', 'permission', 'other'], true))
        {
            $category = 'other';
        }

        $ipLimit = isset($options->wrxtHataMaxReportsIp10Min) ? (int)$options->wrxtHataMaxReportsIp10Min : 20;
        $this->app->service('Warext\\HataBildirimi:IpRateLimiter')->assertCanSubmit($ipLimit);

        $matcher = $this->app->service('Warext\\HataBildirimi:ProblemMatcher');
        $urlHash = $matcher->getUrlHash($currentUrl);
        $signatureHash = $matcher->getSignatureHash($currentUrl, $category, $description);
        $duplicateEnabled = !isset($options->wrxtHataDuplicateDetection) || (bool)$options->wrxtHataDuplicateDetection;
        $candidate = $duplicateEnabled
            ? $matcher->findCandidate($urlHash, $signatureHash, $category, $description)
            : null;

        $context = $this->app->service('Warext\\HataBildirimi:ContextResolver')->resolve($currentUrl);

        $clientDiagnosticsEnabled = !isset($options->wrxtHataClientDiagnostics) || (bool)$options->wrxtHataClientDiagnostics;
        $diagnostics = $clientDiagnosticsEnabled
            ? $diagnosticService->sanitize(
                (string)($input['client_errors_json'] ?? ''),
                (string)($input['network_errors_json'] ?? '')
            )
            : ['client_errors' => null, 'network_errors' => null, 'js_error_count' => 0, 'network_error_count' => 0];

        $serverError = $this->app->service('Warext\\HataBildirimi:ServerErrorCorrelator')
            ->correlate($currentUrl, (int)$visitor->user_id);

        $db = $this->app->db();
        $db->beginTransaction();

        try
        {
            $report = $this->app->em()->create('Warext\\HataBildirimi:Report');
            $report->report_key = $repo->generateReportKey();
            $report->user_id = (int)$visitor->user_id;
            $report->username = (string)$visitor->username;
            $report->category = $category;
            $report->description = $description;
            $report->current_url = $currentUrl;
            $report->url_hash = $urlHash;
            $report->signature_hash = $signatureHash;
            $report->referrer_url = $referrerUrl;
            $report->route_name = mb_substr((string)$context['route_name'], 0, 100);
            $report->content_type = mb_substr((string)$context['content_type'], 0, 50);
            $report->content_id = max(0, (int)$context['content_id']);
            $report->browser_name = $client['browser_name'];
            $report->browser_version = $client['browser_version'];
            $report->os_name = $client['os_name'];
            $report->device_type = $client['device_type'];
            $report->user_agent = $userAgent;
            $report->screen_width = max(0, min(20000, (int)($input['screen_width'] ?? 0)));
            $report->screen_height = max(0, min(20000, (int)($input['screen_height'] ?? 0)));
            $report->viewport_width = max(0, min(20000, (int)($input['viewport_width'] ?? 0)));
            $report->viewport_height = max(0, min(20000, (int)($input['viewport_height'] ?? 0)));
            $report->pixel_ratio = mb_substr((string)($input['pixel_ratio'] ?? '1'), 0, 16);
            $report->style_id = (int)$visitor->style_id;
            $report->language_id = (int)$visitor->language_id;
            $report->timezone = mb_substr((string)($input['timezone'] ?? ''), 0, 100);
            $report->js_enabled = !empty($input['js_enabled']);
            $report->client_errors = $diagnostics['client_errors'];
            $report->network_errors = $diagnostics['network_errors'];
            $report->js_error_count = (int)$diagnostics['js_error_count'];
            $report->network_error_count = (int)$diagnostics['network_error_count'];
            $report->server_error_id = $serverError ? (int)$serverError['error_id'] : 0;
            $report->server_error_score = $serverError ? (int)$serverError['score'] : 0;
            $report->server_error_summary = $serverError
                ? json_encode($serverError['summary'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : null;
            $report->status = 'new';
            $report->possible_duplicate_report_id = $candidate ? (int)$candidate['report']->report_id : 0;
            $report->duplicate_score = $candidate ? (int)$candidate['score'] : 0;
            $report->created_date = \XF::$time;
            $report->updated_date = \XF::$time;
            $report->last_message_date = \XF::$time;
            $report->save();

            $message = $this->app->em()->create('Warext\\HataBildirimi:ReportMessage');
            $message->report_id = (int)$report->report_id;
            $message->user_id = (int)$visitor->user_id;
            $message->username = (string)$visitor->username;
            $message->message_type = 'user';
            $message->message = $description;
            $message->created_date = \XF::$time;
            $message->save();

            if ($candidate)
            {
                $this->createSystemLog($report, 'possible_duplicate', (string)$candidate['report']->report_id . ':' . (int)$candidate['score']);
            }
            if ($report->js_error_count || $report->network_error_count)
            {
                $this->createSystemLog($report, 'diagnostics_attached', 'js:' . $report->js_error_count . ',network:' . $report->network_error_count);
            }
            if ($serverError)
            {
                $this->createSystemLog($report, 'server_error_correlated', (string)$serverError['error_id'] . ':' . (int)$serverError['score']);
            }

            $db->commit();
        }
        catch (\Throwable $e)
        {
            $db->rollback();
            throw $e;
        }

        $attachmentsEnabled = !isset($options->wrxtHataAttachments) || (bool)$options->wrxtHataAttachments;
        $attachmentHash = trim((string)($input['attachment_hash'] ?? ''));
        if ($attachmentsEnabled && $attachmentHash !== '' && $visitor->hasPermission('wrxtHata', 'attach'))
        {
            try
            {
                $preparer = $this->app->service('XF:Attachment\\Preparer');
                $associated = $preparer->associateAttachmentsWithContent($attachmentHash, 'wrxt_bug_report', (int)$report->report_id);
                if ($associated)
                {
                    $report->fastUpdate('attach_count', (int)$report->attach_count + $associated);
                }
            }
            catch (\Throwable $e)
            {
                \XF::logException($e, false, 'Warext Hata Bildirimi Ek Dosya: ');
            }
        }

        return $report;
    }

    protected function createSystemLog(\Warext\HataBildirimi\Entity\Report $report, string $action, string $value): void
    {
        $log = $this->app->em()->create('Warext\\HataBildirimi:ReportLog');
        $log->report_id = (int)$report->report_id;
        $log->actor_user_id = 0;
        $log->action = $action;
        $log->old_value = '';
        $log->new_value = mb_substr($value, 0, 255);
        $log->created_date = \XF::$time;
        $log->save();
    }
}
