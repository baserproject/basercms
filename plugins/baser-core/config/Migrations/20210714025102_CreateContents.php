<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class CreateContents extends BcMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        $this->table('contents', [
            'collation' => 'utf8mb4_general_ci'
         ])
            ->addColumn('name', 'text', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('plugin', 'string', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('type', 'string', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('entity_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('url', 'text', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('site_id', 'integer', ['null' => true, 'default' => 0, 'limit' => 8])
            ->addColumn('alias_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('main_site_content_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('parent_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('lft', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('rght', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('level', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('title', 'string', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('eyecatch', 'string', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('author_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('layout_template', 'string', ['null' => true, 'default' => null, 'limit' => 50])
            ->addColumn('status', 'boolean', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('publish_begin', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('publish_end', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('self_status', 'boolean', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('self_publish_begin', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('self_publish_end', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('exclude_search', 'boolean', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('created_date', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('modified_date', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('site_root', 'boolean', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('deleted_date', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('exclude_menu', 'boolean', ['null' => true, 'default' => false, 'limit' => null])
            ->addColumn('blank_link', 'boolean', ['null' => true, 'default' => false, 'limit' => null])
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
        $this->table('contents')->drop()->save();
    }

}
