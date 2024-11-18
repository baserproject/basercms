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
 * Class BcEventDispatcherTest
 *
 */
class BcEventDispatcherTest extends BcTestCase
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
        parent::tearDown();
    }

    /**
     * dispatch
     */
    public function testDispatch()
    {
        foreach($this->bcModelEventDispatcher->implementedEvents() as $key => $event) {
            $this->eventManager->off($key);
        }

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

}
