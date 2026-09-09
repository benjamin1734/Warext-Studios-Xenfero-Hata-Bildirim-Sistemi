<?php

namespace Warext\HataBildirimi\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class PreparedReplyCategory extends Entity
{
    public static function getStructure(Structure $structure): Structure
    {
        $structure->table = 'xf_wrxt_bug_prepared_reply_category';
        $structure->shortName = 'Warext\HataBildirimi:PreparedReplyCategory';
        $structure->primaryKey = 'prepared_reply_category_id';
        $structure->columns = [
            'prepared_reply_category_id' => ['type' => self::UINT, 'autoIncrement' => true],
            'title' => ['type' => self::STR, 'maxLength' => 100, 'required' => true],
            'display_order' => ['type' => self::UINT, 'default' => 10],
            'created_date' => ['type' => self::UINT, 'default' => \XF::$time],
            'updated_date' => ['type' => self::UINT, 'default' => \XF::$time]
        ];

        return $structure;
    }
}
