<?php

namespace Warext\HataBildirimi\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class PreparedReply extends Entity
{
    public static function getStructure(Structure $structure): Structure
    {
        $structure->table = 'xf_wrxt_bug_prepared_reply';
        $structure->shortName = 'Warext\HataBildirimi:PreparedReply';
        $structure->primaryKey = 'prepared_reply_id';
        $structure->columns = [
            'prepared_reply_id' => ['type' => self::UINT, 'autoIncrement' => true],
            'title' => ['type' => self::STR, 'maxLength' => 100, 'required' => true],
            'message' => ['type' => self::STR, 'required' => true],
            'display_order' => ['type' => self::UINT, 'default' => 10],
            'active' => ['type' => self::BOOL, 'default' => true],
            'created_date' => ['type' => self::UINT, 'default' => \XF::$time],
            'updated_date' => ['type' => self::UINT, 'default' => \XF::$time]
        ];

        return $structure;
    }
}
