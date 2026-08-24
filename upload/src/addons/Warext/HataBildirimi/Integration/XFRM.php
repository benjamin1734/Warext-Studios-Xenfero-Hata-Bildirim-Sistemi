<?php

namespace Warext\HataBildirimi\Integration;

use XF\App;

class XFRM
{
    public function __construct(protected App $app)
    {
    }

    public function resolve(string $path): ?array
    {
        if (!class_exists('XFRM\Entity\ResourceItem'))
        {
            return null;
        }

        if (!preg_match('~(?:^|/)resources/(?:[^/?#]*\.)?(\d+)(?:/|$)~i', $path, $match))
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
            if (!$this->app->em()->find('XFRM:ResourceItem', $id))
            {
                return null;
            }
        }
        catch (\Throwable $e)
        {
            return null;
        }

        return ['route_name' => 'resources', 'content_type' => 'xfrm_resource', 'content_id' => $id];
    }
}
