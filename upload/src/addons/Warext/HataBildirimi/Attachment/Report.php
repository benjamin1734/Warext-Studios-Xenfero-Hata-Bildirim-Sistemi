<?php

namespace Warext\HataBildirimi\Attachment;

use Warext\HataBildirimi\Entity\Report as ReportEntity;
use XF\Attachment\AbstractHandler;
use XF\Entity\Attachment;
use XF\Mvc\Entity\Entity;

class Report extends AbstractHandler
{
    public function canView(Attachment $attachment, Entity $container, &$error = null): bool
    {
        return $container instanceof ReportEntity && $container->canViewAttachments();
    }

    public function canManageAttachments(array $context, &$error = null): bool
    {
        $options = \XF::options();
        if (isset($options->wrxtHataAttachments) && !$options->wrxtHataAttachments)
        {
            return false;
        }

        $visitor = \XF::visitor();
        if (!$visitor->user_id)
        {
            return false;
        }

        if (!empty($context['report_id']))
        {
            $report = \XF::em()->find('Warext\HataBildirimi:Report', (int)$context['report_id']);
            return $report ? $report->canUploadAndManageAttachments() : false;
        }

        return $visitor->hasPermission('wrxtHata', 'submit')
            && $visitor->hasPermission('wrxtHata', 'attach');
    }

    public function onAttachmentDelete(Attachment $attachment, ?Entity $container = null): void
    {
        if ($container instanceof ReportEntity && $container->attach_count > 0)
        {
            $container->fastUpdate('attach_count', max(0, (int)$container->attach_count - 1));
        }
    }

    public function getConstraints(array $context): array
    {
        return \XF::repository('XF:Attachment')->getDefaultAttachmentConstraints();
    }

    public function getContainerIdFromContext(array $context): ?int
    {
        return isset($context['report_id']) ? (int)$context['report_id'] : null;
    }

    public function getContainerLink(Entity $container, array $extraParams = []): string
    {
        return \XF::app()->router('public')->buildLink(
            'hata-bildirimlerim/view',
            null,
            array_merge(['report_id' => $container->report_id], $extraParams)
        );
    }

    public function getContext(?Entity $entity = null, array $extraContext = []): array
    {
        if ($entity instanceof ReportEntity)
        {
            $extraContext['report_id'] = $entity->report_id;
        }

        return $extraContext;
    }
}
