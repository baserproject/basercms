<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * CustomTables seed.
 */
class CustomTablesSeed extends BcSeed
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
                'id' => 1,
                'name' => 'recruit',
                'title' => '求人情報',
                'type' => '1',
                'display_field' => 'title',
                'has_child' => 0,
                'modified' => NULL,
                'created' => NULL,
            ],
            [
                'id' => 2,
                'name' => 'occupations',
                'title' => '職種マスタ',
                'type' => '2',
                'display_field' => 'title',
                'has_child' => 1,
                'modified' => NULL,
                'created' => NULL,
            ],
        ];

        $table = $this->table('custom_tables');
        $table->insert($data)->save();
    }
}
