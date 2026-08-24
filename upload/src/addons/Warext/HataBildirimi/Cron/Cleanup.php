<?php

namespace Warext\HataBildirimi\Cron;

class Cleanup
{
    public static function run(): void
    {
        $db = \XF::db();
        $db->query(
            'DELETE FROM xf_wrxt_bug_ip_rate WHERE updated_date < ?',
            \XF::$time - 86400
        );

        $options = \XF::options();
        $days = isset($options->wrxtHataDiagnosticRetentionDays)
            ? (int)$options->wrxtHataDiagnosticRetentionDays
            : 90;

        if ($days <= 0)
        {
            return;
        }

        $days = min(3650, $days);
        $cutoff = \XF::$time - ($days * 86400);

        $db->query(
            "UPDATE xf_wrxt_bug_report
             SET client_errors = NULL,
                 network_errors = NULL,
                 server_error_summary = NULL
             WHERE created_date < ?
               AND (client_errors IS NOT NULL OR network_errors IS NOT NULL OR server_error_summary IS NOT NULL)",
            $cutoff
        );
    }
}
