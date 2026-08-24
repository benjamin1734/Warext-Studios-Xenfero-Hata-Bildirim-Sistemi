<?php

namespace Warext\HataBildirimi\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Report extends Repository
{
    public function findReportsForUser(int $userId): Finder
    {
        return $this->finder('Warext\HataBildirimi:Report')
            ->where('user_id', $userId)
            ->order('last_message_date', 'DESC');
    }

    public function countRecentReportsForUser(int $userId, int $since): int
    {
        return $this->finder('Warext\HataBildirimi:Report')
            ->where('user_id', $userId)
            ->where('created_date', '>=', $since)
            ->total();
    }

    public function getReportByKey(string $reportKey): ?\Warext\HataBildirimi\Entity\Report
    {
        return $this->finder('Warext\HataBildirimi:Report')
            ->where('report_key', strtoupper(trim($reportKey)))
            ->fetchOne();
    }

    public function getDuplicateChildren(int $reportId)
    {
        return $this->finder('Warext\HataBildirimi:Report')
            ->where('duplicate_report_id', $reportId)
            ->order('created_date', 'DESC')
            ->fetch();
    }

    public function getHotspots(int $since, int $minimum = 3, int $limit = 10): array
    {
        $minimum = max(2, $minimum);
        $limit = max(1, min(25, $limit));

        return $this->db()->fetchAll(
            "SELECT url_hash, MAX(current_url) AS current_url, COUNT(*) AS report_count,
                    COUNT(DISTINCT user_id) AS user_count, MAX(created_date) AS last_date
             FROM xf_wrxt_bug_report
             WHERE created_date >= ? AND url_hash <> '' AND status NOT IN ('invalid', 'archived')
             GROUP BY url_hash
             HAVING COUNT(*) >= {$minimum}
             ORDER BY report_count DESC, last_date DESC
             LIMIT {$limit}",
            $since
        );
    }

    public function generateReportKey(): string
    {
        do
        {
            $key = 'BUG-' . strtoupper(bin2hex(random_bytes(4)));
            $exists = $this->finder('Warext\HataBildirimi:Report')
                ->where('report_key', $key)
                ->fetchOne();
        }
        while ($exists);

        return $key;
    }
}
