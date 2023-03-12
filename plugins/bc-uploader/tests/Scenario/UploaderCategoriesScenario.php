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

namespace BcUploader\Test\Scenario;

use BcUploader\Test\Factory\UploaderCategoryFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * UploaderCategoriesScenario
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BcUploader.Factory/UploaderCategories
 */
class UploaderCategoriesScenario implements FixtureScenarioInterface
{
    /**
     * load
     */
    public function load(...$args)
    {
        //アップロードカテゴリデータを生成
        UploaderCategoryFactory::make(['id' => 1, 'name' => 'blog'])->persist();
        UploaderCategoryFactory::make(['id' => 2, 'name' => 'contact'])->persist();
        UploaderCategoryFactory::make(['id' => 3, 'name' => 'service'])->persist();
    }

}
