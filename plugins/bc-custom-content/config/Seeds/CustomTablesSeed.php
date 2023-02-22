<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * CustomTables seed.
 */
class CustomTablesSeed extends AbstractSeed
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
                'modified' => '2023-02-12 23:31:04',
                'created' => '2023-01-27 14:25:22',
            ],
            [
                'id' => 2,
                'name' => 'occupations',
                'title' => '職種マスタ',
                'type' => '2',
                'display_field' => 'title',
                'has_child' => 1,
                'modified' => '2023-02-03 16:43:34',
                'created' => '2023-01-30 09:29:14',
            ],
        ];

        $table = $this->table('custom_tables');
        $table->insert($data)->save();
    }
}
