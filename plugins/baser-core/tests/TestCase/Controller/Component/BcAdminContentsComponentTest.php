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
use Cake\Controller\ComponentRegistry;
use BaserCore\Controller\BcAppController;
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
        'plugin.BaserCore.Sites',
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

    public function testSettingForm()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testGetParentLayoutTemplate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
