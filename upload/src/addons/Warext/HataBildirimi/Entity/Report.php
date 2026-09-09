<?php

namespace Warext\HataBildirimi\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class Report extends Entity
{
    public function canView(): bool
    {
        $visitor = \XF::visitor();
        if (!$visitor->user_id)
        {
            return false;
        }

        return (int)$visitor->user_id === (int)$this->user_id
            || (bool)$visitor->is_admin
            || $visitor->hasPermission('wrxtHata', 'manage');
    }

    public function canViewAttachments(): bool
    {
        return $this->canView();
    }

    public function canUploadAndManageAttachments(): bool
    {
        $visitor = \XF::visitor();
        if (!$visitor->user_id)
        {
            return false;
        }

        if ((bool)$visitor->is_admin || $visitor->hasPermission('wrxtHata', 'manage'))
        {
            return true;
        }

        return (int)$visitor->user_id === (int)$this->user_id
            && $visitor->hasPermission('wrxtHata', 'attach');
    }

    public function getClientErrors(): array
    {
        return $this->decodeJson((string)$this->client_errors);
    }

    public function getNetworkErrors(): array
    {
        return $this->decodeJson((string)$this->network_errors);
    }

    public function getServerErrorSummary(): array
    {
        return $this->decodeJson((string)$this->server_error_summary);
    }

    protected function decodeJson(string $json): array
    {
        if ($json === '')
        {
            return [];
        }

        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }

    public static function getStructure(Structure $structure): Structure
    {
        $structure->table = 'xf_wrxt_bug_report';
        $structure->shortName = 'Warext\\HataBildirimi:Report';
        $structure->contentType = 'wrxt_bug_report';
        $structure->primaryKey = 'report_id';
        $structure->columns = [
            'report_id' => ['type' => self::UINT, 'autoIncrement' => true],
            'report_key' => ['type' => self::STR, 'maxLength' => 32, 'required' => true],
            'user_id' => ['type' => self::UINT, 'default' => 0],
            'username' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
            'category' => ['type' => self::STR, 'maxLength' => 32, 'default' => 'other'],
            'description' => ['type' => self::STR, 'required' => true],
            'current_url' => ['type' => self::STR, 'maxLength' => 2048, 'default' => ''],
            'url_hash' => ['type' => self::STR, 'maxLength' => 64, 'default' => ''],
            'signature_hash' => ['type' => self::STR, 'maxLength' => 64, 'default' => ''],
            'referrer_url' => ['type' => self::STR, 'maxLength' => 2048, 'default' => ''],
            'route_name' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
            'content_type' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
            'content_id' => ['type' => self::UINT, 'default' => 0],
            'browser_name' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
            'browser_version' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
            'os_name' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
            'device_type' => ['type' => self::STR, 'maxLength' => 20, 'default' => ''],
            'user_agent' => ['type' => self::STR, 'maxLength' => 512, 'default' => ''],
            'screen_width' => ['type' => self::UINT, 'default' => 0],
            'screen_height' => ['type' => self::UINT, 'default' => 0],
            'viewport_width' => ['type' => self::UINT, 'default' => 0],
            'viewport_height' => ['type' => self::UINT, 'default' => 0],
            'pixel_ratio' => ['type' => self::STR, 'maxLength' => 16, 'default' => '1'],
            'style_id' => ['type' => self::UINT, 'default' => 0],
            'language_id' => ['type' => self::UINT, 'default' => 0],
            'timezone' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
            'js_enabled' => ['type' => self::BOOL, 'default' => false],
            'client_errors' => ['type' => self::STR, 'nullable' => true, 'default' => null],
            'network_errors' => ['type' => self::STR, 'nullable' => true, 'default' => null],
            'js_error_count' => ['type' => self::UINT, 'default' => 0],
            'network_error_count' => ['type' => self::UINT, 'default' => 0],
            'server_error_id' => ['type' => self::UINT, 'default' => 0],
            'server_error_score' => ['type' => self::UINT, 'default' => 0, 'max' => 100],
            'server_error_summary' => ['type' => self::STR, 'nullable' => true, 'default' => null],
            'status' => ['type' => self::STR, 'maxLength' => 32, 'default' => 'new'],
            'assignee_user_id' => ['type' => self::UINT, 'default' => 0],
            'possible_duplicate_report_id' => ['type' => self::UINT, 'default' => 0],
            'duplicate_report_id' => ['type' => self::UINT, 'default' => 0],
            'duplicate_score' => ['type' => self::UINT, 'default' => 0, 'max' => 100],
            'attach_count' => ['type' => self::UINT, 'default' => 0],
            'fixed_in_version' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
            'resolution_note' => ['type' => self::STR, 'nullable' => true, 'default' => null],
            'created_date' => ['type' => self::UINT, 'default' => \XF::$time],
            'updated_date' => ['type' => self::UINT, 'default' => 0],
            'last_message_date' => ['type' => self::UINT, 'default' => 0],
            'resolved_date' => ['type' => self::UINT, 'default' => 0]
        ];
        $structure->relations = [
            'User' => ['entity' => 'XF:User', 'type' => self::TO_ONE, 'conditions' => [['user_id', '=', '$user_id']], 'primary' => true],
            'Assignee' => ['entity' => 'XF:User', 'type' => self::TO_ONE, 'conditions' => [['user_id', '=', '$assignee_user_id']]],
            'PossibleDuplicate' => ['entity' => 'Warext\\HataBildirimi:Report', 'type' => self::TO_ONE, 'conditions' => [['report_id', '=', '$possible_duplicate_report_id']]],
            'DuplicateReport' => ['entity' => 'Warext\\HataBildirimi:Report', 'type' => self::TO_ONE, 'conditions' => [['report_id', '=', '$duplicate_report_id']]],
            'Messages' => ['entity' => 'Warext\\HataBildirimi:ReportMessage', 'type' => self::TO_MANY, 'conditions' => [['report_id', '=', '$report_id']], 'order' => 'created_date'],
            'Attachments' => ['entity' => 'XF:Attachment', 'type' => self::TO_MANY, 'conditions' => [['content_type', '=', 'wrxt_bug_report'], ['content_id', '=', '$report_id']], 'with' => 'Data', 'order' => 'attach_date']
        ];

        return $structure;
    }
}
