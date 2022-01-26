<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Controller\Component;

use Cake\Routing\Router;
use BaserCore\Service\PageService;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\ContentService;
use Cake\Controller\ComponentRegistry;
use BaserCore\Controller\BcAppController;
use BaserCore\Controller\PagesController;
use BaserCore\Controller\Component\BcFrontContentsComponent;


/**
 * Class BcMessageTestController
 *
 * @package BaserCore\Test\TestCase\Controller\Component
 * @property BcMessageComponent $BcMessage
 */
class BcFrontContentsTestController extends BcAppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('BaserCore.Contents');
        $this->Contents->addBehavior('Tree', ['level' => 'level']);
    }
}

/**
 * Class BcFrontContentsComponentTest
 *
 * @package BaserCore\Test\TestCase\Controller\Component
 * @property BcFrontContentsComponent $BcFrontContents
 */
class BcFrontContentsComponentTest extends BcTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Pages',
    ];

    /**
     * set up
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->getRequest('baser/admin');
        $this->Controller = new BcFrontContentsTestController();
        $this->ComponentRegistry = new ComponentRegistry($this->Controller);
        $this->BcFrontContents = new BcFrontContentsComponent($this->ComponentRegistry);
        $this->PageService = new PageService();
        $this->ContentService = new ContentService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($_SESSION);
        Router::reload();
        parent::tearDown();
    }

    /**
     * testSetupFront
     * コントローラーがPagesControllerの場合
     * ※ NOTE ucmitz: プレビュー時のテスト未完了
     * @return void
     */
    public function testSetupFront()
    {
        $page = $this->PageService->get(2);
        $request = $this->getRequest()->withParam('Content', $page->content);
        $Controller = new PagesController($request);
        $ComponentRegistry = new ComponentRegistry($Controller);
        $BcFrontContents = new BcFrontContentsComponent($ComponentRegistry);
        $BcFrontContents->setupFront();
        $layout = $Controller->viewBuilder()->getLayout();
        $vars = $Controller->viewBuilder()->getVars();
        $this->assertEquals($this->ContentService->getParentLayoutTemplate($page->content->id), $layout);
        $this->assertIsString($vars['description']);
        $this->assertIsString($vars['title']);

    }

    public function testGetCrumbs()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
