<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class CreateFavorites extends BcMigration
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
        $table = $this->table('favorites', [
            'collation' => 'utf8mb4_general_ci'
         ]);
        $table
            ->addColumn('user_id', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('name', 'string', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('url', 'string', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('sort', 'integer', ['null' => true, 'default' => null, 'limit' => 8])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null, 'limit' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null, 'limit' => null]);
        $table->create();
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
        $this->table('favorites')->drop()->save();
    }

}
