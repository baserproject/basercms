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

use BaserCore\Event\BcViewEventDispatcher;
use BaserCore\Event\BcViewEventListener;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\AppView;
use Cake\Event\Event;
use Cake\Event\EventManager;

/**
 * Class BcViewEventDispatcherTest
 *
 * @property  BcViewEventDispatcher $bcViewEventDispatcher
 */
class BcViewEventDispatcherTest extends BcTestCase
{

    /**
     * @var EventManager|null
     */
    public $eventManager;

    /**
     * @var BcViewEventDispatcher|null
     */
    public $bcViewEventDispatcher;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->eventManager = EventManager::instance();
        $this->bcViewEventDispatcher = new BcViewEventDispatcher();
        foreach($this->bcViewEventDispatcher->implementedEvents() as $key => $event) {
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
        $this->bcViewEventDispatcher = null;
        parent::tearDown();
    }

    /**
     * implementedEvents
     */
    public function testImplementedEvents()
    {
        $this->assertTrue(is_array($this->bcViewEventDispatcher->implementedEvents()));
    }

    /**
     * beforeRenderFile
     */
    public function testBeforeRenderFile()
    {
        $listener = $this->getMockBuilder(BcViewEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersBeforeRenderFile'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['View.Users.beforeRenderFile' => ['callable' => 'usersBeforeRenderFile']]);

        $listener->expects($this->once())
            ->method('usersBeforeRenderFile');

        $this->eventManager
            ->on($listener)
            ->on($this->bcViewEventDispatcher)
            ->dispatch(new Event('View.beforeRenderFile', new AppView(null, null, null, ['name' => 'Users']), []));
    }

    /**
     * afterRenderFile
     */
    public function testAfterRenderFile()
    {
        $listener = $this->getMockBuilder(BcViewEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersAfterRenderFile'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['View.Users.afterRenderFile' => ['callable' => 'usersAfterRenderFile']]);

        $listener->expects($this->once())
            ->method('usersAfterRenderFile');

        $this->eventManager
            ->on($listener)
            ->on($this->bcViewEventDispatcher)
            ->dispatch(new Event(
                'View.afterRenderFile',
                new AppView(null, null, null, ['name' => 'Users']),
                [0 => null, 1 => 'hoge']
            ));
    }

    /**
     * beforeRender
     */
    public function testBeforeRender()
    {
        $listener = $this->getMockBuilder(BcViewEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersBeforeRender'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['View.Users.beforeRender' => ['callable' => 'usersBeforeRender']]);

        $listener->expects($this->once())
            ->method('usersBeforeRender');

        $this->eventManager
            ->on($listener)
            ->on($this->bcViewEventDispatcher)
            ->dispatch(new Event(
                'View.beforeRender',
                new AppView(null, null, null, ['name' => 'Users']),
                []
            ));
    }

    /**
     * afterRender
     */
    public function testAfterRender()
    {
        $listener = $this->getMockBuilder(BcViewEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersAfterRender'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['View.Users.afterRender' => ['callable' => 'usersAfterRender']]);

        $listener->expects($this->once())
            ->method('usersAfterRender');

        $this->eventManager
            ->on($listener)
            ->on($this->bcViewEventDispatcher)
            ->dispatch(new Event(
                'View.afterRender',
                new AppView(null, null, null, ['name' => 'Users']),
                []
            ));
    }

    /**
     * beforeLayout
     */
    public function testBeforeLayout()
    {
        $listener = $this->getMockBuilder(BcViewEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersBeforeLayout'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['View.Users.beforeLayout' => ['callable' => 'usersBeforeLayout']]);

        $listener->expects($this->once())
            ->method('usersBeforeLayout');

        $this->eventManager
            ->on($listener)
            ->on($this->bcViewEventDispatcher)
            ->dispatch(new Event(
                'View.beforeLayout',
                new AppView(null, null, null, ['name' => 'Users']),
                []
            ));
    }

    /**
     * afterLayout
     */
    public function testAfterLayout()
    {
        $listener = $this->getMockBuilder(BcViewEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersAfterLayout'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['View.Users.afterLayout' => ['callable' => 'usersAfterLayout']]);

        $listener->expects($this->once())
            ->method('usersAfterLayout');

        $this->eventManager
            ->on($listener)
            ->on($this->bcViewEventDispatcher)
            ->dispatch(new Event(
                'View.afterLayout',
                new AppView(null, null, null, ['name' => 'Users']),
                []
            ));
    }

}
