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

use BcCustomContent\Test\Factory\CustomEntryFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BcCustomContent.Factory/CustomTables
 */
class CustomEntriesScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args): mixed
    {
        CustomEntryFactory::make([
            'id' => 1,
            'custom_table_id' => 1,
            'name' => 'プログラマー',
            'title' => 'Webエンジニア・Webプログラマー',
            'created' => '2023-01-30 07:09:22',
        ])->persist();
        CustomEntryFactory::make([
            'id' => 2,
            'custom_table_id' => 1,
            'name' => 'プログラマー 2',
        ])->persist();
        CustomEntryFactory::make([
            'id' => 3,
            'custom_table_id' => 1,
            'name' => 'プログラマー 3',
        ])->persist();
        return null;
    }
}
