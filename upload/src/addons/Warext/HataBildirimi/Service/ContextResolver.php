<?php

namespace Warext\HataBildirimi\Service;

use Warext\HataBildirimi\Integration\XFMG;
use Warext\HataBildirimi\Integration\XFRM;
use XF\Service\AbstractService;

class ContextResolver extends AbstractService
{
    public function resolve(string $url): array
    {
        $path = $this->getPath($url);
        if ($path === '')
        {
            return ['route_name' => '', 'content_type' => '', 'content_id' => 0];
        }

        foreach ([new XFRM($this->app), new XFMG($this->app)] as $integration)
        {
            $resolved = $integration->resolve($path);
            if ($resolved)
            {
                return $resolved;
            }
        }

        $definitions = [
            ['threads', 'thread', '~(?:^|/)threads/(?:[^/?#]*\.)?(\d+)(?:/|$)~i'],
            ['forums', 'node', '~(?:^|/)forums/(?:[^/?#]*\.)?(\d+)(?:/|$)~i'],
            ['members', 'user', '~(?:^|/)members/(?:[^/?#]*\.)?(\d+)(?:/|$)~i']
        ];

        foreach ($definitions as [$route, $type, $pattern])
        {
            if (preg_match($pattern, $path, $match))
            {
                return [
                    'route_name' => $route,
                    'content_type' => $type,
                    'content_id' => (int)$match[1]
                ];
            }
        }

        $segments = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
        return [
            'route_name' => isset($segments[0]) ? mb_substr((string)$segments[0], 0, 100) : '',
            'content_type' => '',
            'content_id' => 0
        ];
    }

    protected function getPath(string $url): string
    {
        $parts = parse_url(trim($url));
        if ($parts === false)
        {
            return '';
        }

        return isset($parts['path']) ? (string)$parts['path'] : '';
    }
}
