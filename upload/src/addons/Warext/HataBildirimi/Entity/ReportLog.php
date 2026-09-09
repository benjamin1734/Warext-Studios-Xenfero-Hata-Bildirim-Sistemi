<?php

namespace Warext\HataBildirimi\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class ReportLog extends Entity
{
    public static function getStructure(Structure $structure): Structure
    {
        $structure->table = 'xf_wrxt_bug_report_log';
        $structure->shortName = 'Warext\HataBildirimi:ReportLog';
        $structure->primaryKey = 'log_id';
        $structure->columns = [
            'log_id' => ['type' => self::UINT, 'autoIncrement' => true],
            'report_id' => ['type' => self::UINT, 'required' => true],
            'actor_user_id' => ['type' => self::UINT, 'default' => 0],
            'action' => ['type' => self::STR, 'maxLength' => 50, 'required' => true],
            'old_value' => ['type' => self::STR, 'maxLength' => 255, 'default' => ''],
            'new_value' => ['type' => self::STR, 'maxLength' => 255, 'default' => ''],
            'created_date' => ['type' => self::UINT, 'default' => \XF::$time]
        ];
        $structure->relations = [
            'Actor' => [
                'entity' => 'XF:User',
                'type' => self::TO_ONE,
                'conditions' => [['user_id', '=', '$actor_user_id']]
            ]
        ];

        return $structure;
    }
}
