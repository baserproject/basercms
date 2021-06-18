<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreatePermissions extends AbstractMigration
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
        $table = $this->table('permissions', ['id' => false]);
        $table->addColumn('id', 'integer', [
            'autoIncrement' => true,
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('no', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('sort', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('user_group_id', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('url', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('auth', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('status', 'integer', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addPrimaryKey([
            'id',
        ]);
        $table->create();
    }
}
