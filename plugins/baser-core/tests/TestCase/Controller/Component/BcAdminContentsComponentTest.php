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
        unset($_SESSION);
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
}
