<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\Scenario;

use BaserCore\Test\Factory\ContentFactory;
use BcSearchIndex\Test\Factory\SearchIndexFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * SearchIndexesSearchScenario
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BaserCore.Factory/SearchIndexes
 */
class SearchIndexesSearchScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        SearchIndexFactory::make([
            'id' => 1,
            'title' => 'test data 1',
            'type' => 'admin',
            'site_id' => 1,
            'status' => true,
        ])->persist();
        SearchIndexFactory::make([
            'id' => 2,
            'title' => 'test data 2',
            'type' => 'admin',
            'site_id' => 1,
            'status' => true,
        ])->persist();
        SearchIndexFactory::make([
            'id' => 3,
            'title' => 'test data 3',
            'priority' => '1',
            'site_id' => 2,
            'status' => true,
        ])->persist();
        SearchIndexFactory::make([
            'id' => 4,
            'title' => 'test data 4',
            'priority' => '2',
            'site_id' => 2,
            'status' => true,
        ])->persist();
        SearchIndexFactory::make([
            'id' => 5,
            'title' => 'test data 5',
            'site_id' => 3,
            'status' => true,
            'modified' => '2022-09-14 21:10:41',
        ])->persist();
        SearchIndexFactory::make([
            'id' => 6,
            'model' => 'Page',
            'title' => 'test data 6',
            'site_id' => 3,
            'status' => true,
            'modified' => '2022-09-15 21:10:41',
        ])->persist();
        SearchIndexFactory::make([
            'id' => 7,
            'type' => 'ページ',
            'model' => 'Page',
            'model_id' => 16,
            'site_id' => 4,
            'content_id' => 2,
            'content_filter_id' => 3,
            'lft' => 3,
            'rght' => 4,
            'title' => '会社案内',
            'detail' => 'baserCMS inc.の会社案内ページ 会社案内会社データ会社名baserCMS inc.  [デモ]設立2009年11月所在地福岡県福岡市博多区博多駅前（ダミー）事業内容インターネットサービス業（ダミー）Webサイト制作事業（ダミー）WEBシステム開発事業（ダミー）アクセスマップ※ JavaScript を有効にしてください。var latlng = new google.maps.LatLng(33.6065756,130.4182970);var options = {zoom: 16,center: latlng,mapTypeId: google.maps.MapTypeId.ROADMAP,navigationControl: true,mapTypeControl: true,scaleControl: true,scrollwheel: false,};var map = new google.maps.Map(document.getElementById("map"), options);var marker = new google.maps.Marker({position: latlng,map: map,title:"baserCMS inc. [デモ]"});var infowindow = new google.maps.InfoWindow({content: "baserCMS inc. [デモ]福岡県""});infowindow.open(map,marker);google.maps.event.addListener(marker, "click", function() {infowindow.open(map,marker);});',
            'url' => '/about',
            'status' => true,
            'priority' => 0.5,
            'created' => '2016-07-21 11:49:19',
            'modified' => NULL,
        ])->persist();
        ContentFactory::make([
            'id' => 1,
            'name' => '',
            'plugin' => 'BaserCore',
            'type' => 'ContentFolder',
            'entity_id' => 1,
            'url' => '/',
            'site_id' => 1,
            'alias_id' => null,
            'main_site_content_id' => null,
            'parent_id' => 0,
            'lft' => 1,
            'rght' => 48,
            'level' => 0,
            'title' => 'baserCMSサンプル',
            'description' => '',
            'eyecatch' => '',
            'author_id' => 1,
            'layout_template' => 'default',
            'status' => true,
            'publish_begin' => null,
            'publish_end' => null,
            'self_status' => true,
            'self_publish_begin' => '2019-06-11 12:27:01',
            'self_publish_end' => null,
            'exclude_search' => false,
            'created_date' => null,
            'modified_date' => '2019-06-11 12:27:01',
            'site_root' => true,
            'deleted_date' => null,
            'exclude_menu' => false,
            'blank_link' => false,
            'created' => '2016-07-29 18:02:53',
            'modified' => '2020-09-14 21:10:41',
        ])->persist();
    }

}
