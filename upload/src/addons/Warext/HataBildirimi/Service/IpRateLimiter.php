<?php

namespace Warext\HataBildirimi\Service;

use XF\Service\AbstractService;

class IpRateLimiter extends AbstractService
{
    public function assertCanSubmit(int $limit): void
    {
        $limit = max(0, min(500, $limit));
        if ($limit === 0)
        {
            return;
        }

        $ip = trim((string)$this->app->request()->getIp());
        if ($ip === '')
        {
            return;
        }

        $salt = (string)$this->app->config('globalSalt');
        if ($salt === '')
        {
            return;
        }

        $ipHash = hash_hmac('sha256', $ip, $salt);
        $now = \XF::$time;
        $windowStart = $now - 600;
        $db = $this->db();
        $db->beginTransaction();

        try
        {
            $db->query(
                'INSERT IGNORE INTO xf_wrxt_bug_ip_rate (ip_hash, window_start, report_count, updated_date) VALUES (?, ?, 0, ?)',
                [$ipHash, $now, $now]
            );

            $row = $db->fetchRow(
                'SELECT window_start, report_count FROM xf_wrxt_bug_ip_rate WHERE ip_hash = ? FOR UPDATE',
                $ipHash
            );

            if (!$row || (int)$row['window_start'] <= $windowStart)
            {
                $db->query(
                    'UPDATE xf_wrxt_bug_ip_rate SET window_start = ?, report_count = 1, updated_date = ? WHERE ip_hash = ?',
                    [$now, $now, $ipHash]
                );
            }
            elseif ((int)$row['report_count'] >= $limit)
            {
                throw new \RuntimeException((string)\XF::phrase('wrxt_hata_rate_limit'));
            }
            else
            {
                $db->query(
                    'UPDATE xf_wrxt_bug_ip_rate SET report_count = report_count + 1, updated_date = ? WHERE ip_hash = ?',
                    [$now, $ipHash]
                );
            }

            $db->commit();
        }
        catch (\Throwable $e)
        {
            $db->rollback();
            throw $e;
        }
    }
}
