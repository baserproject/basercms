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

use BaserCore\Event\BcControllerEventDispatcher;
use BaserCore\Event\BcControllerEventListener;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\TestSuite\BcTestCase;
use Cake\Controller\Controller;
use Cake\Event\EventManager;

/**
 * Class BcControllerEventDispatcherTest
 *
 * @package Baser.Test.Case.Event
 * @property  BcControllerEventDispatcher $BcControllerEventDispatcher
 */
class BcEventDispatcherTraitTest extends BcTestCase
{

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
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
     * testDispatchLayerEvent
     */
    public function testDispatchLayerEvent()
    {
        $listener = $this->getMockBuilder(BcControllerEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods(['testTest'])
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Controller.Test.test' => ['callable' => 'testTest']]);

        $listener->expects($this->once())
            ->method('testTest');

        EventManager::instance()->on($listener);

        $class = new class extends Controller {
            use BcEventDispatcherTrait;
        };
        $class->setName('Test');
        $class->dispatchLayerEvent('test', [], []);
    }

}
