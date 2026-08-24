<?php

namespace Warext\HataBildirimi;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;
use XF\Job\PermissionRebuild;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function installStep1(): void
    {
        $this->schemaManager()->createTable('xf_wrxt_bug_report', function(Create $table)
        {
            $table->addColumn('report_id', 'int')->unsigned()->autoIncrement();
            $table->addColumn('report_key', 'varchar', 32);
            $table->addColumn('user_id', 'int')->unsigned()->setDefault(0);
            $table->addColumn('username', 'varchar', 50)->setDefault('');
            $table->addColumn('category', 'varchar', 32)->setDefault('other');
            $table->addColumn('description', 'mediumtext');
            $table->addColumn('current_url', 'varchar', 2048)->setDefault('');
            $table->addColumn('url_hash', 'char', 64)->setDefault('');
            $table->addColumn('signature_hash', 'char', 64)->setDefault('');
            $table->addColumn('referrer_url', 'varchar', 2048)->setDefault('');
            $table->addColumn('route_name', 'varchar', 100)->setDefault('');
            $table->addColumn('content_type', 'varchar', 50)->setDefault('');
            $table->addColumn('content_id', 'int')->unsigned()->setDefault(0);
            $table->addColumn('browser_name', 'varchar', 50)->setDefault('');
            $table->addColumn('browser_version', 'varchar', 50)->setDefault('');
            $table->addColumn('os_name', 'varchar', 50)->setDefault('');
            $table->addColumn('device_type', 'varchar', 20)->setDefault('');
            $table->addColumn('user_agent', 'varchar', 512)->setDefault('');
            $table->addColumn('screen_width', 'int')->unsigned()->setDefault(0);
            $table->addColumn('screen_height', 'int')->unsigned()->setDefault(0);
            $table->addColumn('viewport_width', 'int')->unsigned()->setDefault(0);
            $table->addColumn('viewport_height', 'int')->unsigned()->setDefault(0);
            $table->addColumn('pixel_ratio', 'varchar', 16)->setDefault('1');
            $table->addColumn('style_id', 'int')->unsigned()->setDefault(0);
            $table->addColumn('language_id', 'int')->unsigned()->setDefault(0);
            $table->addColumn('timezone', 'varchar', 100)->setDefault('');
            $table->addColumn('js_enabled', 'tinyint')->setDefault(0);
            $table->addColumn('client_errors', 'mediumtext')->nullable();
            $table->addColumn('network_errors', 'mediumtext')->nullable();
            $table->addColumn('js_error_count', 'int')->unsigned()->setDefault(0);
            $table->addColumn('network_error_count', 'int')->unsigned()->setDefault(0);
            $table->addColumn('server_error_id', 'int')->unsigned()->setDefault(0);
            $table->addColumn('server_error_score', 'tinyint')->unsigned()->setDefault(0);
            $table->addColumn('server_error_summary', 'mediumtext')->nullable();
            $table->addColumn('status', 'varchar', 32)->setDefault('new');
            $table->addColumn('assignee_user_id', 'int')->unsigned()->setDefault(0);
            $table->addColumn('possible_duplicate_report_id', 'int')->unsigned()->setDefault(0);
            $table->addColumn('duplicate_report_id', 'int')->unsigned()->setDefault(0);
            $table->addColumn('duplicate_score', 'tinyint')->unsigned()->setDefault(0);
            $table->addColumn('attach_count', 'int')->unsigned()->setDefault(0);
            $table->addColumn('fixed_in_version', 'varchar', 100)->setDefault('');
            $table->addColumn('resolution_note', 'mediumtext')->nullable();
            $table->addColumn('created_date', 'int')->unsigned();
            $table->addColumn('updated_date', 'int')->unsigned()->setDefault(0);
            $table->addColumn('last_message_date', 'int')->unsigned()->setDefault(0);
            $table->addColumn('resolved_date', 'int')->unsigned()->setDefault(0);
            $table->addPrimaryKey('report_id');
            $table->addUniqueKey('report_key');
            $table->addKey(['user_id', 'created_date']);
            $table->addKey(['status', 'last_message_date']);
            $table->addKey(['url_hash', 'created_date']);
            $table->addKey(['signature_hash', 'created_date']);
            $table->addKey(['content_type', 'content_id', 'created_date'], 'content_context');
            $table->addKey('server_error_id');
            $table->addKey('assignee_user_id');
            $table->addKey('possible_duplicate_report_id');
            $table->addKey('duplicate_report_id');
        });
    }

    public function installStep2(): void
    {
        $this->schemaManager()->createTable('xf_wrxt_bug_report_message', function(Create $table)
        {
            $table->addColumn('message_id', 'int')->unsigned()->autoIncrement();
            $table->addColumn('report_id', 'int')->unsigned();
            $table->addColumn('user_id', 'int')->unsigned()->setDefault(0);
            $table->addColumn('username', 'varchar', 50)->setDefault('');
            $table->addColumn('message_type', 'varchar', 16)->setDefault('user');
            $table->addColumn('message', 'mediumtext');
            $table->addColumn('created_date', 'int')->unsigned();
            $table->addPrimaryKey('message_id');
            $table->addKey(['report_id', 'created_date']);
            $table->addKey(['user_id', 'created_date']);
        });
    }

    public function installStep3(): void
    {
        $this->schemaManager()->createTable('xf_wrxt_bug_report_log', function(Create $table)
        {
            $table->addColumn('log_id', 'int')->unsigned()->autoIncrement();
            $table->addColumn('report_id', 'int')->unsigned();
            $table->addColumn('actor_user_id', 'int')->unsigned()->setDefault(0);
            $table->addColumn('action', 'varchar', 50);
            $table->addColumn('old_value', 'varchar', 255)->setDefault('');
            $table->addColumn('new_value', 'varchar', 255)->setDefault('');
            $table->addColumn('created_date', 'int')->unsigned();
            $table->addPrimaryKey('log_id');
            $table->addKey(['report_id', 'created_date']);
            $table->addKey(['actor_user_id', 'created_date']);
        });
    }

    public function installStep4(): void
    {
        $this->createIpRateTable();
    }

    public function upgrade1000030Step1(): void
    {
        $this->schemaManager()->alterTable('xf_wrxt_bug_report', function(Alter $table)
        {
            $table->addColumn('signature_hash', 'char', 64)->setDefault('')->after('url_hash');
            $table->addColumn('possible_duplicate_report_id', 'int')->unsigned()->setDefault(0)->after('assignee_user_id');
            $table->addColumn('duplicate_score', 'tinyint')->unsigned()->setDefault(0)->after('duplicate_report_id');
            $table->addColumn('attach_count', 'int')->unsigned()->setDefault(0)->after('duplicate_score');
            $table->addKey(['signature_hash', 'created_date']);
            $table->addKey('possible_duplicate_report_id');
        });
    }

    public function upgrade1000040Step1(): void
    {
        $this->schemaManager()->alterTable('xf_wrxt_bug_report', function(Alter $table)
        {
            $table->addColumn('client_errors', 'mediumtext')->nullable()->after('js_enabled');
            $table->addColumn('network_errors', 'mediumtext')->nullable()->after('client_errors');
            $table->addColumn('js_error_count', 'int')->unsigned()->setDefault(0)->after('network_errors');
            $table->addColumn('network_error_count', 'int')->unsigned()->setDefault(0)->after('js_error_count');
            $table->addKey(['content_type', 'content_id', 'created_date'], 'content_context');
        });
    }

    public function upgrade1000050Step1(): void
    {
        $this->schemaManager()->alterTable('xf_wrxt_bug_report', function(Alter $table)
        {
            $table->addColumn('server_error_id', 'int')->unsigned()->setDefault(0)->after('network_error_count');
            $table->addColumn('server_error_score', 'tinyint')->unsigned()->setDefault(0)->after('server_error_id');
            $table->addColumn('server_error_summary', 'mediumtext')->nullable()->after('server_error_score');
            $table->addColumn('fixed_in_version', 'varchar', 100)->setDefault('')->after('attach_count');
            $table->addColumn('resolution_note', 'mediumtext')->nullable()->after('fixed_in_version');
            $table->addKey('server_error_id');
        });
    }

    public function upgrade1000070Step1(): void
    {
        $this->createIpRateTable();
    }

    protected function createIpRateTable(): void
    {
        $this->schemaManager()->createTable('xf_wrxt_bug_ip_rate', function(Create $table)
        {
            $table->addColumn('ip_hash', 'char', 64);
            $table->addColumn('window_start', 'int')->unsigned();
            $table->addColumn('report_count', 'int')->unsigned()->setDefault(0);
            $table->addColumn('updated_date', 'int')->unsigned();
            $table->addPrimaryKey('ip_hash');
            $table->addKey('updated_date');
        });
    }

    public function postInstall(array &$stateChanges): void
    {
        $this->applyDefaultPermissions();
    }

    public function postUpgrade($previousVersion, array &$stateChanges): void
    {
        if ((int)$previousVersion < 1000110)
        {
            $this->applyDefaultPermissions();
        }
    }

    protected function applyDefaultPermissions(): void
    {
        $this->applyMissingGroupPermissions(2, ['submit', 'viewOwn', 'attach']);
        $this->applyMissingGroupPermissions(3, ['submit', 'viewOwn', 'attach', 'manage']);
        $this->applyAdminAccountPermissions();
    }

    protected function applyMissingGroupPermissions(int $userGroupId, array $permissionIds): void
    {
        $userGroup = \XF::em()->find('XF:UserGroup', $userGroupId);
        if (!$userGroup)
        {
            return;
        }

        $permissionRepo = \XF::repository('XF:PermissionEntry');
        $existing = $permissionRepo->getGlobalUserGroupPermissionEntries($userGroupId);
        $configured = $existing['wrxtHata'] ?? [];
        $values = [];

        foreach ($permissionIds as $permissionId)
        {
            if (!array_key_exists($permissionId, $configured))
            {
                $values[$permissionId] = 'allow';
            }
        }

        if (!$values)
        {
            return;
        }

        $service = \XF::service('XF:UpdatePermissions');
        $service->setUserGroup($userGroup);
        $service->setGlobal();
        $service->updatePermissions(['wrxtHata' => $values]);
    }

    protected function applyAdminAccountPermissions(): void
    {
        $db = $this->app->db();
        $adminUserIds = array_map('intval', $db->fetchAllColumn('SELECT user_id FROM xf_admin'));
        $permissionRepo = \XF::repository('XF:PermissionEntry');

        foreach ($adminUserIds as $userId)
        {
            $user = \XF::em()->find('XF:User', $userId);
            if (!$user)
            {
                continue;
            }

            $existing = $permissionRepo->getGlobalUserPermissionEntries($userId);
            $configured = $existing['wrxtHata'] ?? [];
            $values = [];

            foreach (['submit', 'viewOwn', 'attach', 'manage'] as $permissionId)
            {
                if (!array_key_exists($permissionId, $configured))
                {
                    $values[$permissionId] = 'allow';
                }
            }

            if ($values)
            {
                $service = \XF::service('XF:UpdatePermissions');
                $service->setUser($user);
                $service->setGlobal();
                $service->updatePermissions(['wrxtHata' => $values]);
            }
        }

        $db->query("INSERT IGNORE INTO xf_admin_permission_entry (user_id, admin_permission_id)
            SELECT user_id, 'wrxtHataManage' FROM xf_admin");

        \XF::repository('XF:AdminPermission')->rebuildAdminPermissionCache();
    }

    protected function removeInstalledPermissions(): void
    {
        $db = $this->app->db();
        $db->delete('xf_permission_entry', 'permission_group_id = ?', 'wrxtHata');
        $db->delete('xf_admin_permission_entry', 'admin_permission_id = ?', 'wrxtHataManage');

        if ($this->app->container()->isCached('permission.builder'))
        {
            $this->app->permissionBuilder()->refreshData();
        }

        $this->app->jobManager()->enqueueUnique('permissionRebuild', PermissionRebuild::class);
        \XF::repository('XF:AdminPermission')->rebuildAdminPermissionCache();
    }

    public function uninstallStep1(): void
    {
        $lastReportId = 0;
        $alertRepo = \XF::repository('XF:UserAlert');

        while (true)
        {
            $reportIds = $this->app->db()->fetchAllColumn(
                'SELECT report_id FROM xf_wrxt_bug_report WHERE report_id > ? ORDER BY report_id LIMIT 500',
                $lastReportId
            );
            if (!$reportIds)
            {
                break;
            }

            foreach ($reportIds as $reportId)
            {
                $reportId = (int)$reportId;
                $alertRepo->fastDeleteAlertsForContent('wrxt_bug_report', $reportId);
                $lastReportId = $reportId;
            }
        }

        while (true)
        {
            $attachments = \XF::finder('XF:Attachment')
                ->where('content_type', 'wrxt_bug_report')
                ->limit(250)
                ->fetch();

            if (!$attachments->count())
            {
                break;
            }

            foreach ($attachments as $attachment)
            {
                $attachment->delete();
            }
        }
    }

    public function uninstallStep2(): void
    {
        $this->removeInstalledPermissions();
    }

    public function uninstallStep3(): void
    {
        $this->schemaManager()->dropTable('xf_wrxt_bug_ip_rate');
        $this->schemaManager()->dropTable('xf_wrxt_bug_report_log');
        $this->schemaManager()->dropTable('xf_wrxt_bug_report_message');
        $this->schemaManager()->dropTable('xf_wrxt_bug_report');
    }
}
