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
    public function testSetupFront()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testGetCrumbs()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
