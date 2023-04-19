<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\Controller\Admin;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\Admin\BlogCategoriesController;
use BcBlog\Test\Factory\BlogCategoryFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogCategoriesControllerTest
 *
 * @property  BlogCategoriesController $Controller
 */
class BlogCategoriesControllerTest extends BcTestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BcBlog.Factory/BlogCategories',
        'plugin.BcBlog.Factory/BlogContents',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->Controller = new BlogCategoriesController($this->loginAdmin($this->getRequest()));
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] ブログを一覧表示する
     */
    public function testAdmin_index()
    {
        BlogContentFactory::make(['id' => 1, 'eye_catch_size' => 'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7czozOiIzMDAiO3M6MTI6InRodW1iX2hlaWdodCI7czozOiIzMDAiO3M6MTg6Im1vYmlsZV90aHVtYl93aWR0aCI7czozOiIxMDAiO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO3M6MzoiMTAwIjt9'])->persist();
        ContentFactory::make(['id' => 1, 'entity_id' => 1, 'type' => 'BlogContent', 'plugin' => 'BcBlog'])->persist();
        $this->get('/baser/admin/bc-blog/blog_categories/index');
        $this->assertResponseError();
        $this->get('/baser/admin/bc-blog/blog_categories/index/1');
        $this->assertResponseOk();
    }

    /**
     * test beforeAdd
     */
    public function testBeforeAddEvent()
    {
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcBlog.BlogCategories.beforeAdd', function (Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'Nghiem';
            $event->setData('data', $data);
        });
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = ['name' => 'testName1', 'title' => 'testTitle1'];
        BlogContentFactory::make([
            'id' => '1',
            'description' => 'test',
        ])->persist();
        $blogContentId = 1;
        $this->post("/baser/admin/bc-blog/blog_categories/add/$blogContentId", $data);
        $blogCategories = $this->getTableLocator()->get('BaserCore.BlogCategories');
        $query = $blogCategories->find()->where(['name' => 'Nghiem']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * test afterAdd
     */
    public function testAfterAddEvent()
    {
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcBlog.BlogCategories.afterAdd', function (Event $event) {
            $blogCategory = $event->getData('blogCategory');
            $blogCategories = TableRegistry::getTableLocator()->get('BaserCore.BlogCategories');
            $blogCategory->name = 'etc';
            $blogCategories->save($blogCategory);
        });
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = ['name' => 'testName1', 'title' => 'testTitle1'];
        BlogContentFactory::make([
            'id' => '1',
            'description' => 'test',
        ])->persist();
        $blogContentId = 1;
        $this->post("/baser/admin/bc-blog/blog_categories/add/$blogContentId", $data);
        $blogCategories = $this->getTableLocator()->get('BaserCore.BlogCategories');
        $query = $blogCategories->find()->where(['name' => 'etc']);
        $this->assertEquals(1, $query->count());
    }


    /**
     * [ADMIN] 登録処理
     */
    public function testAdmin_add()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        ContentFactory::make([
            'id' => '1',
            'url' => '/blog/',
            'name' => 'blog',
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'site_id' => 1,
            'parent_id' => 3,
            'lft' => 7,
            'rght' => 8,
            'entity_id' => 1,
            'site_root' => false,
            'status' => true
        ])->persist();
        BlogContentFactory::make([
            'id' => '1',
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
            'template' => 'default',
            'list_count' => '10',
            'list_direction' => 'DESC',
            'feed_count' => '10',
            'tag_use' => '1',
            'comment_use' => '1',
            'comment_approve' => '0',
            'widget_area' => '2',
            'eye_catch_size' => 'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7czozOiIzMDAiO3M6MTI6InRodW1iX2hlaWdodCI7czozOiIzMDAiO3M6MTg6Im1vYmlsZV90aHVtYl93aWR0aCI7czozOiIxMDAiO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO3M6MzoiMTAwIjt9',
            'use_content' => '1',
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
        ])->persist();
        $blogContentId = 1;
        $data = ['name' => 'testName', 'title' => 'testTitle'];
        $this->post("/baser/admin/bc-blog/blog_categories/add/$blogContentId", $data);
        // ステータスを確認
        $this->assertResponseCode(302);
        // データの登録を確認
        $blogCategory = BlogCategoryFactory::get(1);
        $this->assertEquals($data['name'], $blogCategory['name']);
        // 失敗のメッセージを確認
        $data['name'] = 'test name';
        $this->post("/baser/admin/bc-blog/blog_categories/add/$blogContentId", $data);
        $this->assertResponseContains('入力エラーです。内容を修正してください。');
    }

    /**
     * Test beforeEdit method
     *
     * @return void
     */
    public function testBeforeEditEvent(): void
    {
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcBlog.BlogCategories.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'Nghiem';
            $event->setData('data', $data);
        });
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        BlogContentFactory::make([
            'id' => '1',
            'description' => 'test edit',
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'name' => 'release',
            'title' => 'プレスリリース',
        ])->persist();
        $data = ['name' => 'editedName', 'blog_content_id' => '1'];
        $this->put("/baser/admin/bc-blog/blog_categories/edit/1/1", $data);
        $blogCategory = BlogCategoryFactory::get(1);
        $this->assertEquals('Nghiem', $blogCategory['name']);
    }

    /**
     * test afterEdit
     */
    public function testAfterEditEvent()
    {
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcBlog.BlogCategories.afterEdit', function (Event $event) {
            $blogCategory = $event->getData('blogCategory');
            $blogCategories = TableRegistry::getTableLocator()->get('BaserCore.BlogCategories');
            $blogCategory->name = 'Nghiem';
            $blogCategories->save($blogCategory);
        });
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        BlogContentFactory::make([
            'id' => '1',
            'description' => 'test edit',
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'name' => 'release',
            'title' => 'プレスリリース',
        ])->persist();
        $data = ['name' => 'editedName', 'blog_content_id' => '1'];
        $this->put("/baser/admin/bc-blog/blog_categories/edit/1/1", $data);
        $blogCategory = BlogCategoryFactory::get(1);
        $this->assertEquals('Nghiem', $blogCategory['name']);

    }

    /**
     * [ADMIN] 編集処理
     */
    public function testAdmin_edit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        ContentFactory::make([
            'id' => '1',
            'url' => '/blog/',
            'name' => 'blog',
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'site_id' => 1,
            'parent_id' => 3,
            'lft' => 7,
            'rght' => 8,
            'entity_id' => 1,
            'site_root' => false,
            'status' => true
        ])->persist();
        BlogContentFactory::make([
            'id' => '1',
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
            'template' => 'default',
            'list_count' => '10',
            'list_direction' => 'DESC',
            'feed_count' => '10',
            'tag_use' => '1',
            'comment_use' => '1',
            'comment_approve' => '0',
            'widget_area' => '2',
            'eye_catch_size' => 'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7czozOiIzMDAiO3M6MTI6InRodW1iX2hlaWdodCI7czozOiIzMDAiO3M6MTg6Im1vYmlsZV90aHVtYl93aWR0aCI7czozOiIxMDAiO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO3M6MzoiMTAwIjt9',
            'use_content' => '1',
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'no' => 1,
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => 1,
            'lft' => 1,
            'rght' => 2,
        ])->persist();
        $data = ['name' => 'editedName', 'blog_content_id' => '1'];
        $this->put("/baser/admin/bc-blog/blog_categories/edit/1/1", $data);
        // ステータスを確認
        $this->assertResponseCode(302);
        // メッセージを確認
        $this->assertFlashMessage('カテゴリー「editedName」を更新しました。');
        // データの変更を確認
        $blogCategory = BlogCategoryFactory::get(1);
        $this->assertEquals('editedName', $blogCategory['name']);
        // 失敗のメッセージを確認
        $data['name'] = 'edited name';
        $this->put("/baser/admin/bc-blog/blog_categories/edit/1/1", $data);
        $this->assertResponseContains('入力エラーです。内容を修正してください。');
    }

    /**
     * [ADMIN] 削除処理
     */
    public function testAdmin_delete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        BlogCategoryFactory::make([
            'id' => '1',
            'blog_content_id' => '1',
            'no' => '1',
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => '1',
            'parent_id' => null,
            'lft' => '1',
            'rght' => '4',
            'owner_id' => '1',
            'created' => '2015-01-27 12:56:53',
            'modified' => null
        ])->persist();
        $this->delete("/baser/admin/bc-blog/blog_categories/delete/1/1");
        // ステータスを確認
        $this->assertResponseCode(302);
        // メッセージを確認
        $this->assertFlashMessage('release を削除しました。');
        // リダイレクトを確認
        $this->assertRedirect([
            'plugin' => 'BcBlog',
            'prefix' => 'Admin',
            'controller' => 'blog_categories',
            'action' => 'index/1'
        ]);
        // データの削除を確認
        $this->assertEquals(0, BlogCategoryFactory::count());
    }

}
