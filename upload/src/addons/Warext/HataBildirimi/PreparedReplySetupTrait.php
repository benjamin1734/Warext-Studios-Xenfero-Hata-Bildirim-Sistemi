<?php

namespace Warext\HataBildirimi;

use XF\Db\Schema\Create;

trait PreparedReplySetupTrait
{
    public function installStep5(): void
    {
        $this->createPreparedReplyTable();
    }

    public function upgrade1000180Step1(): void
    {
        $this->createPreparedReplyTable();
    }

    protected function createPreparedReplyTable(): void
    {
        $this->schemaManager()->createTable('xf_wrxt_bug_prepared_reply', function(Create $table)
        {
            $table->addColumn('prepared_reply_id', 'int')->unsigned()->autoIncrement();
            $table->addColumn('title', 'varchar', 100);
            $table->addColumn('message', 'mediumtext');
            $table->addColumn('display_order', 'int')->unsigned()->setDefault(10);
            $table->addColumn('active', 'tinyint')->unsigned()->setDefault(1);
            $table->addColumn('created_date', 'int')->unsigned();
            $table->addColumn('updated_date', 'int')->unsigned();
            $table->addPrimaryKey('prepared_reply_id');
            $table->addKey(['active', 'display_order']);
        });
    }

    public function uninstallStep4(): void
    {
        $this->schemaManager()->dropTable('xf_wrxt_bug_prepared_reply');
    }
}
