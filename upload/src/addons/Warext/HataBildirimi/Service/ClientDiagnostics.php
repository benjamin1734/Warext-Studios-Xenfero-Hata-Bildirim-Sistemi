<?php

namespace Warext\HataBildirimi\Service;

use XF\Service\AbstractService;

class ClientDiagnostics extends AbstractService
{
    public function sanitizePageUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '')
        {
            return '';
        }

        $parts = parse_url($url);
        if ($parts === false)
        {
            return '';
        }

        $scheme = isset($parts['scheme']) ? strtolower((string)$parts['scheme']) : '';
        if ($scheme !== '' && !in_array($scheme, ['http', 'https'], true))
        {
            return '';
        }

        $host = isset($parts['host']) ? strtolower((string)$parts['host']) : '';
        if ($host !== '' && !$this->isAllowedPageHost($host))
        {
            return '';
        }

        $port = isset($parts['port']) ? ':' . (int)$parts['port'] : '';
        $path = isset($parts['path']) ? (string)$parts['path'] : '';
        $query = '';

        if (!empty($parts['query']))
        {
            parse_str((string)$parts['query'], $params);
            foreach ($params as $key => &$value)
            {
                if (preg_match('/password|passwd|token|csrf|auth|secret|session|api[_-]?key|access[_-]?token|_xfToken/i', (string)$key))
                {
                    $value = '[redacted]';
                }
            }
            unset($value);

            if ($params)
            {
                $query = '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
            }
        }

        $prefix = $host !== '' ? (($scheme !== '' ? $scheme . '://' : '') . $host . $port) : '';
        return mb_substr($prefix . $path . $query, 0, 2048);
    }

    public function sanitize(string $clientJson, string $networkJson): array
    {
        $client = $this->sanitizeClientErrors($clientJson);
        $network = $this->sanitizeNetworkErrors($networkJson);

        return [
            'client_errors' => $client ? json_encode($client, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            'network_errors' => $network ? json_encode($network, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            'js_error_count' => count($client),
            'network_error_count' => count($network)
        ];
    }

    protected function isAllowedPageHost(string $host): bool
    {
        $options = \XF::options();
        if (isset($options->wrxtHataBoardUrlsOnly) && !$options->wrxtHataBoardUrlsOnly)
        {
            return true;
        }

        $boardUrl = (string)($options->boardUrl ?? '');
        if ($boardUrl === '')
        {
            return true;
        }

        $boardHost = strtolower((string)(parse_url($boardUrl, PHP_URL_HOST) ?: ''));
        return $boardHost === '' || $host === $boardHost;
    }

    protected function sanitizeClientErrors(string $json): array
    {
        $items = $this->decode($json);
        $clean = [];

        foreach (array_slice($items, -8) as $item)
        {
            if (!is_array($item))
            {
                continue;
            }

            $type = (string)($item['type'] ?? 'error');
            if (!in_array($type, ['error', 'unhandledrejection'], true))
            {
                $type = 'error';
            }

            $message = $this->cleanText((string)($item['message'] ?? ''), 1000);
            if ($message === '')
            {
                continue;
            }

            $clean[] = [
                'type' => $type,
                'message' => $message,
                'source' => $this->cleanUrl((string)($item['source'] ?? '')),
                'line' => max(0, min(10000000, (int)($item['line'] ?? 0))),
                'column' => max(0, min(10000000, (int)($item['column'] ?? 0))),
                'stack' => $this->cleanText((string)($item['stack'] ?? ''), 1500),
                'time' => $this->cleanTime($item['time'] ?? 0)
            ];
        }

        return $clean;
    }

    protected function sanitizeNetworkErrors(string $json): array
    {
        $items = $this->decode($json);
        $clean = [];
        $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

        foreach (array_slice($items, -8) as $item)
        {
            if (!is_array($item))
            {
                continue;
            }

            $method = strtoupper((string)($item['method'] ?? 'GET'));
            if (!in_array($method, $allowedMethods, true))
            {
                $method = 'GET';
            }

            $status = max(0, min(599, (int)($item['status'] ?? 0)));
            if ($status > 0 && $status < 400)
            {
                continue;
            }

            $url = $this->cleanUrl((string)($item['url'] ?? ''));
            if ($url === '')
            {
                continue;
            }

            $clean[] = [
                'transport' => in_array((string)($item['transport'] ?? ''), ['fetch', 'xhr'], true)
                    ? (string)$item['transport']
                    : 'xhr',
                'method' => $method,
                'status' => $status,
                'url' => $url,
                'message' => $this->cleanText((string)($item['message'] ?? ''), 500),
                'time' => $this->cleanTime($item['time'] ?? 0)
            ];
        }

        return $clean;
    }

    protected function decode(string $json): array
    {
        $json = trim($json);
        if ($json === '' || strlen($json) > 50000)
        {
            return [];
        }

        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }

    protected function cleanUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '')
        {
            return '';
        }

        if (strlen($url) > 4096)
        {
            $url = substr($url, 0, 4096);
        }

        $parts = parse_url($url);
        if ($parts === false)
        {
            return '';
        }

        $scheme = isset($parts['scheme']) ? strtolower((string)$parts['scheme']) : '';
        if ($scheme !== '' && !in_array($scheme, ['http', 'https'], true))
        {
            return '';
        }

        $host = isset($parts['host']) ? strtolower((string)$parts['host']) : '';
        $path = isset($parts['path']) ? (string)$parts['path'] : '';
        $port = isset($parts['port']) ? ':' . (int)$parts['port'] : '';

        if ($host !== '')
        {
            return mb_substr(($scheme !== '' ? $scheme . '://' : '') . $host . $port . $path, 0, 2048);
        }

        return mb_substr($path, 0, 2048);
    }

    protected function cleanText(string $value, int $maxLength): string
    {
        $value = trim($value);
        if ($value === '')
        {
            return '';
        }

        $value = preg_replace(
            '/\b(password|passwd|authorization|bearer|token|csrf|_xfToken|api[_-]?key|access[_-]?token|secret|session)\b\s*[:=]\s*[^\s,;]+/iu',
            '$1=[redacted]',
            $value
        ) ?? $value;

        $value = preg_replace_callback(
            '~https?://[^\s\)\]\}]+~iu',
            function(array $match)
            {
                return $this->cleanUrl($match[0]);
            },
            $value
        ) ?? $value;

        return mb_substr($value, 0, $maxLength);
    }

    protected function cleanTime($value): int
    {
        $time = (int)$value;
        if ($time <= 0)
        {
            return 0;
        }

        if ($time > 100000000000)
        {
            $time = (int)floor($time / 1000);
        }

        $minimum = \XF::$time - 86400;
        $maximum = \XF::$time + 300;
        if ($time < $minimum || $time > $maximum)
        {
            return 0;
        }

        return $time;
    }
}
