<?php

namespace Warext\HataBildirimi\Integration;

use XF\App;

class XFMG
{
    public function __construct(protected App $app)
    {
    }

    public function resolve(string $path): ?array
    {
        if (!class_exists('XFMG\Entity\MediaItem'))
        {
            return null;
        }

        if (!preg_match('~(?:^|/)media/(?:[^/?#]*\.)?(\d+)(?:/|$)~i', $path, $match))
        {
            return null;
        }

        $id = (int)$match[1];
        if ($id <= 0)
        {
            return null;
        }

        try
        {
            if (!$this->app->em()->find('XFMG:MediaItem', $id))
            {
                return null;
            }
        }
        catch (\Throwable $e)
        {
            return null;
        }

        return ['route_name' => 'media', 'content_type' => 'xfmg_media', 'content_id' => $id];
    }
}
