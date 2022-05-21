<?php
declare(strict_types=1);

namespace BaserCore\Test\Fixture\Service\SearchIndexesService;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SearchIndexesFixture
 */
class SearchIndexesReconstructFixture extends TestFixture
{
    /**
     * Import
     *
     * @var array
     */
    public $import = ['table' => 'search_indexes'];

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
                'type' => 'ページ',
                'model' => 'Page',
                'model_id' => 1,
                'site_id' => 1,
                'content_id' => 2,
                'content_filter_id' => null,
                'lft' => 2,
                'rght' => 3,
                'title' => 'トップページ',
                'detail' => '',
                'url' => '/index',
                'status' => true,
                'priority' => '0.5',
                'publish_begin' => null,
                'publish_end' => null,
                'created' => '2022-03-27 18:47:18',
                'modified' => '2022-03-27 18:47:18',
            ],
            [
                'id' => 2,
                'type' => 'ページ',
                'model' => 'Page',
                'model_id' => 2,
                'site_id' => 1,
                'content_id' => 3,
                'content_filter_id' => null,
                'lft' => 4,
                'rght' => 5,
                'title' => '会社案内',
                'detail' => '',
                'url' => '/about',
                'status' => true,
                'priority' => '0.5',
                'publish_begin' => null,
                'publish_end' => null,
                'created' => '2022-03-27 18:47:19',
                'modified' => '2022-03-27 18:47:19',
            ],
            [
                'id' => 3,
                'type' => 'ページ',
                'model' => 'Page',
                'model_id' => 3,
                'site_id' => 1,
                'content_id' => 5,
                'content_filter_id' => null,
                'lft' => 7,
                'rght' => 8,
                'title' => 'サービス１',
                'detail' => '',
                'url' => '/service/service1',
                'status' => true,
                'priority' => '0.5',
                'publish_begin' => null,
                'publish_end' => null,
                'created' => '2022-03-27 18:47:19',
                'modified' => '2022-03-27 18:47:19',
            ],
            [
                'id' => 4,
                'type' => 'ページ',
                'model' => 'Page',
                'model_id' => 4,
                'site_id' => 1,
                'content_id' => 6,
                'content_filter_id' => null,
                'lft' => 9,
                'rght' => 10,
                'title' => 'サービス２',
                'detail' => '',
                'url' => '/service/service2',
                'status' => true,
                'priority' => '0.5',
                'publish_begin' => null,
                'publish_end' => null,
                'created' => '2022-03-27 18:47:19',
                'modified' => '2022-03-27 18:47:19',
            ],
        ];
        parent::init();
    }
}
