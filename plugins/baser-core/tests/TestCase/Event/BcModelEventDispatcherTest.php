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

use BaserCore\Event\BcModelEventDispatcher;
use BaserCore\Event\BcModelEventListener;
use BaserCore\Model\Table\UsersTable;
use BaserCore\TestSuite\BcTestCase;
use Cake\Event\Event;
use Cake\Event\EventManager;

/**
 * Class BcModelEventDispatcherTest
 *
 * @package Baser.Test.Case.Event
 * @property  BcModelEventDispatcher $bcModelEventDispatcher
 * @property eventManager $eventManager
 */
class BcModelEventDispatcherTest extends BcTestCase
{

    /**
     * @var EventManager|null
     */
    public $eventManager;

    /**
     * @var BcModelEventDispatcher|null
     */
    public $bcModelEventDispatcher;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->eventManager = EventManager::instance();
        $this->bcModelEventDispatcher = new BcModelEventDispatcher();
        foreach($this->bcModelEventDispatcher->implementedEvents() as $key => $event) {
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
        $this->bcModelEventDispatcher = null;
        parent::tearDown();
    }

    /**
     * implementedEvents
     */
    public function testImplementedEvents()
    {
        $this->assertTrue(is_array($this->bcModelEventDispatcher->implementedEvents()));
    }

    /**
     * beforeFind
     */
    public function testBeforeFind()
    {
        $listener = $this->getMockBuilder(BcModelEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersBeforeFind'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Model.Users.beforeFind' => ['callable' => 'usersBeforeFind']]);

        $listener->expects($this->once())
            ->method('usersBeforeFind');

        $this->eventManager
            ->on($listener)
            ->on($this->bcModelEventDispatcher)
            ->dispatch(new Event(
                'Model.beforeFind',
                new UsersTable,
                []
            ));
    }

    /**
     * afterFind
     */
    public function testAfterFind()
    {
        $listener = $this->getMockBuilder(BcModelEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersAfterFind'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Model.Users.afterFind' => ['callable' => 'usersAfterFind']]);

        $listener->expects($this->once())
            ->method('usersAfterFind');

        $this->eventManager
            ->on($listener)
            ->on($this->bcModelEventDispatcher)
            ->dispatch(new Event(
                'Model.afterFind',
                new UsersTable,
                []
            ));
    }

    /**
     * beforeValidate
     */
    public function testBeforeValidate()
    {
        $listener = $this->getMockBuilder(BcModelEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersBeforeValidate'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Model.Users.beforeValidate' => ['callable' => 'usersBeforeValidate']]);

        $listener->expects($this->once())
            ->method('usersBeforeValidate');

        $this->eventManager
            ->on($listener)
            ->on($this->bcModelEventDispatcher)
            ->dispatch(new Event(
                'Model.beforeValidate',
                new UsersTable,
                []
            ));
    }

    /**
     * afterValidate
     */
    public function testAfterValidate()
    {
        $listener = $this->getMockBuilder(BcModelEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersAfterValidate'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Model.Users.afterValidate' => ['callable' => 'usersAfterValidate']]);

        $listener->expects($this->once())
            ->method('usersAfterValidate');

        $this->eventManager
            ->on($listener)
            ->on($this->bcModelEventDispatcher)
            ->dispatch(new Event(
                'Model.afterValidate',
                new UsersTable,
                []
            ));
    }

    /**
     * beforeSave
     */
    public function testBeforeSave()
    {
        $listener = $this->getMockBuilder(BcModelEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersBeforeSave'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Model.Users.beforeSave' => ['callable' => 'usersBeforeSave']]);

        $listener->expects($this->once())
            ->method('usersBeforeSave');

        $this->eventManager
            ->on($listener)
            ->on($this->bcModelEventDispatcher)
            ->dispatch(new Event(
                'Model.beforeSave',
                new UsersTable,
                []
            ));
    }

    /**
     * afterSave
     */
    public function testAfterSave()
    {
        $listener = $this->getMockBuilder(BcModelEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersAfterSave'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Model.Users.afterSave' => ['callable' => 'usersAfterSave']]);

        $listener->expects($this->once())
            ->method('usersAfterSave');

        $this->eventManager
            ->on($listener)
            ->on($this->bcModelEventDispatcher)
            ->dispatch(new Event(
                'Model.afterSave',
                new UsersTable,
                []
            ));
    }

    /**
     * beforeDelete
     */
    public function testBeforeDelete()
    {
        $listener = $this->getMockBuilder(BcModelEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersBeforeDelete'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Model.Users.beforeDelete' => ['callable' => 'usersBeforeDelete']]);

        $listener->expects($this->once())
            ->method('usersBeforeDelete');

        $this->eventManager
            ->on($listener)
            ->on($this->bcModelEventDispatcher)
            ->dispatch(new Event(
                'Model.beforeDelete',
                new UsersTable,
                []
            ));
    }

    /**
     * afterDelete
     */
    public function testAfterDelete()
    {
        $listener = $this->getMockBuilder(BcModelEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['usersAfterDelete'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Model.Users.afterDelete' => ['callable' => 'usersAfterDelete']]);

        $listener->expects($this->once())
            ->method('usersAfterDelete');

        $this->eventManager
            ->on($listener)
            ->on($this->bcModelEventDispatcher)
            ->dispatch(new Event(
                'Model.afterDelete',
                new UsersTable,
                []
            ));
    }

}
