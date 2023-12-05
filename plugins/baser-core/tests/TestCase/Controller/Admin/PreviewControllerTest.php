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

use BaserCore\Service\PagesServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SmallSetContentsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\Admin\PreviewController;
use BaserCore\Utility\BcContainerTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class PreviewControllerTest
 *
 * @property  PreviewController $PreviewController
 */
class PreviewControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PreviewController = new PreviewController($this->getRequest());
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
        $this->assertEquals(['view'], $this->PreviewController->FormProtection->getConfig('unlockedActions'));
    }

    /**
     * testView
     */
    public function testView()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $this->loginAdmin($this->getRequest('/baser/admin'));
        // getリクエストの場合既存のデータを返す
        $this->get('/baser/admin/baser-core/preview/view?url=https://localhost/&preview=default');
        $this->assertResponseOk();
        // 保存前プレビュー
        $pagesService = $this->getService(PagesServiceInterface::class);
        $page = $pagesService->get(1);
        $page->contents = "<p>test</p>";
        $page->title = "testView title";
        $page->content['title'] = "testView title";
        $page->content['created_date'] = date('Y-m-d H:i:s');

        $this->enableCsrfToken();
        $this->post('/baser/admin/baser-core/preview/view?url=https://localhost/&preview=default', $page->toArray());
        $this->assertResponseOk();
        $this->assertEquals($page->contents, $this->viewVariable('page')['contents']);
        // 草稿プレビュー
        $page->draft = "<p>draft</p>";
        $this->post('/baser/admin/baser-core/preview/view?url=https://localhost/&preview=draft', $page->toArray());
        $this->assertResponseOk();
        $this->assertEquals($page->draft, $this->viewVariable('page')['contents']);
    }

    /**
     * test _createPreviewRequest
     * @return void
     */
    public function test_createPreviewRequest()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $this->loginAdmin($this->getRequest('/'));
        $result = $this->execPrivateMethod(
            $this->PreviewController,
            '_createPreviewRequest',
            [$this->getRequest()->withQueryParams(['url' => 'https://localhost/', 'preview' => 'default'])]
        );
        $this->assertEquals('view', $result->getParam('action'));
        $this->assertEquals(1, $result->getParam('entityId'));
        $this->assertEquals('Pages', $result->getParam('controller'));
    }

    /**
     * test encodePath
     */
    public function test_encodePath()
    {
        //正常系実行
        $url = 'https://localhost/こんにちは/xin-chao?name=こんにちは';
        $result = $this->PreviewController->encodePath($url);
        $this->assertEquals('https://localhost/%E3%81%93%E3%82%93%E3%81%AB%E3%81%A1%E3%81%AF/xin-chao?name=こんにちは', $result);
        $url = 'https://localhost/abc-test-/xin-chao/';
        $result = $this->PreviewController->encodePath($url);
        $this->assertEquals('https://localhost/abc-test-/xin-chao/', $result);
    }

}
