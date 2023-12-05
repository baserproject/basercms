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

namespace BaserCore\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\ContentFoldersScenario;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\PagesScenario;
use BaserCore\Test\Scenario\PluginsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use Cake\Event\Event;
use BaserCore\Service\PagesService;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\Admin\PagesController;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class PagesControllerTest
 *
 * @property  PagesController $PagesController
 */
class PagesControllerTest extends BcTestCase
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
        $this->loadFixtureScenario(PluginsScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(ContentFoldersScenario::class);
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(PagesScenario::class);
        $this->PagesController = new PagesController($this->getRequest());
        $this->PagesService = new PagesService();
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
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->PagesController->BcAdminContents);
    }

    /**
     * 固定ページ情報登録
     */
    public function testAdmin_ajax_add()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test beforeAdd
     */
    public function test_beforeAdd()
    {
        //準備
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BaserCore.Pages.beforeAdd', function (Event $event) {
            $data = $event->getData('data');
            $data['page_template'] = 'Nghiem';
            $event->setData('data', $data);
        });
        $this->loginAdmin($this->getRequest('/'));
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = [
            'page_template' => 'test create',
            'content' => [
                "parent_id" => "1",
                "title" => "test フォルダー",
                "plugin" => 'BaserCore',
                "type" => "Page",
                "site_id" => "1",
                "alias_id" => "",
                "entity_id" => "",
            ]
        ];
        //正常系実行
        $this->post('/baser/admin/baser-core/pages/add/' . 1, $data);
        $this->assertResponseSuccess();
        $Pages = $this->getTableLocator()->get('Pages');
        $rs = $Pages->find()->where(['page_template' => $data['page_template']])->toArray();
        $this->assertCount(0, $rs);
        $pages = $Pages->find()->where(['page_template' => 'Nghiem'])->toArray();
        $this->assertEquals('Nghiem', $pages[0]->page_template);
    }


    /**
     * test add
     */
    public function test_add()
    {
        $this->loginAdmin($this->getRequest('/'));
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = [
            'page_template' => 'Nghiem create',
            'content' => [
                "parent_id" => "1",
                "title" => "Nghiem フォルダー",
                "plugin" => 'BaserCore',
                "type" => "Page",
                "site_id" => "1",
                "alias_id" => "",
                "entity_id" => "",
            ]
        ];
        $this->post('/baser/admin/baser-core/pages/add/' . 1, $data);
        $this->assertResponseSuccess();
        $Pages = $this->getTableLocator()->get('Pages');
        $pages = $Pages->find()->where(['page_template' => $data['page_template']])->toArray();
        $this->assertCount(1, $pages);
        $this->assertEquals('Nghiem create', $pages[0]->page_template);
    }


    /**
     * test afterAdd
     */
    public function test_afterAdd()
    {
        //準備
        $this->loginAdmin($this->getRequest('/'));
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BaserCore.Pages.afterAdd', function (Event $event) {
            $page = $event->getData('data');
            $pages = $this->getTableLocator()->get('Pages');
            $page->page_template = 'Nghiem after';
            $pages->save($page);
        });
        $data = [
            'page_template' => 'test create',
            'content' => [
                "parent_id" => "1",
                "title" => "test フォルダー",
                "plugin" => 'BaserCore',
                "type" => "Page",
                "site_id" => "1",
                "alias_id" => "",
                "entity_id" => "",
            ]
        ];
        //正常系実行
        $this->post('/baser/admin/baser-core/pages/add/' . 1, $data);
        $this->assertResponseSuccess();
        $Pages = $this->getTableLocator()->get('Pages');
        $rs = $Pages->find()->where(['page_template' => $data['page_template']])->toArray();
        $this->assertCount(0, $rs);
        $pages = $Pages->find()->where(['page_template' => 'Nghiem after'])->toArray();
        $this->assertEquals('Nghiem after', $pages[0]->page_template);

    }

    /**
     * [ADMIN] 固定ページ情報編集
     */
    public function testEdit()
    {
        $this->loginAdmin($this->getRequest('/'));
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = $this->PagesService->getIndex()->first();
        $data->page_template = 'testEdit';
        $data->content->name = "pageTestUpdate";
        $id = $data->id;
        $this->post('/baser/admin/baser-core/pages/edit/' . $id, [
            'Pages' => $data->toArray(),
            "Contents" => ['title' => $data->content->name, 'parent_id' => $data->content->parent_id]
        ]);
        $this->assertResponseSuccess();
        $this->assertRedirect('/baser/admin/baser-core/pages/edit/' . $id);
        $this->assertEquals('testEdit', $this->PagesService->get($id)->page_template);
        $this->assertEquals('pageTestUpdate', $this->PagesService->get($id)->content->name);
    }

    /**
     * 削除
     *
     * Controller::requestAction() で呼び出される
     */
    public function testAdmin_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 固定ページファイルを登録する
     */
    public function testAdmin_entry_page_files()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 固定ページファイルを登録する
     */
    public function testAdmin_write_page_files()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ビューを表示する
     */
    public function testDisplay()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コピー
     */
    public function testAdmin_ajax_copy()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
