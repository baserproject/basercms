<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateSearchIndexes extends AbstractMigration
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
        $table = $this->table('search_indexes',['id' => false])
            ->addColumn('id', 'integer', ['null' => false, 'default' => null, 'limit' => 8, 'autoIncrement' => true])
            ->addPrimaryKey(['id'])
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
}
