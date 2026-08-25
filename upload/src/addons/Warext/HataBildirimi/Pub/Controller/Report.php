<?php

namespace Warext\HataBildirimi\Pub\Controller;

use XF\Pub\Controller\AbstractController;

class Report extends AbstractController
{
    public function actionIndex()
    {
        $this->assertRegistrationRequired();
        $visitor = \XF::visitor();
        $options = \XF::options();

        if ((isset($options->wrxtHataEnabled) && !$options->wrxtHataEnabled)
            || !$visitor->hasPermission('wrxtHata', 'submit'))
        {
            return $this->noPermission();
        }

        $currentUrl = trim((string)$this->request->getServer('HTTP_REFERER'));
        if ($currentUrl === '')
        {
            $currentUrl = $this->buildLink('canonical:index');
        }

        $attachmentData = null;
        $attachmentsEnabled = !isset($options->wrxtHataAttachments) || (bool)$options->wrxtHataAttachments;
        if ($attachmentsEnabled && $visitor->hasPermission('wrxtHata', 'attach'))
        {
            $attachmentData = $this->repository('XF:Attachment')->getEditorData('wrxt_bug_report', null);
        }

        return $this->view(
            'Warext\\HataBildirimi:ReportForm',
            'wrxt_hata_report_form',
            [
                'currentUrl' => $currentUrl,
                'attachmentData' => $attachmentData
            ]
        );
    }

    public function actionSubmit()
    {
        $this->assertPostOnly();
        $this->assertRegistrationRequired();

        $input = $this->filter([
            'category' => 'str',
            'description' => 'str',
            'current_url' => 'str',
            'referrer_url' => 'str',
            'screen_width' => 'uint',
            'screen_height' => 'uint',
            'viewport_width' => 'uint',
            'viewport_height' => 'uint',
            'pixel_ratio' => 'str',
            'timezone' => 'str',
            'user_agent' => 'str',
            'js_enabled' => 'bool',
            'attachment_hash' => 'str',
            'client_errors_json' => 'str',
            'network_errors_json' => 'str'
        ]);

        if ($input['user_agent'] === '')
        {
            $input['user_agent'] = (string)$this->request->getServer('HTTP_USER_AGENT');
        }
        if ($input['referrer_url'] === '')
        {
            $input['referrer_url'] = (string)$this->request->getServer('HTTP_REFERER');
        }

        try
        {
            $report = $this->service('Warext\\HataBildirimi:CreateReport')->create($input);
        }
        catch (\InvalidArgumentException | \RuntimeException | \LogicException $e)
        {
            return $this->error($e->getMessage());
        }

        return $this->redirect(
            $this->buildLink('hata-bildirimlerim/view', null, ['report_id' => $report->report_id]),
            \XF::phrase('wrxt_hata_report_created', ['key' => $report->report_key])
        );
    }
}
