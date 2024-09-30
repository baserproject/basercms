<?php

namespace BaserCore\Test\TestCase\BcMailer;

use BaserCore\Mailer\BcMailer;
use BaserCore\TestSuite\BcTestCase;

class BcMailerTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcMailer = new BcMailer();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test getPlugin
     */
    public function test_getPlugin()
    {
        $plugin = $this->BcMailer->getPlugin();
        $this->assertEquals('BaserCore', $plugin);
    }
}
