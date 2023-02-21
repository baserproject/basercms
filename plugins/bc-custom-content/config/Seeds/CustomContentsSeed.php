<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * CustomContents seed.
 */
class CustomContentsSeed extends AbstractSeed
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
                'custom_table_id' => 8,
                'description' => '採用情報を検索することができます。',
                'template' => 'default',
                'widget_area' => 2,
                'list_count' => 10,
                'list_order' => 'holiday',
                'list_direction' => 'DESC',
                'created' => '2023-01-20 08:57:38',
                'modified' => '2023-02-03 15:55:23',
            ],
        ];

        $table = $this->table('custom_contents');
        $table->insert($data)->save();
    }
}
