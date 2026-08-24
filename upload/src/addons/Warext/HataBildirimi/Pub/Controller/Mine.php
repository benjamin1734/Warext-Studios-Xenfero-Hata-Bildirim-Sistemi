<?php

namespace Warext\HataBildirimi\Pub\Controller;

use XF\Pub\Controller\AbstractController;

class Mine extends AbstractController
{
    public function actionIndex()
    {
        $this->assertRegistrationRequired();
        $visitor = \XF::visitor();

        if (!$visitor->hasPermission('wrxtHata', 'viewOwn'))
        {
            return $this->noPermission();
        }

        $page = max(1, $this->filter('page', 'uint'));
        $perPage = 20;
        $finder = $this->repository('Warext\HataBildirimi:Report')
            ->findReportsForUser((int)$visitor->user_id);
        $total = $finder->total();
        $this->assertValidPage($page, $perPage, $total, 'hata-bildirimlerim');
        $reports = $finder->limitByPage($page, $perPage)->fetch();

        return $this->view(
            'Warext\HataBildirimi:ReportMine',
            'wrxt_hata_report_mine',
            [
                'reports' => $reports,
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total
            ]
        );
    }

    public function actionView()
    {
        $report = $this->assertOwnedReport(['Attachments']);
        $messages = $this->finder('Warext\HataBildirimi:ReportMessage')
            ->where('report_id', $report->report_id)
            ->where('message_type', '<>', 'note')
            ->order('created_date')
            ->fetch();

        return $this->view(
            'Warext\HataBildirimi:ReportView',
            'wrxt_hata_report_view',
            [
                'report' => $report,
                'messages' => $messages,
                'canReply' => $report->status !== 'archived'
            ]
        );
    }

    public function actionReply()
    {
        $this->assertPostOnly();
        $report = $this->assertOwnedReport();

        if ($report->status === 'archived')
        {
            return $this->error('Arşivlenmiş hata bildirimlerine yeni cevap gönderilemez.');
        }

        $message = $this->filter('message', 'str');
        $manager = $this->service('Warext\HataBildirimi:ReportManager');

        try
        {
            if (in_array($report->status, ['resolved', 'cannot_reproduce'], true))
            {
                $manager->changeStatus(\XF::visitor(), $report, 'investigating');
            }
            $manager->addMessage(\XF::visitor(), $report, $message, 'user');
        }
        catch (\InvalidArgumentException $e)
        {
            return $this->error($e->getMessage());
        }

        return $this->redirect($this->buildLink('hata-bildirimlerim/view', null, ['report_id' => $report->report_id]));
    }

    protected function assertOwnedReport(array $with = []): \Warext\HataBildirimi\Entity\Report
    {
        $this->assertRegistrationRequired();
        $visitor = \XF::visitor();

        if (!$visitor->hasPermission('wrxtHata', 'viewOwn'))
        {
            throw $this->exception($this->noPermission());
        }

        $reportId = $this->filter('report_id', 'uint');
        $report = $this->em()->find('Warext\HataBildirimi:Report', $reportId, $with);
        if (!$report)
        {
            throw $this->exception($this->notFound());
        }

        if ((int)$report->user_id !== (int)$visitor->user_id)
        {
            throw $this->exception($this->noPermission());
        }

        return $report;
    }
}
