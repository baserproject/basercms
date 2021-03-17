<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * UsersUserGroups seed.
 */
class UsersUserGroupsSeed extends AbstractSeed
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
    public function run()
    {
        $data = [
            [
                'id' => '1',
                'user_id' => '1',
                'user_group_id' => '1',
                'created' => '2020-04-01 19:28:31',
                'modified' => '2020-04-01 19:28:31',
            ],
        ];

        $table = $this->table('users_user_groups');
        $table->insert($data)->save();
    }
}
