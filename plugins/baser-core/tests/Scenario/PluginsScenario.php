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

use BaserCore\Test\Factory\PluginFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * PluginsScenario
 *
 */
class PluginsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        PluginFactory::make([
            'id' => 1,
            'name' => 'BcBlog',
            'title' => 'ブログ',
            'version' => '1.0.0',
            'status' => '1',
            'db_init' => '1',
            'priority' => '1',
            'created' => '2021-05-03 10:57:07',
            'modified' => '2021-05-03 10:57:07'
        ])->persist();
        PluginFactory::make([
            'id' => 2,
            'name' => 'BcMail',
            'title' => 'メール',
            'version' => '1.0.0',
            'status' => '1',
            'db_init' => '1',
            'priority' => '2',
            'created' => '2021-05-03 10:57:07',
            'modified' => '2021-05-03 10:57:07'
        ])->persist();
        PluginFactory::make([
            'id' => 3,
            'name' => 'BcUploader',
            'title' => 'アップローダー',
            'version' => '1.0.0',
            'status' => '1',
            'db_init' => '1',
            'priority' => '3',
            'created' => '2021-05-03 10:57:07',
            'modified' => '2021-05-03 10:57:07'
        ])->persist();
        PluginFactory::make([
            'id' => 4,
            'name' => 'BcFavorite',
            'title' => 'お気に入り',
            'version' => '1.0.0',
            'status' => '1',
            'db_init' => '1',
            'priority' => '4',
            'created' => '2021-05-03 10:57:07',
            'modified' => '2021-05-03 10:57:07'
        ])->persist();
        PluginFactory::make([
            'id' => 5,
            'name' => 'BcSearchIndex',
            'title' => 'サイト内検索',
            'version' => '1.0.0',
            'status' => '1',
            'db_init' => '1',
            'priority' => '4',
            'created' => '2021-05-03 10:57:07',
            'modified' => '2021-05-03 10:57:07'
        ])->persist();
    }

}
