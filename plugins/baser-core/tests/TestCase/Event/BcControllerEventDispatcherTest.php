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

namespace BaserCore\Test\TestCase\Event;

use BaserCore\Controller\Admin\UsersController;
use BaserCore\Event\BcControllerEventDispatcher;
use BaserCore\Event\BcControllerEventListener;
use BaserCore\TestSuite\BcTestCase;
use Cake\Event\Event;
use Cake\Event\EventManager;

/**
 * Class BcControllerEventDispatcherTest
 *
 * @property  BcControllerEventDispatcher $BcControllerEventDispatcher
 */
class BcControllerEventDispatcherTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Sites'
    ];

    /**
     * @var EventManager|null
     */
    public $eventManager;

    /**
     * @var BcControllerEventDispatcher|null
     */
    public $bcControllerEventDispatcher;

    /**
     * @var UsersController
     */
    public $UsersController;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->eventManager = EventManager::instance();
        $this->bcControllerEventDispatcher = new BcControllerEventDispatcher();
        foreach($this->bcControllerEventDispatcher->implementedEvents() as $key => $event) {
            $this->eventManager->off($key);
        }
        $this->UsersController = new UsersController($this->loginAdmin($this->getRequest('/baser/admin/baser-core/users/')));
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->eventManager = null;
        $this->bcControllerEventDispatcher = null;
        parent::tearDown();
    }

    /**
     * implementedEvents
     */
    public function testImplementedEvents()
    {
        $this->assertTrue(is_array($this->bcControllerEventDispatcher->implementedEvents()));
    }

    /**
     * initialize
     */
    public function testInitialize()
    {
        $listener = $this->getMockBuilder(BcControllerEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersInitialize'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Controller.BaserCore.Users.initialize' => ['callable' => 'usersInitialize']]);

        $listener->expects($this->once())
            ->method('usersInitialize');

        $this->eventManager
            ->on($listener)
            ->on($this->bcControllerEventDispatcher)
            ->dispatch(new Event('Controller.initialize', $this->UsersController, []));
    }

    /**
     * startup
     */
    public function testStartup()
    {
        $listener = $this->getMockBuilder(BcControllerEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersStartup'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Controller.BaserCore.Users.startup' => ['callable' => 'usersStartup']]);

        $listener->expects($this->once())
            ->method('usersStartup');

        $this->eventManager
            ->on($listener)
            ->on($this->bcControllerEventDispatcher)
            ->dispatch(new Event('Controller.startup', $this->UsersController, []));
    }

    /**
     * beforeRender
     */
    public function testBeforeRender()
    {
        $listener = $this->getMockBuilder(BcControllerEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersBeforeRender'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Controller.BaserCore.Users.beforeRender' => ['callable' => 'usersBeforeRender']]);

        $listener->expects($this->once())
            ->method('usersBeforeRender');

        $this->eventManager
            ->on($listener)
            ->on($this->bcControllerEventDispatcher)
            ->dispatch(new Event('Controller.beforeRender', $this->UsersController, []));
    }

    /**
     * beforeRedirect
     */
    public function testBeforeRedirect()
    {
        $listener = $this->getMockBuilder(BcControllerEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersBeforeRedirect'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Controller.BaserCore.Users.beforeRedirect' => ['callable' => 'usersBeforeRedirect']]);

        $listener->expects($this->once())
            ->method('usersBeforeRedirect');

        $this->eventManager
            ->on($listener)
            ->on($this->bcControllerEventDispatcher)
            ->dispatch(new Event('Controller.beforeRedirect', $this->UsersController, []));
    }

    /**
     * shutdown
     */
    public function testShutdown()
    {
        $listener = $this->getMockBuilder(BcControllerEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersShutdown'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Controller.BaserCore.Users.shutdown' => ['callable' => 'usersShutdown']]);

        $listener->expects($this->once())
            ->method('usersShutdown');

        $this->eventManager
            ->on($listener)
            ->on($this->bcControllerEventDispatcher)
            ->dispatch(new Event('Controller.shutdown', $this->UsersController, []));
    }

}
