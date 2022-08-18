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

use App\Application;
use BaserCore\Event\BcContainerEventListener;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainer;
use Cake\Event\Event;
use Cake\Event\EventManager;

/**
 * Class BcContainerEventListenerTest
 *
 * @package Baser.Test.Case.Event
 * @property  BcContainerEventListener $bcContainerEventListener
 */
class BcContainerEventListenerTest extends BcTestCase
{

    /**
     * @var EventManager|null
     */
    public $eventManager;

    /**
     * @var BcContainerEventListener|null
     */
    public $bcContainerEventListener;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->eventManager = EventManager::instance();
        $this->bcContainerEventListener = new BcContainerEventListener();
        foreach($this->bcContainerEventListener->implementedEvents() as $key => $event) {
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
        $this->bcContainerEventListener = null;
        parent::tearDown();
    }

    /**
     * test implementedEvents
     */
    public function testImplementedEvents()
    {
        $rs = $this->bcContainerEventListener->implementedEvents();
        $this->assertTrue(is_array($rs));
        $this->assertEquals("buildContainer", $rs['Application.buildContainer']['callable']);
    }

    /**
     * initialize
     */
    public function testInitialize()
    {
        $listener = $this->getMockBuilder(BcContainerEventListener::class)
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Application.buildContainer' => ['callable' => 'buildContainer']]);

        $listener->expects($this->once())
            ->method('buildContainer');

        $this->eventManager
            ->on($listener)
            ->dispatch(new Event('Application.buildContainer', new Application(ROOT . '/config'), []));
    }

    /**
     * test buildContainer
     * @return void
     */
    public function testBuildContainer(){
        $event = new Event("test", ["subject" => "_subject test"], ["container" => "container test"]);
        $this->bcContainerEventListener->buildContainer($event);
        $this->assertEquals("container test", BcContainer::$container);
    }
}
