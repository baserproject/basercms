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
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\ContentService;
use Cake\Controller\ComponentRegistry;
use BaserCore\Controller\BcAppController;
use BaserCore\Service\ContentFolderService;
use BaserCore\Controller\Admin\ContentsController;
use BaserCore\Controller\Admin\ContentFoldersController;
use BaserCore\Controller\Component\BcAdminContentsComponent;


/**
 * Class BcMessageTestController
 *
 * @package BaserCore\Test\TestCase\Controller\Component
 * @property BcMessageComponent $BcMessage
 */
class BcAdminContentsTestController extends BcAppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('BaserCore.Contents');
        $this->Contents->addBehavior('Tree', ['level' => 'level']);
    }
}

/**
 * Class BcAdminContentsComponentTest
 *
 * @package BaserCore\Test\TestCase\Controller\Component
 * @property BcAdminContentsComponent $BcAdminContents
 */
class BcAdminContentsComponentTest extends BcTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * set up
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->getRequest('baser/admin/contents');
        $this->Controller = new BcAdminContentsTestController();
        $this->ComponentRegistry = new ComponentRegistry($this->Controller);
        $this->BcAdminContents = new BcAdminContentsComponent($this->ComponentRegistry);
        $this->ContentService = new ContentService();
        $this->ContentFolderService = new ContentFolderService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($_SESSION, $this->Controller, $this->BcAdminContents);
        Router::reload();
        parent::tearDown();
    }
    /**
     * test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcAdminContents->ContentService);
        // baser/admin/contents 管理システム設定の場合
        $this->assertNotEmpty($this->BcAdminContents->getConfig('items'));
    }
    /**
     * test setupAdmin
     *
     * @return void
     */
    public function testSetupAdmin()
    {
        $this->BcAdminContents->setupAdmin();
        $this->assertNotEmpty($this->BcAdminContents->getConfig('items'));
    }


    public function testBeforeRender()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * testSettingForm
     * ※ コントローラーがContentControllerの場合
     * @return void
     */
    public function testSettingFormWithContent()
    {
        $Controller = new ContentsController();
        $content = $this->ContentService->get(1);
        $Controller->set('content', $content);
        $ComponentRegistry = new ComponentRegistry($Controller);
        $BcAdminContents = new BcAdminContentsComponent($ComponentRegistry);
        $BcAdminContents->settingForm();
        $vars = $Controller->viewBuilder()->getVars();
        $this->assertIsBool($vars['related']);
        $this->assertIsInt($vars["currentSiteId"]);
        $this->assertInstanceOf("BaserCore\Model\Entity\Content", $vars["content"]);
        $this->assertIsArray($vars["relatedContents"]);
        $this->assertEquals($content->site_id == 1 ? null : 1, $vars["mainSiteId"]);
        $this->assertEquals("パソコン", $vars["mainSiteDisplayName"]);
        $this->assertInstanceOf("Cake\ORM\Query", $vars["sites"]);
        $this->assertNotNull($vars["layoutTemplates"]);
        $this->assertIsString($vars["publishLink"]);
    }

    /**
     * testSettingForm
     *※ コントローラーがContentFolderControllerの場合
     * @return void
     */
    public function testSettingFormWithContentFolder()
    {
        $Controller = new ContentFoldersController();
        $contentFolder = $this->ContentFolderService->get(1);
        $Controller->set('contentFolder', $contentFolder);
        $Controller->set('content', $contentFolder->content);
        $Controller->set('contentEntities', [
            'ContentFolder' => $contentFolder,
            'Content' => $contentFolder->content,
        ]);
        $ComponentRegistry = new ComponentRegistry($Controller);
        $BcAdminContents = new BcAdminContentsComponent($ComponentRegistry);
        $BcAdminContents->settingForm();
        $vars = $Controller->viewBuilder()->getVars();
        $this->assertIsBool($vars['related']);
        $this->assertIsInt($vars["currentSiteId"]);
        $this->assertInstanceOf("BaserCore\Model\Entity\Content", $vars["content"]);
        $this->assertIsArray($vars["relatedContents"]);
        $this->assertEquals($contentFolder->content->site_id == 1 ? null : 1, $vars["mainSiteId"]);
        $this->assertEquals("パソコン", $vars["mainSiteDisplayName"]);
        $this->assertInstanceOf("Cake\ORM\Query", $vars["sites"]);
        $this->assertNotNull($vars["layoutTemplates"]);
    }

    /**
     * testCheckContentEntities
     *
     * @return void
     */
    public function testCheckContentEntities()
    {
        // contentEntitiesの順番が適切でない場合順番が入れ替わってるかチェック
        $entities = ['Content' => 'test', 'ContentFolder' => 'test'];
        $this->Controller->viewBuilder()->setVar('contentEntities', $entities);
        $this->execPrivateMethod($this->BcAdminContents, 'checkContentEntities', [$this->Controller]);
        $entities = $this->Controller->viewBuilder()->getVar('contentEntities');
        $this->assertNotEquals('Content', array_key_first($entities));
    }

    /**
     * testCheckContentEntities
     *
     * @return void
     */
    public function testCheckContentEntitiesWithError()
    {
        // contentEntitiesが適切でない場合エラーができるかチェック
        $this->expectExceptionMessage('contentEntitiesが適切に設定されていません');
        $this->execPrivateMethod($this->BcAdminContents, 'checkContentEntities', [$this->Controller]);
    }
}
