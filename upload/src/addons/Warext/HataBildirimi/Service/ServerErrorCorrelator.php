<?php

namespace Warext\HataBildirimi\Service;

use XF\Service\AbstractService;

class ServerErrorCorrelator extends AbstractService
{
    public function correlate(string $currentUrl, int $userId): ?array
    {
        $options = \XF::options();
        if (isset($options->wrxtHataServerCorrelation) && !$options->wrxtHataServerCorrelation)
        {
            return null;
        }

        $window = isset($options->wrxtHataServerWindow) ? (int)$options->wrxtHataServerWindow : 300;
        $window = max(30, min(1800, $window));
        $since = \XF::$time - $window;
        $until = \XF::$time + 30;

        try
        {
            $rows = $this->app->db()->fetchAll(
                "SELECT error_id, exception_date, user_id, exception_type, message, filename, line, trace_string, request_state
                 FROM xf_error_log
                 WHERE exception_date BETWEEN ? AND ?
                   AND (user_id = ? OR user_id = 0 OR user_id IS NULL)
                 ORDER BY error_id DESC
                 LIMIT 25",
                [$since, $until, $userId]
            );
        }
        catch (\Throwable $e)
        {
            return null;
        }

        if (!$rows)
        {
            return null;
        }

        $matcher = $this->app->service('Warext\\HataBildirimi:ProblemMatcher');
        $currentHash = $matcher->getUrlHash($currentUrl);
        $currentPath = $this->path($currentUrl);
        $best = null;

        foreach ($rows as $row)
        {
            $score = 0;
            $age = abs(\XF::$time - (int)$row['exception_date']);
            $score += max(0, 30 - (int)floor(($age / max(1, $window)) * 30));

            if ((int)($row['user_id'] ?? 0) === $userId && $userId > 0)
            {
                $score += 30;
            }

            $requestUrl = $this->requestUrl($row['request_state'] ?? '');
            if ($requestUrl !== '')
            {
                $requestHash = $matcher->getUrlHash($requestUrl);
                if ($currentHash !== '' && $requestHash === $currentHash)
                {
                    $score += 40;
                }
                elseif ($currentPath !== '' && $this->path($requestUrl) === $currentPath)
                {
                    $score += 25;
                }
            }

            $score = min(100, $score);
            if ($score < 45)
            {
                continue;
            }

            if ($best === null || $score > $best['score'])
            {
                $best = [
                    'error_id' => (int)$row['error_id'],
                    'score' => $score,
                    'summary' => [
                        'date' => (int)$row['exception_date'],
                        'type' => $this->cleanText((string)$row['exception_type'], 100),
                        'message' => $this->cleanText((string)$row['message'], 1500),
                        'file' => $this->cleanFile((string)$row['filename']),
                        'line' => max(0, (int)$row['line']),
                        'trace' => $this->cleanText((string)$row['trace_string'], 3000)
                    ]
                ];
            }
        }

        return $best;
    }

    protected function requestUrl($serialized): string
    {
        if (!is_string($serialized) || $serialized === '')
        {
            return '';
        }

        $state = null;
        try
        {
            $state = @unserialize($serialized, ['allowed_classes' => false]);
        }
        catch (\Throwable $e)
        {
            $state = null;
        }

        if (!is_array($state))
        {
            $decoded = json_decode($serialized, true);
            $state = is_array($decoded) ? $decoded : null;
        }

        if (!is_array($state))
        {
            return '';
        }

        foreach (['url', 'request_uri', 'requestUri', 'REQUEST_URI'] as $key)
        {
            if (!empty($state[$key]) && is_string($state[$key]))
            {
                return mb_substr($state[$key], 0, 2048);
            }
        }

        if (isset($state['server']) && is_array($state['server']))
        {
            foreach (['REQUEST_URI', 'HTTP_REFERER'] as $key)
            {
                if (!empty($state['server'][$key]) && is_string($state['server'][$key]))
                {
                    return mb_substr($state['server'][$key], 0, 2048);
                }
            }
        }

        return '';
    }

    protected function path(string $url): string
    {
        $parts = parse_url($url);
        if ($parts === false)
        {
            return '';
        }

        return isset($parts['path']) ? rtrim((string)$parts['path'], '/') : '';
    }

    protected function cleanText(string $value, int $maxLength): string
    {
        $value = trim($value);
        if ($value === '')
        {
            return '';
        }

        $value = preg_replace(
            '/\b(password|passwd|authorization|bearer|token|csrf|_xfToken|api[_-]?key|access[_-]?token|secret|session|cookie)\b\s*[:=]\s*[^\s,;]+/iu',
            '$1=[redacted]',
            $value
        ) ?? $value;

        $value = preg_replace_callback(
            '~https?://[^\s\)\]\}]+~iu',
            static function(array $match)
            {
                $parts = parse_url($match[0]);
                if ($parts === false)
                {
                    return '';
                }
                $scheme = isset($parts['scheme']) ? strtolower((string)$parts['scheme']) : 'https';
                $host = isset($parts['host']) ? strtolower((string)$parts['host']) : '';
                $port = isset($parts['port']) ? ':' . (int)$parts['port'] : '';
                $path = isset($parts['path']) ? (string)$parts['path'] : '';
                return $host !== '' ? $scheme . '://' . $host . $port . $path : $path;
            },
            $value
        ) ?? $value;

        return mb_substr($value, 0, $maxLength);
    }

    protected function cleanFile(string $file): string
    {
        $file = str_replace('\\', '/', $file);
        $root = str_replace('\\', '/', \XF::getRootDirectory());
        if ($root !== '' && str_starts_with($file, $root))
        {
            $file = ltrim(substr($file, strlen($root)), '/');
        }

        return mb_substr($file, 0, 255);
    }
}
