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
use BaserCore\Test\Factory\ContentFolderFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * RootContentScenario
 *
 * サイトのルートに配置するコンテンツを生成
 * Content / ContentFolder を作成する
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BaserCore.Factory/Contents
 * - plugin.BaserCore.Factory/ContentFolders
 */
class RootContentScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        $id = $args[0];
        $siteId = $args[1];
        $parentId = $args[2];
        $name = $args[3];
        $url = $args[4];
        ContentFactory::make(['plugin' => 'BaserCore', 'type' => 'ContentFolder'])
            ->treeNode($id, $siteId, $parentId, $name, $url, $id, true)->persist();
        ContentFolderFactory::make(['id' => $id])->persist();
    }

}
