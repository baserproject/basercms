<?php
declare(strict_types=1);

namespace BaserCore\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContentsFixture
 */
class ContentsFixture extends TestFixture
{
    /**
     * Import
     *
     * @var array
     */
    public $import = ['table' => 'contents'];

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'name' => '',
                'plugin' => 'Core',
                'type' => 'ContentFolder',
                'entity_id' => 1,
                'url' => '/',
                'site_id' => 0,
                'alias_id' => null,
                'main_site_content_id' => null,
                'parent_id' => null,
                'lft' => 1,
                'rght' => 18,
                'level' => 0,
                'title' => 'ryuring.com',
                'description' => '',
                'eyecatch' => '',
                'author_id' => 1,
                'layout_template' => 'default',
                'status' => true,
                'publish_begin' => null,
                'publish_end' => null,
                'self_status' => true,
                'self_publish_begin' => null,
                'self_publish_end' => null,
                'exclude_search' => false,
                'created_date' => '2017-05-03 14:22:08',
                'modified_date' => '2017-05-03 14:22:08',
                'site_root' => true,
                'deleted_date' => null,
                'deleted' => false,
                'exclude_menu' => false,
                'blank_link' => false,
                'created' => '2016-07-29 18:02:53',
                'modified' => '2017-05-03 14:51:52',
            ],
            [
                'id' => 2,
                'name' => 'index',
                'plugin' => 'Core',
                'type' => 'Page',
                'entity_id' => 1,
                'url' => '/index',
                'site_id' => 0,
                'alias_id' => null,
                'main_site_content_id' => null,
                'parent_id' => 1,
                'lft' => 14,
                'rght' => 15,
                'level' => 1,
                'title' => 'トップページ',
                'description' => '',
                'eyecatch' => '',
                'author_id' => 1,
                'layout_template' => '',
                'status' => true,
                'publish_begin' => null,
                'publish_end' => null,
                'self_status' => true,
                'self_publish_begin' => null,
                'self_publish_end' => null,
                'exclude_search' => false,
                'created_date' => '2017-05-03 14:22:08',
                'modified_date' => '2017-05-03 14:22:08',
                'site_root' => false,
                'deleted_date' => null,
                'deleted' => false,
                'exclude_menu' => false,
                'blank_link' => false,
                'created' => '2016-07-29 18:13:03',
                'modified' => '2017-05-03 15:12:27',
            ],
        ];
        parent::init();
    }
}
