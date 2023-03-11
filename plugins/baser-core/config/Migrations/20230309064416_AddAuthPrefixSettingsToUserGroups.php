<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class AddAuthPrefixSettingsToUserGroups extends BcMigration
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
        $this->table('user_groups')
            ->addColumn('auth_prefix_settings', 'text', [
                'after' => 'auth_prefix',
                'default' => null,
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
        $this->table('user_groups')
            ->removeColumn('auth_prefix_settings')
            ->update();
    }
}
