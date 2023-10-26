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

use BaserCore\Test\Factory\PluginFactory;
use BcCustomContent\Test\Factory\CustomTableFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BcCustomContent.Factory/CustomTables
 */
class CustomTablesScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        PluginFactory::make(  [
            'name' => 'BcCustomContent',
            'title' => 'カスタムコンテンツ',
            'version' => '1.0.0',
            'status' => '1',
            'db_init' => '1',
            'priority' => '1',
            'created' => '2021-05-03 10:57:07',
            'modified' => '2021-05-03 10:57:07'
        ]);

        CustomTableFactory::make([
            'id' => 1,
            'type' => 1,
            'name' => 'recruit',
            'title' => '求人情報',
            'display_field' => 'title',
            'has_child' => 0,
            'created' => null,
            'modified' => null
        ])->persist();

        CustomTableFactory::make([
            'id' => 2,
            'type' => 2,
            'name' => 'occupations',
            'title' => '職種マスタ',
            'display_field' => 'title',
            'has_child' => 1,
            'created' => null,
            'modified' => null
        ])->persist();
    }
}
