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

namespace BcCustomContent\Test\Scenario;

use BaserCore\Test\Factory\ContentFactory;
use BcCustomContent\Test\Factory\CustomContentFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BcCustomContent.Factory/CustomContents
 * - plugin.BaserCore.Factory/Contents
 */
class CustomContentsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        CustomContentFactory::make([
            'id' => 1,
            'custom_table_id' => 1,
            'description' => 'サービステスト',
            'template' => 'template_1',
        ])->persist();
        ContentFactory::make([
            'id' => 1,
            'url' => '/',
            'name' => '',
            'plugin' => 'BcCustomContent',
            'type' => 'CustomContent',
            'site_id' => 1,
            'parent_id' => null,
            'title' => 'サービスタイトル',
            'lft' => 1,
            'rght' => 2,
            'entity_id' => 1,
            'status' => true
        ])->persist();

        CustomContentFactory::make([
            'id' => 2,
            'custom_table_id' => 2,
            'description' => '求人',
            'template' => 'template_2',
        ])->persist();
        ContentFactory::make([
            'id' => 2,
            'url' => '/recruit/',
            'plugin' => 'BcCustomContent',
            'type' => 'CustomContent',
            'site_id' => 1,
            'parent_id' => null,
            'title' => '求人タイトル',
            'lft' => 3,
            'rght' => 4,
            'entity_id' => 2,
            'status' => true
        ])->persist();
    }
}
