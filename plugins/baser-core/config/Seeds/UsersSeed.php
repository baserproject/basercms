<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Users seed.
 */
class UsersSeed extends AbstractSeed
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
                'name' => 'basercake3',
                'password' => '$2y$10$x6WQstawmuyS7XrqutyDjOSOLxJp3dv72O73B7lhqzP8XvVlmcx4G',
                'real_name_1' => 'basercake4',
                'real_name_2' => '',
                'email' => 'admin@example.com',
                'nickname' => '',
                'created' => '2017-05-03 14:22:08',
                'modified' => '2020-04-22 10:24:08',
            ],
        ];

        $table = $this->table('users');
        $table->insert($data)->save();
    }
}
