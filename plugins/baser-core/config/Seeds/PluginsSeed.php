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
                'id' => '1',
                'name' => 'BcSpaSample',
                'title' => 'SPAサンプル',
                'version' => '0.0.1',
                'status' => '0',
                'db_init' => '1',
                'priority' => '1',
                'created' => NULL,
                'modified' => '2022-10-01 09:00:00',
            ]
        ];

        $table = $this->table('plugins');
        $table->insert($data)->save();
    }
}
