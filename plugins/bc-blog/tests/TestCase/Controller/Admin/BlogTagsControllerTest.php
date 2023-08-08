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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\Admin\BlogTagsController;
use BcBlog\Test\Factory\BlogTagFactory;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogTagsControllerTest
 *
 * @property BlogTagsController $BlogTagsController
 */
class BlogTagsControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->BlogTagsController = new BlogTagsController($this->loginAdmin($this->getRequest()));
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
     * test index
     */
    public function test_initialize()
    {
        $this->assertNotEmpty($this->BlogTagsController->BcAdminContents);
    }

    /**
     * test add
     */
    public function test_add(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //実行成功場合、
        $data = [
            'name' => 'test add'
        ];
        $this->post('/baser/admin/bc-blog/blog_tags/add', $data);
        //リダイレクトを確認
        $this->assertResponseCode(302);
        $this->assertRedirect(['action' => 'index']);
        //メッセージを確認
        $this->assertFlashMessage('タグ「test add」を追加しました。');

        //実行失敗場合、
        $data = [
            'name' => null
        ];
        $this->post('/baser/admin/bc-blog/blog_tags/add', $data);
        //メッセージを確認
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals(['name' => ['_empty' => "ブログタグを入力してください。"]], $vars['blogTag']->getErrors());
        //リダイレクトしないを確認
        $this->assertResponseCode(200);
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        // データ生成
        BlogTagFactory::make([
            'id' => '1',
            'name' => 'test_tag',
            'created' => '2022-01-27 12:56:53',
            'modified' => null
        ])->persist();
        $this->delete('/baser/admin/bc-blog/blog_tags/delete/1');
        // ステータスを確認
        $this->assertResponseCode(302);
        // メッセージを確認
        $this->assertFlashMessage('ブログタグ「test_tag」を削除しました。');
        // リダイレクトを確認
        $this->assertRedirect([
            'plugin' => 'BcBlog',
            'prefix' => 'Admin',
            'controller' => 'blog_tags',
            'action' => 'index'
        ]);
        // データの削除を確認
        $this->assertEquals(0, BlogTagFactory::count());

        //失敗テスト
        $this->delete('/baser/admin/bc-blog/blog_tags/delete/1');
        $this->assertResponseCode(404);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        BlogTagFactory::make(['id' => 1, 'name' => 'test'])->persist();

        //Getで使ったらリダイレクトしない
        $this->get('/baser/admin/bc-blog/blog_tags/edit/1', ['name' => null]);
        $this->assertResponseCode(200);
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals('test', $vars['blogTag']['name']);

        //実行成功場合
        $this->post('/baser/admin/bc-blog/blog_tags/edit/1', ['name' => 'updated']);
        //リダイレクトを確認
        $this->assertResponseCode(302);
        $this->assertRedirect(['action' => 'index']);
        //メッセージを確認
        $this->assertFlashMessage('タグ「updated」を更新しました。');

        //実行失敗場合
        $this->post('/baser/admin/bc-blog/blog_tags/edit/1', ['name' => null]);
        $vars = $this->_controller->viewBuilder()->getVars();
        //メッセージを確認
        $this->assertEquals(['name' => ['_empty' => "ブログタグを入力してください。"]], $vars['blogTag']->getErrors());
        //リダイレクトしないを確認
        $this->assertResponseCode(200);

        //実行失敗場合　存在しないIDを利用
        $this->post('/baser/admin/bc-blog/blog_tags/edit/11', ['name' => null]);
        $this->assertResponseCode(404);
    }

    /**
     * test index
     */
    public function test_index()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        BlogTagFactory::make(['name' => 'name test'])->persist();
        //実行成功場合
        $this->post('/baser/admin/bc-blog/blog_tags/index/1');
        //取得データを確認
        $vars = $this->_controller->viewBuilder()->getVars()['blogTags'];
        $this->assertEquals(1, count($vars));
        //リダイレクトを確認
        $this->assertResponseOk();
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcBlog.BlogTags.beforeAdd', function (Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'beforeAdd';
            $event->setData('data', $data);
        });
        $this->post('/baser/admin/bc-blog/blog_tags/add', ['name' => 'new']);
        $BlogTags = $this->getTableLocator()->get('BcBlog.BlogTags');
        $query = $BlogTags->find()->where(['name' => 'beforeAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcBlog.BlogTags.afterAdd', function (Event $event) {
            $data = $event->getData('data');
            $data->name = 'afterAdd';
            $blogTags = TableRegistry::getTableLocator()->get('BlogTags');
            $blogTags->save($data);
        });
        $this->post('/baser/admin/bc-blog/blog_tags/add', ['name' => 'new']);
        $blogTags = $this->getTableLocator()->get('BcBlog.BlogTags');
        $query = $blogTags->find()->where(['name' => 'afterAdd']);
        $this->assertEquals(1, $query->count());
    }
    /**
     * Test beforeAddEvent
     */
    public function testBeforeEditEvent()
    {
        BlogTagFactory::make(['id' => 1, 'name' => 'test'])->persist();
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcBlog.BlogTags.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'beforeEdit';
            $event->setData('data', $data);
        });
        $this->post('/baser/admin/bc-blog/blog_tags/edit/1', ['name' => 'updated']);
        $BlogTags = $this->getTableLocator()->get('BcBlog.BlogTags');
        $query = $BlogTags->find()->where(['name' => 'beforeEdit']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterEditEvent()
    {
        BlogTagFactory::make(['id' => 1, 'name' => 'test'])->persist();
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcBlog.BlogTags.afterEdit', function (Event $event) {
            $data = $event->getData('data');
            $data->name = 'afterEdit';
            $blogTags = TableRegistry::getTableLocator()->get('BlogTags');
            $blogTags->save($data);
        });
        $this->post('/baser/admin/bc-blog/blog_tags/edit/1', ['name' => 'updated']);
        $blogTags = $this->getTableLocator()->get('BcBlog.BlogTags');
        $query = $blogTags->find()->where(['name' => 'afterEdit']);
        $this->assertEquals(1, $query->count());
    }
}
