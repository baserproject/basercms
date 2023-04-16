<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * Plugins seed.
 */
class PluginsSeed extends BcSeed
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
                'name' => 'BcBlog',
                'title' => 'ブログ',
                'version' => '2.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '1',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '2',
                'name' => 'BcSearchIndex',
                'title' => 'サイト内検索',
                'version' => '2.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '2',
                'created' => NULL,
                'modified' => NULL,
            ]
        ];

        $table = $this->table('plugins');
        $table->insert($data)->save();
    }
}
