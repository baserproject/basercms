<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateFavorites extends AbstractMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('favorites', ['id' => false]);
        $table
            ->addColumn('id', 'integer', ['autoIncrement' => true, 'null' => false, 'default' => null, 'limit' => 8])
            ->addPrimaryKey(['id'])
            ->addColumn('user_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('name', 'string', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('url', 'string', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('sort', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null, 'limit' => null]);
        $table->create();
    }
}
