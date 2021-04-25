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
 * @package Baser.Test.Case.Event
 * @property  BcControllerEventDispatcher $BcControllerEventDispatcher
 */
class BcControllerEventDispatcherTest extends BcTestCase
{

    /**
     * @var EventManager|null
     */
    public $eventManager;

    /**
     * @var BcControllerEventDispatcher|null
     */
    public $bcControllerEventDispatcher;

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
            ->willReturn(['Controller.Users.initialize' => ['callable' => 'usersInitialize']]);

        $listener->expects($this->once())
            ->method('usersInitialize');

        $this->eventManager
            ->on($listener)
            ->on($this->bcControllerEventDispatcher)
            ->dispatch(new Event('Controller.initialize', new UsersController(), []));
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
            ->willReturn(['Controller.Users.startup' => ['callable' => 'usersStartup']]);

        $listener->expects($this->once())
            ->method('usersStartup');

        $this->eventManager
            ->on($listener)
            ->on($this->bcControllerEventDispatcher)
            ->dispatch(new Event('Controller.startup', new UsersController(), []));
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
            ->willReturn(['Controller.Users.beforeRender' => ['callable' => 'usersBeforeRender']]);

        $listener->expects($this->once())
            ->method('usersBeforeRender');

        $this->eventManager
            ->on($listener)
            ->on($this->bcControllerEventDispatcher)
            ->dispatch(new Event('Controller.beforeRender', new UsersController(), []));
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
            ->willReturn(['Controller.Users.beforeRedirect' => ['callable' => 'usersBeforeRedirect']]);

        $listener->expects($this->once())
            ->method('usersBeforeRedirect');

        $this->eventManager
            ->on($listener)
            ->on($this->bcControllerEventDispatcher)
            ->dispatch(new Event('Controller.beforeRedirect', new UsersController(), []));
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
            ->willReturn(['Controller.Users.shutdown' => ['callable' => 'usersShutdown']]);

        $listener->expects($this->once())
            ->method('usersShutdown');

        $this->eventManager
            ->on($listener)
            ->on($this->bcControllerEventDispatcher)
            ->dispatch(new Event('Controller.shutdown', new UsersController(), []));
    }

}
