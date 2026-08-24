<?php

namespace Warext\HataBildirimi\Util;

class ClientInfo
{
    public static function parse(string $userAgent): array
    {
        $browserName = 'Bilinmiyor';
        $browserVersion = '';
        $osName = 'Bilinmiyor';
        $deviceType = 'desktop';

        $browsers = [
            'Edge' => '/Edg\/([0-9.]+)/',
            'Opera' => '/OPR\/([0-9.]+)/',
            'Chrome' => '/Chrome\/([0-9.]+)/',
            'Firefox' => '/Firefox\/([0-9.]+)/',
            'Safari' => '/Version\/([0-9.]+).*Safari/'
        ];

        foreach ($browsers as $name => $pattern)
        {
            if (preg_match($pattern, $userAgent, $match))
            {
                $browserName = $name;
                $browserVersion = $match[1] ?? '';
                break;
            }
        }

        if (stripos($userAgent, 'Windows NT 10.0') !== false)
        {
            $osName = 'Windows 10/11';
        }
        elseif (preg_match('/Android\s+([0-9.]+)/i', $userAgent, $match))
        {
            $osName = 'Android ' . ($match[1] ?? '');
        }
        elseif (preg_match('/(?:iPhone OS|CPU OS)\s+([0-9_]+)/i', $userAgent, $match))
        {
            $osName = 'iOS ' . str_replace('_', '.', $match[1] ?? '');
        }
        elseif (preg_match('/Mac OS X\s+([0-9_]+)/i', $userAgent, $match))
        {
            $osName = 'macOS ' . str_replace('_', '.', $match[1] ?? '');
        }
        elseif (stripos($userAgent, 'Linux') !== false)
        {
            $osName = 'Linux';
        }

        if (preg_match('/iPad|Tablet/i', $userAgent))
        {
            $deviceType = 'tablet';
        }
        elseif (preg_match('/Mobile|Android|iPhone|iPod/i', $userAgent))
        {
            $deviceType = 'mobile';
        }

        return [
            'browser_name' => $browserName,
            'browser_version' => $browserVersion,
            'os_name' => trim($osName),
            'device_type' => $deviceType
        ];
    }
}
