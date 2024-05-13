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

use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BcCustomContent.Factory/CustomFields
 * - plugin.BaserCore.Factory/CustomLinks
 */
class CustomFieldsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args): mixed
    {
        CustomFieldFactory::make([
            'id' => 1,
            'title' => '求人分類',
            'name' => 'recruit_category',
            'type' => 'BcCcRelated',
        ])->persist();
        CustomLinkFactory::make([
            'id' => 1,
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'name' => 'recruit_category',
            'title' => '求人分類'
        ])->persist();

        CustomFieldFactory::make([
            'id' => 2,
            'title' => 'この仕事の特徴',
            'name' => 'feature',
            'type' => 'group',
        ])->persist();
        CustomLinkFactory::make([
            'id' => 2,
            'custom_table_id' => 1,
            'custom_field_id' => 2,
            'lft' => 1,
            'rght' => 20,
            'name' => 'feature',
            'title' => 'この仕事の特徴',
            'display_front' => 0,
        ])->persist();
        return null;
    }
}
