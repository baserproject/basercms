<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * UploaderConfigs seed.
 */
class UploaderConfigsSeed extends AbstractSeed
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
                'name' => 'large_width',
                'value' => '500',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
            [
                'id' => 2,
                'name' => 'large_height',
                'value' => '500',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
            [
                'id' => 3,
                'name' => 'midium_width',
                'value' => '300',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
            [
                'id' => 4,
                'name' => 'midium_height',
                'value' => '300',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
            [
                'id' => 5,
                'name' => 'small_width',
                'value' => '150',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
            [
                'id' => 6,
                'name' => 'small_height',
                'value' => '150',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
            [
                'id' => 7,
                'name' => 'small_thumb',
                'value' => '1',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
            [
                'id' => 8,
                'name' => 'mobile_large_width',
                'value' => '240',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
            [
                'id' => 9,
                'name' => 'mobile_large_height',
                'value' => '240',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
            [
                'id' => 10,
                'name' => 'mobile_small_width',
                'value' => '100',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
            [
                'id' => 11,
                'name' => 'mobile_small_height',
                'value' => '100',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
            [
                'id' => 12,
                'name' => 'mobile_small_thumb',
                'value' => '1',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
            [
                'id' => 13,
                'name' => 'use_permission',
                'value' => '0',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
            [
                'id' => 14,
                'name' => 'layout_type',
                'value' => 'panel',
                'created' => '2016-08-12 00:48:35',
                'modified' => NULL,
            ],
        ];

        $table = $this->table('uploader_configs');
        $table->insert($data)->save();
    }
}
