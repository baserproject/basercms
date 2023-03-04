<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class CreateBlogCategories extends BcMigration
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
        $this->table('blog_categories')
            ->addColumn('blog_content_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('no', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('name', 'string', ['null' => true, 'default' => null, 'limit' => 50])
            ->addColumn('title', 'string', ['null' => true, 'default' => null, 'limit' => 50])
            ->addColumn('status', 'boolean', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('parent_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('lft', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('rght', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
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
        $this->table('blog_categories')->drop()->save();
    }
}
