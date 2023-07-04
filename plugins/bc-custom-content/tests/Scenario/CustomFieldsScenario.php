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
    public function load(...$args)
    {
        CustomFieldFactory::make([
            'id' => 1,
            'title' => '求人分類',
            'name' => 'recruit_category',
            'type' => 'BcCcRelated',
            'status' => 1,
            'default_value' => '新卒採用',
            'validate' => '',
            'regex' => '',
            'regex_error_message' => '',
            'counter' => 0,
            'auto_convert' => '',
            'placeholder' => '',
            'size' => NULL,
            'max_length' => NULL,
            'source' => '',
            'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"","height":"","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":"","max_file_size":"","file_ext":""}}',
            'created' => '2023-01-30 06:22:47',
            'modified' => '2023-02-20 11:18:32',
            'line' => NULL,
        ])->persist();
        CustomLinkFactory::make([
            'id' => 1,
            'no' => NULL,
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'parent_id' => NULL,
            'lft' => 1,
            'rght' => 2,
            'level' => 0,
            'name' => 'recruit_category',
            'title' => '求人分類',
            'group_valid' => 0,
            'created' => '2023-01-30 06:45:08',
            'modified' => '2023-02-12 23:31:04',
            'use_loop' => 0,
            'display_admin_list' => 1,
            'use_api' => 1,
            'search_target_front' => 1,
            'before_linefeed' => 0,
            'after_linefeed' => 0,
            'display_front' => 1,
            'search_target_admin' => 1,
            'description' => NULL,
            'attention' => NULL,
            'before_head' => NULL,
            'after_head' => NULL,
            'options' => NULL,
            'class' => NULL,
            'status' => 1,
            'required' => NULL,
        ])->persist();

        CustomFieldFactory::make([
            'id' => 2,
            'title' => 'この仕事の特徴',
            'name' => 'feature',
            'type' => 'group',
            'status' => 1,
            'default_value' => '',
            'validate' => '',
            'regex' => '',
            'regex_error_message' => '',
            'counter' => 0,
            'auto_convert' => '',
            'placeholder' => '',
            'size' => NULL,
            'max_length' => NULL,
            'source' => '経験者優遇
土日祝日休み
交通費支給
社会保険あり
研修あり
昇給あり
資格取得支援
職場内禁煙',
            'meta' => '{"BcCcAutoZip":{"pref":"","address":""},"BcCcCheckbox":{"label":""},"BcCcRelated":{"custom_table_id":"1","filter_name":"","filter_value":""},"BcCcWysiwyg":{"width":"","height":"","editor_tool_type":"simple"},"BcCustomContent":{"email_confirm":"","max_file_size":"","file_ext":""}}',
            'created' => '2023-01-30 06:23:41',
            'modified' => '2023-02-20 11:21:03',
            'line' => NULL,
        ])->persist();
        CustomLinkFactory::make([
            'id' => 2,
            'no' => NULL,
            'custom_table_id' => 1,
            'custom_field_id' => 2,
            'parent_id' => NULL,
            'lft' => 1,
            'rght' => 2,
            'level' => 0,
            'name' => 'feature',
            'title' => 'この仕事の特徴',
            'group_valid' => 0,
            'created' => '2023-01-30 06:45:08',
            'modified' => '2023-02-12 23:31:04',
            'use_loop' => 0,
            'display_admin_list' => 0,
            'use_api' => 1,
            'search_target_front' => 1,
            'before_linefeed' => 0,
            'after_linefeed' => 0,
            'display_front' => 1,
            'search_target_admin' => 1,
            'description' => NULL,
            'attention' => NULL,
            'before_head' => NULL,
            'after_head' => NULL,
            'options' => NULL,
            'class' => NULL,
            'status' => 1,
            'required' => 1,
        ])->persist();
    }
}
