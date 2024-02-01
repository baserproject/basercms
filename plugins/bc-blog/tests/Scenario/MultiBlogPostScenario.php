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
use BcBlog\Test\Factory\BlogPostBlogTagFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Factory\BlogTagFactory;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * MultiBlogPostScenario
 *
 * マルチサイトのデータセット
 * site とそれに紐づく content / contentFolder を作成する
 * content に紐づく blogContent / blogPost を作成する
 * - /
 * - /s/
 * - /en/
 * - /example.com/
 * - /sub/
 *
 */
class MultiBlogPostScenario implements FixtureScenarioInterface
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * load
     */
    public function load(...$args): mixed
    {
        $this->loadFixtureScenario(MultiSiteScenario::class);
        $this->createBlogContents();
        $this->createBlogPosts();
        $this->createBlogTags();
        return null;
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
            'news1', // name
            '/news/', // url,
            'News 1' // title
        );
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            7,  // id
            1, // siteId
            1, // parentId
            'news2', // name
            '/s/news/', // url
            'News 2' // title
        );
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            8,  // id
            1, // siteId
            1, // parentId
            'news3', // name
            '/en/news/', // url
            'News 3' // title
        );
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            9,  // id
            4, // siteId
            1, // parentId
            'news4', // name
            '/example.com/news/', // url
            'News 4' // title
        );
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            10,  // id
            5, // siteId
            1, // parentId
            'news5', // name
            '/sub/', // url
            'News 5' // title
        );
        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $contentsTable->recover();
    }

    /**
     * ブログ記事を作成
     */
    protected function createBlogPosts()
    {
        BlogPostFactory::make([
            'id' => 1,
            'blog_content_id' => 6,
            'no' => 3,
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => 1,
            'posted' => '2016-01-27 12:57:59',
        ])->persist();
        BlogPostFactory::make([
            'id' => 2,
            'blog_content_id' => 6,
            'no' => 4,
            'name' => 'smartphone_release',
            'title' => 'スマホサイトリリース',
            'status' => 1,
            'posted' => '2016-02-10 12:57:59',
        ])->persist();
        BlogPostFactory::make([
            'id' => 3,
            'blog_content_id' => 6,
            'no' => 5,
            'name' => 'english_release',
            'title' => '英語サイトリリース',
            'status' => 1,
            'posted' => '2017-02-10 12:57:59',
        ])->persist();
        BlogPostFactory::make([
            'id' => 4,
            'blog_content_id' => 6,
            'no' => 6,
            'name' => 'another_domain_release',
            'title' => '別サイトリリース',
            'status' => 1,
        ])->persist();
        BlogPostFactory::make([
            'id' => 5,
            'blog_content_id' => 10,
            'no' => 7,
            'name' => 'sub_domain_release',
            'title' => '別サイトリリース',
            'status' => 1,
        ])->persist();
        BlogPostFactory::make([
            'id' => 6,
            'blog_content_id' => 11,
            'no' => 3,
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => 1,
        ])->persist();
        return null;
    }

    protected function createBlogTags()
    {
        BlogTagFactory::make([[
            'id' => 1,
            'name' => '新製品',
        ]])->persist();
        BlogPostBlogTagFactory::make([
            'blog_post_id' => 1,
            'blog_tag_id' => 1
        ])->persist();

        BlogPostBlogTagFactory::make([
            'blog_post_id' => 2,
            'blog_tag_id' => 1
        ])->persist();

        return null;
    }

}
