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

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\PagesDisplayService;
use BaserCore\Controller\Admin\PreviewController;

/**
 * Class PreviewControllerTest
 *
 * @property  PreviewController $PreviewController
 */
class PreviewControllerTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BcSearchIndex.SearchIndexes',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PreviewController = new PreviewController($this->getRequest());
        $this->PreviewService = new PagesDisplayService();
        $this->Pages = $this->getTableLocator()->get('BaserCore.Pages');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PreviewController, $this->PreviewService, $this->Pages);
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->PreviewController->BcFrontContents);
        $this->assertNotEmpty($this->PreviewController->ContentsService);
    }
    /**
     * testView
     */
    public function testView()
    {
        $this->loginAdmin($this->getRequest('/baser/admin'));
        // getリクエストの場合既存のデータを返す
        $this->get('/baser/admin/baser-core/preview/view?url=https://localhost/about&preview=default');
        $this->assertResponseOk();
        // postの際はcontents_tmpが反映されているかを確認
        $page = $this->Pages->find()->contain('Contents')->first();
        $page->contents_tmp = "<p>test</p>";
        $this->enableCsrfToken();
        $this->post('/baser/admin/baser-core/preview/view?url=https://localhost/about&preview=default', ['Page' => $page->toArray()]);
        $this->assertResponseOk();
        $this->assertEquals($page->contents_tmp, $this->viewVariable('page')['contents']);
    }

    /**
     * testCreateRequest
     *
     * @return void
     * @dataProvider createRequestDataProvider
     */
    public function testCreateRequest($url, $expected)
    {
        $result = $this->execPrivateMethod($this->PreviewController, 'createRequest', [$url]);
        $this->assertEquals($result->getParam('controller'), $expected['controller']);
        $this->assertEquals($result->getParam('action'), $expected['action']);
        $this->assertEquals($result->getParam('Content.id'), $expected['id']);
    }

    public function createRequestDataProvider()
    {
        return [
            // メインサイトの場合
            [
                '/about',
                ['controller' => "Pages", 'action' => 'display', 'id' => 5]
            ],
            // サブサイトの場合
            [
                '/en/サイトID3の固定ページ',
                ['controller' => "Pages", 'action' => 'display', 'id' => 25]
            ],
        ];
    }
}
