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

use BaserCore\Test\Scenario\MultiSiteScenario;
use BcBlog\Test\Factory\BlogCategoryFactory;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * MultiSiteScenario
 *
 * マルチサイトのデータセット
 * site とそれに紐づく content / contentFolder を作成する
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
 * - plugin.BcBlog.Factory/BlogContents
 * - plugin.BcBlog.Factory/BlogCategories
 */
class MultiSiteBlogScenario implements FixtureScenarioInterface
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
        $this->loadFixtureScenario(MultiSiteScenario::class);
        $this->createBlogContents();
        $this->createBlogCategories();
    }

    /**
     * ブログコンテンツを作成
     */
    protected function createBlogContents()
    {
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            6,  // id
            1, // siteId
            1, // parentId
            'news', // name
            '/news/' // url
        );
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            7,  // id
            2, // siteId
            2, // parentId
            'news2', // name
            '/s/news2/' // url
        );
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            8,  // id
            3, // siteId
            3, // parentId
            'news3', // name
            '/en/news3/' // url
        );
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            9,  // id
            4, // siteId
            4, // parentId
            'news4', // name
            '/example.com/news4/' // url
        );
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            10,  // id
            5, // siteId
            5, // parentId
            'news5', // name
            '/sub/news5/' // url
        );
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $contentsTable->recover();
    }

    /**
     * ブログカテゴリを作成
     */
    protected function createBlogCategories()
    {
      BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 6,
            'no' => 1,
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => 1,
            'parent_id' => NULL,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 2,
            'blog_content_id' => 6,
            'no' => 2,
            'name' => 'child',
            'title' => '子',
            'status' => 1,
            'parent_id' => 1,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 3,
            'blog_content_id' => 6,
            'no' => 3,
            'name' => 'child-no-parent',
            'title' => '親なし',
            'status' => 1,
            'parent_id' => null,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 4,
            'blog_content_id' => 7,
            'no' => 4,
            'name' => 'smartphone_release',
            'title' => 'スマホサイトリリース',
            'status' => 1,
            'parent_id' => null,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 5,
            'blog_content_id' => 8,
            'no' => 5,
            'name' => 'english_release',
            'title' => '英語サイトリリース',
            'status' => 1,
            'parent_id' => null,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 6,
            'blog_content_id' => 9,
            'no' => 6,
            'name' => 'another_domain_release',
            'title' => '別サイトリリース',
            'status' => 1,
            'parent_id' => null,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 7,
            'blog_content_id' => 10,
            'no' => 7,
            'name' => 'sub_domain_release',
            'title' => '別サイトリリース',
            'status' => 1,
            'parent_id' => null,
        ])->persist();
        $blogCategoriesTable = TableRegistry::getTableLocator()->get('BcBlog.BlogCategories');
        $blogCategoriesTable->recover();
    }

}
