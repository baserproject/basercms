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
    public function run(): void
    {
        $data = [
            [
                'id' => '1',
                'name' => 'admin',
                'password' => '$2y$10$YSYT3O.0QCLCCXrllkzmCeC7rB2H1p.VP/gODnwBLzNsfaurK1SKy',
                'real_name_1' => 'admin',
                'real_name_2' => '',
                'email' => 'admin@example.com',
                'nickname' => '',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
            [
                'id' => '2',
                'name' => 'operator',
                'password' => '$2y$10$YSYT3O.0QCLCCXrllkzmCeC7rB2H1p.VP/gODnwBLzNsfaurK1SKy',
                'real_name_1' => 'operator',
                'real_name_2' => '',
                'email' => 'operator@example.com',
                'nickname' => '',
                'created' => '2022-10-01 09:00:00',
                'modified' => '2022-10-01 09:00:00',
            ],
        ];

        $table = $this->table('users');
        $table->insert($data)->save();
    }
}
