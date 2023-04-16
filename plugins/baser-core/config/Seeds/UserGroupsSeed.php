<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * UserGroups seed.
 */
class UserGroupsSeed extends BcSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'id' => '1',
                'name' => 'admins',
                'title' => 'システム管理',
                'auth_prefix' => 'Admin,Api/Admin',
                'auth_prefix_settings' => '',
                'use_move_contents' => '1',
                'modified' => NULL,
                'created' => NULL,
            ],
            [
                'id' => '2',
                'name' => 'operators',
                'title' => 'サイト運営',
                'auth_prefix' => 'Admin',
                'auth_prefix_settings' => '{"Admin":{"type":"2"},"Api/Admin":{"type":"2"}}',
                'use_move_contents' => '0',
                'modified' => NULL,
                'created' => NULL,
            ],
        ];

        $table = $this->table('user_groups');
        $table->insert($data)->save();
    }
}
