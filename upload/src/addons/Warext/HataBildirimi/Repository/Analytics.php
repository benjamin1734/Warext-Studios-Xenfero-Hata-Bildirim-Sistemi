<?php

namespace Warext\HataBildirimi\Repository;

use XF\Mvc\Entity\Repository;

class Analytics extends Repository
{
    public function getDashboard(int $days = 30): array
    {
        $days = max(1, min(365, $days));
        $since = \XF::$time - ($days * 86400);
        $db = $this->db();

        $summary = $db->fetchRow(
            "SELECT
                COUNT(*) AS total,
                SUM(status IN ('new','investigating','confirmed','in_progress')) AS open_count,
                SUM(status = 'resolved') AS resolved_count,
                SUM(status = 'duplicate') AS duplicate_count,
                SUM(js_error_count > 0) AS reports_with_js_errors,
                SUM(network_error_count > 0) AS reports_with_network_errors,
                SUM(js_error_count) AS js_error_count,
                SUM(network_error_count) AS network_error_count
             FROM xf_wrxt_bug_report
             WHERE created_date >= ?",
            $since
        );

        return [
            'days' => $days,
            'since' => $since,
            'summary' => $summary ?: [],
            'daily' => $this->getDaily($since),
            'categories' => $this->getGroupCounts('category', $since, 10),
            'browsers' => $this->getGroupCounts('browser_name', $since, 10),
            'devices' => $this->getGroupCounts('device_type', $since, 10),
            'styles' => $this->getGroupCounts('style_id', $since, 10),
            'contexts' => $this->getGroupCounts('content_type', $since, 10, true),
            'top_pages' => $this->getTopPages($since, 15)
        ];
    }

    protected function getDaily(int $since): array
    {
        return $this->db()->fetchAll(
            "SELECT DATE_FORMAT(FROM_UNIXTIME(created_date), '%Y-%m-%d') AS report_day,
                    COUNT(*) AS report_count,
                    SUM(status = 'resolved') AS resolved_count,
                    SUM(js_error_count) AS js_error_count,
                    SUM(network_error_count) AS network_error_count
             FROM xf_wrxt_bug_report
             WHERE created_date >= ?
             GROUP BY report_day
             ORDER BY report_day ASC",
            $since
        );
    }

    protected function getGroupCounts(string $column, int $since, int $limit, bool $excludeEmpty = false): array
    {
        $allowed = ['category', 'browser_name', 'device_type', 'style_id', 'content_type'];
        if (!in_array($column, $allowed, true))
        {
            throw new \InvalidArgumentException('Geçersiz istatistik alanı.');
        }

        $where = 'created_date >= ?';
        if ($excludeEmpty)
        {
            $where .= " AND {$column} <> ''";
        }

        $limit = max(1, min(50, $limit));

        return $this->db()->fetchAll(
            "SELECT {$column} AS item_key, COUNT(*) AS report_count
             FROM xf_wrxt_bug_report
             WHERE {$where}
             GROUP BY {$column}
             ORDER BY report_count DESC
             LIMIT {$limit}",
            $since
        );
    }

    protected function getTopPages(int $since, int $limit): array
    {
        $limit = max(1, min(50, $limit));

        return $this->db()->fetchAll(
            "SELECT url_hash, MAX(current_url) AS current_url, COUNT(*) AS report_count,
                    COUNT(DISTINCT user_id) AS user_count,
                    SUM(js_error_count) AS js_error_count,
                    SUM(network_error_count) AS network_error_count,
                    MAX(created_date) AS last_date
             FROM xf_wrxt_bug_report
             WHERE created_date >= ? AND url_hash <> ''
             GROUP BY url_hash
             ORDER BY report_count DESC, last_date DESC
             LIMIT {$limit}",
            $since
        );
    }
}
