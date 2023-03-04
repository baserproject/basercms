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

namespace BcBlog\Test\TestCase\Service\Admin;

use BaserCore\Service\UsersService;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Service\Admin\BlogPostsAdminService;
use BcBlog\Service\Admin\BlogPostsAdminServiceInterface;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogContentsAdminServiceTest
 */
class BlogPostsAdminServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BcBlog.Factory/BlogPosts',
        'plugin.BcBlog.Factory/BlogContents',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        // データを作成する
        ContentFactory::make([
            'id' => 4,
            'url' => '/index',
            'site_id' => 1,
            'status' => true,
            'entity_id' => 1,
            'plugin' => 'BcBlog',
            'type' => 'BlogContent'
        ])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        $this->loadFixtureScenario(InitAppScenario::class);

        // 対象メーソドを実行する
        /** @var BlogPostsAdminService $service */
        $service = $this->getService(BlogPostsAdminServiceInterface::class);
        $post = ['id' => 1];
        $request = $this->loginAdmin($this->getRequest()->withParam('pass.0', 1));
        $result = $service->getViewVarsForIndex($post, $request);

        // 戻り値の中身を確認する
        $this->assertEquals($post, $result['posts']);
        $this->assertEquals(1, $result['blogContent']->id);
        $this->assertNotEmpty($result['users']);
        $this->assertEquals('https://localhost/', $result['publishLink']);
    }

    /**
     * test getViewVarsForAdd
     */
    public function test_getViewVarsForAdd()
    {
        // データを作成する
        ContentFactory::make([
            'id' => 4,
            'url' => '/index',
            'site_id' => 1,
            'status' => true,
            'entity_id' => 1,
            'plugin' => 'BcBlog',
            'type' => 'BlogContent'
        ])->persist();
        BlogPostFactory::make(['id' => 1])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        $this->loadFixtureScenario(InitAppScenario::class);

        // 対象メーソドを実行する
        /** @var BlogPostsAdminService $service */
        $service = $this->getService(BlogPostsAdminServiceInterface::class);
        $request = $this->getRequest('/baser/admin')->withParam('pass.0', 1);
        $post = BlogPostFactory::get(1);
        /** @var UsersService $userService */
        $userService = $this->getService(UsersServiceInterface::class);
        $user = $userService->get(1);
        $result = $service->getViewVarsForAdd($request, $post, $user);

        // 戻り値の中身を確認する
        $this->assertEquals($post, $result['post']);
        $this->assertEquals(1, $result['blogContent']->id);
        $this->assertArrayHasKey('editor', $result);
        $this->assertArrayHasKey('editorOptions', $result);
        $this->assertArrayHasKey('editorEnterBr', $result);
        $this->assertArrayHasKey('users', $result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('hasNewCategoryAddablePermission', $result);
        $this->assertArrayHasKey('hasNewTagAddablePermission', $result);
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        // データを作成する
        ContentFactory::make([
            'id' => 4,
            'url' => '/index',
            'site_id' => 1,
            'status' => true,
            'entity_id' => 1,
            'plugin' => 'BcBlog',
            'type' => 'BlogContent'
        ])->persist();
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        $this->loadFixtureScenario(InitAppScenario::class);

        // 対象メーソドを実行する
        /** @var BlogPostsAdminService $service */
        $service = $this->getService(BlogPostsAdminServiceInterface::class);
        $request = $this->getRequest('/baser/admin')->withParam('pass.0', 1);
        $post = BlogPostFactory::get(1);
        /** @var UsersService $userService */
        $userService = $this->getService(UsersServiceInterface::class);
        $user = $userService->get(1);
        $result = $service->getViewVarsForEdit($request, $post, $user);

        // 戻り値の中身を確認する
        $this->assertEquals($post, $result['post']);
        $this->assertEquals(1, $result['blogContent']->id);
        $this->assertArrayHasKey('editor', $result);
        $this->assertArrayHasKey('editorOptions', $result);
        $this->assertArrayHasKey('editorEnterBr', $result);
        $this->assertArrayHasKey('users', $result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertArrayHasKey('hasNewCategoryAddablePermission', $result);
        $this->assertArrayHasKey('hasNewTagAddablePermission', $result);
        $this->assertArrayHasKey('publishLink', $result);
    }

    /**
     * test getPublishLink
     */
    public function test_getPublishLink()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getEditorOptions
     */
    public function test_getEditorOptions()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
