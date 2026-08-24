<?php

namespace Warext\HataBildirimi\Alert;

use Warext\HataBildirimi\Entity\Report as ReportEntity;
use XF\Alert\AbstractHandler;
use XF\Entity\UserAlert;
use XF\Mvc\Entity\Entity;

class Report extends AbstractHandler
{
    public function getEntityWith(): array
    {
        return ['User'];
    }

    public function getOptOutActions(): array
    {
        return ['staff_reply', 'status'];
    }

    public function getOptOutDisplayOrder(): int
    {
        return 940;
    }

    public function canViewContent(Entity $entity, &$error = null): bool
    {
        return $entity instanceof ReportEntity && $entity->canView();
    }

    public function canViewAlert(UserAlert $alert, &$error = null): bool
    {
        $content = $alert->Content;
        return $content instanceof ReportEntity && $content->canView();
    }
}
