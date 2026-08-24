<?php

namespace Warext\HataBildirimi\Service;

use Warext\HataBildirimi\Entity\Report;
use XF\Service\AbstractService;

class ProblemMatcher extends AbstractService
{
    public function normalizeUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '')
        {
            return '';
        }

        $parts = parse_url($url);
        if ($parts === false)
        {
            return mb_strtolower($url);
        }

        $scheme = isset($parts['scheme']) ? mb_strtolower((string)$parts['scheme']) . '://' : '';
        $host = isset($parts['host']) ? mb_strtolower((string)$parts['host']) : '';
        $port = isset($parts['port']) ? ':' . (int)$parts['port'] : '';
        $path = isset($parts['path']) ? rtrim((string)$parts['path'], '/') : '';
        if ($path === '')
        {
            $path = '/';
        }

        $query = '';
        if (!empty($parts['query']))
        {
            parse_str((string)$parts['query'], $params);
            foreach (array_keys($params) as $key)
            {
                if (preg_match('/^(utm_|fbclid$|gclid$|_xf)/i', (string)$key))
                {
                    unset($params[$key]);
                }
            }
            if ($params)
            {
                ksort($params);
                $query = '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
            }
        }

        return $scheme . $host . $port . $path . $query;
    }

    public function getUrlHash(string $url): string
    {
        $normalized = $this->normalizeUrl($url);
        return $normalized !== '' ? hash('sha256', $normalized) : '';
    }

    public function getSignatureHash(string $url, string $category, string $description): string
    {
        return hash('sha256', $this->normalizeUrl($url) . '|' . $category . '|' . $this->normalizeText($description));
    }

    public function findCandidate(string $urlHash, string $signatureHash, string $category, string $description): ?array
    {
        if ($urlHash === '')
        {
            return null;
        }

        $finder = $this->app->finder('Warext\HataBildirimi:Report')
            ->where('url_hash', $urlHash)
            ->where('category', $category)
            ->where('created_date', '>=', \XF::$time - 2592000)
            ->where('status', '<>', 'invalid')
            ->where('status', '<>', 'archived')
            ->order('report_id', 'DESC')
            ->limit(30);

        $best = null;
        $bestScore = 0;

        foreach ($finder->fetch() as $report)
        {
            if ($signatureHash !== '' && $report->signature_hash !== '' && hash_equals((string)$report->signature_hash, $signatureHash))
            {
                return ['report' => $report, 'score' => 100];
            }

            $score = $this->similarity($description, (string)$report->description);
            if ($score > $bestScore)
            {
                $best = $report;
                $bestScore = $score;
            }
        }

        return $best && $bestScore >= 68
            ? ['report' => $best, 'score' => $bestScore]
            : null;
    }

    public function similarity(string $left, string $right): int
    {
        $leftNormalized = mb_substr($this->normalizeText($left), 0, 700);
        $rightNormalized = mb_substr($this->normalizeText($right), 0, 700);

        if ($leftNormalized === '' || $rightNormalized === '')
        {
            return 0;
        }
        if ($leftNormalized === $rightNormalized)
        {
            return 100;
        }

        similar_text($leftNormalized, $rightNormalized, $textPercent);

        $leftTokens = $this->tokens($leftNormalized);
        $rightTokens = $this->tokens($rightNormalized);
        $union = array_unique(array_merge($leftTokens, $rightTokens));
        $intersection = array_intersect($leftTokens, $rightTokens);
        $tokenPercent = $union ? (count($intersection) / count($union)) * 100 : 0;

        return (int)round(max($textPercent, $tokenPercent));
    }

    protected function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        return trim($text);
    }

    protected function tokens(string $text): array
    {
        $tokens = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $tokens = array_filter($tokens, static fn(string $token): bool => mb_strlen($token, 'UTF-8') >= 3);
        return array_values(array_unique($tokens));
    }
}
