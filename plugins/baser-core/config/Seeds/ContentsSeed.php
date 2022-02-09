<?php
declare(strict_types=1);

use Migrations\AbstractSeed;

/**
 * Contents seed.
 */
class ContentsSeed extends AbstractSeed
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
                'name' => '',
                'plugin' => 'BaserCore',
                'type' => 'ContentFolder',
                'entity_id' => '1',
                'url' => '/',
                'site_id' => '1',
                'alias_id' => NULL,
                'main_site_content_id' => NULL,
                'parent_id' => NULL,
                'lft' => '1',
                'rght' => '18',
                'level' => '0',
                'title' => 'メインサイト',
                'description' => '',
                'eyecatch' => '',
                'author_id' => '1',
                'layout_template' => 'default',
                'status' => '1',
                'publish_begin' => NULL,
                'publish_end' => NULL,
                'self_status' => '1',
                'self_publish_begin' => NULL,
                'self_publish_end' => NULL,
                'exclude_search' => '0',
                'created_date' => '2017-05-03 14:22:08',
                'modified_date' => '2017-05-03 14:22:08',
                'site_root' => '1',
                'deleted_date' => NULL,
                'exclude_menu' => '0',
                'blank_link' => '0',
                'created' => '2016-07-29 18:02:53',
                'modified' => '2017-05-03 14:51:52',
            ],
            [
                'id' => '2',
                'name' => 'index',
                'plugin' => 'BaserCore',
                'type' => 'Page',
                'entity_id' => '2',
                'url' => '/index',
                'site_id' => '1',
                'alias_id' => NULL,
                'main_site_content_id' => NULL,
                'parent_id' => '1',
                'lft' => '14',
                'rght' => '15',
                'level' => '1',
                'title' => 'トップページ',
                'description' => '',
                'eyecatch' => '',
                'author_id' => '1',
                'layout_template' => '',
                'status' => '1',
                'publish_begin' => NULL,
                'publish_end' => NULL,
                'self_status' => '1',
                'self_publish_begin' => NULL,
                'self_publish_end' => NULL,
                'exclude_search' => '0',
                'created_date' => '2017-05-03 14:22:08',
                'modified_date' => '2017-05-03 14:22:08',
                'site_root' => '0',
                'deleted_date' => NULL,
                'exclude_menu' => '0',
                'blank_link' => '0',
                'created' => '2016-07-29 18:13:03',
                'modified' => '2017-05-03 15:12:27',
            ],
        ];

        $table = $this->table('contents');
        $table->insert($data)->save();
    }
}
