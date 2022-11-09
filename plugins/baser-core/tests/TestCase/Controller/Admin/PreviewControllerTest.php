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
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BaserCore.Factory/Pages',
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
        $this->assertEquals(['view'], $this->PreviewController->Security->getConfig('unlockedActions'));
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
}
