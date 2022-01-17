<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreatePages extends AbstractMigration
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
        $this->table('pages')
                ->addColumn('id', 'integer', ['null' => false, 'default' => null, 'limit' => 8])
                ->addPrimaryKey(['id'])
                ->addColumn('contents', 'text', ['null' => true, 'default' => null, 'limit' => null])
                ->addColumn('draft', 'text', ['null' => true, 'default' => null, 'limit' => null])
                ->addColumn('page_template', 'string', ['null' => true, 'default' => null, 'limit' => null])
                ->addColumn('code', 'text', ['null' => true, 'default' => null, 'limit' => null])
                ->addColumn('modified', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
                ->addColumn('created', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
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

        $this->table('pages')->drop()->save();
    }
}
