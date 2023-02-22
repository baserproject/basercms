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
use BcUploader\Test\Factory\UploaderConfigFactory;
use BcUploader\Test\Factory\UploaderFileFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * UploaderFilesScenario
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BcUploader.Factory/UploaderFiles
 * - plugin.BcUploader.Factory/UploaderCategories
 * - plugin.BcUploader.Factory/UploaderConfigs
 */
class UploaderFilesScenario implements FixtureScenarioInterface
{
    /**
     * load
     */
    public function load(...$args)
    {
        //アップローダープラグインデータを生成
        UploaderConfigFactory::make(['name' => 'large_width', 'value' => 500])->persist();
        UploaderConfigFactory::make(['name' => 'large_height', 'value' => 500])->persist();
        UploaderConfigFactory::make(['name' => 'midium_width', 'value' => 300])->persist();
        UploaderConfigFactory::make(['name' => 'midium_height', 'value' => 300])->persist();
        UploaderConfigFactory::make(['name' => 'small_width', 'value' => 150])->persist();
        UploaderConfigFactory::make(['name' => 'small_height', 'value' => 150])->persist();
        UploaderConfigFactory::make(['name' => 'small_thumb', 'value' => 1])->persist();
        UploaderConfigFactory::make(['name' => 'mobile_large_width', 'value' => 240])->persist();
        UploaderConfigFactory::make(['name' => 'mobile_large_height', 'value' => 240])->persist();
        UploaderConfigFactory::make(['name' => 'mobile_small_width', 'value' => 100])->persist();
        UploaderConfigFactory::make(['name' => 'mobile_small_height', 'value' => 100])->persist();
        UploaderConfigFactory::make(['name' => 'mobile_small_thumb', 'value' => 1])->persist();
        UploaderConfigFactory::make(['name' => 'use_permission', 'value' => 0])->persist();
        UploaderConfigFactory::make(['name' => 'layout_type', 'value' => 'panel'])->persist();

        //アップロードカテゴリデータを生成
        UploaderCategoryFactory::make(['id' => 1, 'name' => 'blog'])->persist();
        UploaderCategoryFactory::make(['id' => 2, 'name' => 'contact'])->persist();

        //アップロードファイルデータを生成
        UploaderFileFactory::make(['id' => 1, 'name' => 'social_new.jpg', 'atl' => 'social_new.jpg', 'uploader_category_id' => 1, 'user_id' => 1])->persist();
        UploaderFileFactory::make(['id' => 2, 'name' => 'widget-hero.jpg', 'atl' => 'widget-hero.jpg', 'uploader_category_id' => 1, 'user_id' => 1])->persist();
        UploaderFileFactory::make(['id' => 3, 'name' => 'logo-48x48_c.png', 'atl' => 'logo-48x48_c.jpg', 'uploader_category_id' => 2, 'user_id' => 1])->persist();
        UploaderFileFactory::make(['id' => 4, 'name' => '2_1.jpg', 'atl' => '2_1.jpg', 'user_id' => 1])->persist();
        UploaderFileFactory::make(['id' => 5, 'name' => '2_2.jpg', 'atl' => '2_2.jpg', 'user_id' => 1])->persist();
        UploaderFileFactory::make(['id' => 6, 'name' => '2_3.jpg', 'atl' => '2_3.jpg', 'user_id' => 1])->persist();
    }

}
