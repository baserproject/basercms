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

namespace BcBlog\Test\Scenario;

use BaserCore\Test\Factory\ContentFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * BlogContentScenario
 *
 * ブログコンテンツを生成する
 * Content / BlogContent を生成する
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BaserCore.Factory/Contents
 * - plugin.BcBlog.Factory/BlogContents
 */
class BlogContentScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args): mixed
    {
        $id = $args[0];
        $siteId = $args[1];
        $parentId = $args[2];
        $name = $args[3];
        $url = $args[4];
        $tile = $args[5]?? '';
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent'])
            ->treeNode($id, $siteId, $parentId, $name, $url, $id, true, $tile)->persist();
        BlogContentFactory::make(['id' => $id, 'description' => 'ディスクリプション', 'template' => 'default'])->persist();
        return null;
    }

}
