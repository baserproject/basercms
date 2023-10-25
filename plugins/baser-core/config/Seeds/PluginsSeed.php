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
            ],
            [
                'id' => '3',
                'name' => 'BcContentLink',
                'title' => 'BcContentLink',
                'version' => '2.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '3',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '4',
                'name' => 'BcCustomContent',
                'title' => 'BcCustomContent',
                'version' => '2.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '4',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '5',
                'name' => 'BcEditorTemplate',
                'title' => 'BcEditorTemplate',
                'version' => '2.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '5',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '6',
                'name' => 'BcFavorite',
                'title' => 'BcFavorite',
                'version' => '2.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '6',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '7',
                'name' => 'BcInstaller',
                'title' => 'BcInstaller',
                'version' => '2.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '7',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '8',
                'name' => 'BcMail',
                'title' => 'BcMail',
                'version' => '2.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '8',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '9',
                'name' => 'BcSearchIndex',
                'title' => 'BcSearchIndex',
                'version' => '2.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '9',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '10',
                'name' => 'BcThemeConfig',
                'title' => 'BcThemeConfig',
                'version' => '2.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '10',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '11',
                'name' => 'BcThemeFile',
                'title' => 'BcThemeFile',
                'version' => '2.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '11',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '12',
                'name' => 'BcUploader',
                'title' => 'BcUploader',
                'version' => '2.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '12',
                'created' => NULL,
                'modified' => NULL,
            ],
            [
                'id' => '13',
                'name' => 'BcWidgetArea',
                'title' => 'BcWidgetArea',
                'version' => '2.0.0',
                'status' => '1',
                'db_init' => '1',
                'priority' => '13',
                'created' => NULL,
                'modified' => NULL,
            ]
        ];

        $table = $this->table('plugins');
        $table->insert($data)->save();
    }
}
