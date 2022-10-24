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

use BaserCore\Test\Factory\SiteFactory;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * MultiSiteScenario
 *
 * マルチサイトのデータセット
 * Site とそれに紐づく Content / ContentFolder を作成する
 * - /
 * - /s/
 * - /en/
 * - /example.com/
 * - /sub/
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BaserCore.Factory/Sites
 * - plugin.BaserCore.Factory/Contents
 * - plugin.BaserCore.Factory/ContentFolders
 */
class MultiSiteScenario implements FixtureScenarioInterface
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * load
     */
    public function load(...$args)
    {
        SiteFactory::make()->main()->persist();
        SiteFactory::make()->smartphone(2)->persist();
        SiteFactory::make()->english(3)->persist();
        SiteFactory::make()->anotherDomain(4)->persist();
        SiteFactory::make()->subDomain(5)->persist();
        $this->loadFixtureScenario(
            RootContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            null, // name
            '/' // url
        );
        $this->loadFixtureScenario(
            RootContentScenario::class,
            2,  // id
            2, // siteId
            1, // parentId
            's', // name
            '/s/' // url
        );
        $this->loadFixtureScenario(
            RootContentScenario::class,
            3,  // id
            3, // siteId
            1, // parentId
            'en', // name
            '/en/' // url
        );
        $this->loadFixtureScenario(
            RootContentScenario::class,
            4,  // id
            4, // siteId
            1, // parentId
            'example.com', // name
            '/example.com/' // url
        );
        $this->loadFixtureScenario(
            RootContentScenario::class,
            5,  // id
            5, // siteId
            1, // parentId
            'sub', // name
            '/sub/' // url
        );
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $contentsTable->recover();
    }

}
