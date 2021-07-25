<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateContents extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $this->table('contents', ['id' => false])
        ->addColumn('id', 'integer', ['null' => false, 'default' => null, 'limit' => 8, 'autoIncrement' => true])
        ->addPrimaryKey(['id'])
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
        ->addColumn('description', 'text', ['null' => true, 'default' => null, 'limit' => null])
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
        ->addColumn('deleted', 'boolean', ['null' => true, 'default' => null, 'limit' => null])
        ->addColumn('exclude_menu', 'boolean', ['null' => true, 'default' => false, 'limit' => null])
        ->addColumn('blank_link', 'boolean', ['null' => true, 'default' => false, 'limit' => null])
        ->addColumn('created', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
        ->addColumn('modified', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
        ->create();
    }
}
