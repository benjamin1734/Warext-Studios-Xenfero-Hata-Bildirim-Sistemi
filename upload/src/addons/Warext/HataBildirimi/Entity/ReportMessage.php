<?php

namespace Warext\HataBildirimi\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class ReportMessage extends Entity
{
    public static function getStructure(Structure $structure): Structure
    {
        $structure->table = 'xf_wrxt_bug_report_message';
        $structure->shortName = 'Warext\HataBildirimi:ReportMessage';
        $structure->primaryKey = 'message_id';
        $structure->columns = [
            'message_id' => ['type' => self::UINT, 'autoIncrement' => true],
            'report_id' => ['type' => self::UINT, 'required' => true],
            'user_id' => ['type' => self::UINT, 'default' => 0],
            'username' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
            'message_type' => ['type' => self::STR, 'maxLength' => 16, 'default' => 'user'],
            'message' => ['type' => self::STR, 'required' => true],
            'created_date' => ['type' => self::UINT, 'default' => \XF::$time]
        ];
        $structure->relations = [
            'Report' => [
                'entity' => 'Warext\HataBildirimi:Report',
                'type' => self::TO_ONE,
                'conditions' => [['report_id', '=', '$report_id']],
                'primary' => true
            ],
            'User' => [
                'entity' => 'XF:User',
                'type' => self::TO_ONE,
                'conditions' => [['user_id', '=', '$user_id']]
            ]
        ];

        return $structure;
    }
}
