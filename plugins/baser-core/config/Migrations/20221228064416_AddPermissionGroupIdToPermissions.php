<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class AddPermissionGroupIdToPermissions extends BcMigration
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
        $this->table('permissions')
            ->addColumn('permission_group_id', 'integer', [
                'after' => 'sort',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->update();
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
        $this->table('permissions')
            ->removeColumn('permission_group_id')
            ->update();
    }
}
