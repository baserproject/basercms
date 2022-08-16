<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Plugins seed.
 */
class PluginsSeed extends AbstractSeed
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
                'id' => '6',
                'name' => 'BcSpaSample',
                'title' => 'SPAサンプル',
                'version' => NULL,
                'status' => '1',
                'db_init' => '1',
                'priority' => '6',
                'created' => NULL,
                'modified' => '2021-12-09 17:16:53',
            ],
            [
                'id' => '3',
                'name' => 'BcFavorite',
                'title' => 'お気に入り',
                'version' => '1.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '3',
                'created' => '2021-09-16 06:46:11',
                'modified' => '2021-09-16 06:46:11',
            ],
        ];

        $table = $this->table('plugins');
        $table->insert($data)->save();
    }
}
