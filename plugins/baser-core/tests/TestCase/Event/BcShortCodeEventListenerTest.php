<?php

namespace BaserCore\Test\TestCase\Event;

use BaserCore\Event\BcShortCodeEventListener;
use BaserCore\TestSuite\BcTestCase;

class BcShortCodeEventListenerTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcShortCodeEventListener = new BcShortCodeEventListener();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test implementedEvents
     */
    public function testImplementedEvents()
    {
        $this->assertTrue(is_array($this->BcShortCodeEventListener->implementedEvents()));
    }

}
