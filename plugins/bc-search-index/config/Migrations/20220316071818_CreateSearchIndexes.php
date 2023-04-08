<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class CreateSearchIndexes extends BcMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up()
    {
        $this->table('search_indexes', [
            'collation' => 'utf8mb4_general_ci'
         ])
            ->addColumn('type', 'string', ['null' => true, 'default' => null, 'limit' => 100])
            ->addColumn('model', 'string', ['null' => true, 'default' => null, 'limit' => 50])
            ->addColumn('model_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('site_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('content_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('content_filter_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('lft', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('rght', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('title', 'string', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('detail', 'text', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('url', 'text', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('status', 'boolean', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('priority', 'string', ['null' => true, 'default' => null, 'limit' => 3])
            ->addColumn('publish_begin', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('publish_end', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
            ->create();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     * @return void
     */
    public function down()
    {
        $this->table('search_indexes')->drop()->save();
    }

}
