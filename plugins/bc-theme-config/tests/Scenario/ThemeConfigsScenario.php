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

namespace BcThemeConfig\Test\Scenario;

use BcThemeConfig\Test\Factory\ThemeConfigFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BcThemeConfig.Factory/ThemeConfigs
 */
class ThemeConfigsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        ThemeConfigFactory::make(['name' => 'color_main', 'value' => '001800'])->persist();
        ThemeConfigFactory::make(['name' => 'color_sub', 'value' => '001800'])->persist();
        ThemeConfigFactory::make(['name' => 'color_link', 'value' => '2B7BB9'])->persist();
        ThemeConfigFactory::make(['name' => 'color_hover', 'value' => '2B7BB9'])->persist();
    }
}
