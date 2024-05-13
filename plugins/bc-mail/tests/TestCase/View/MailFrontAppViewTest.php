<?php

namespace BcMail\Test\TestCase\View;

use BaserCore\TestSuite\BcTestCase;
use BcMail\View\MailFrontAppView;

class MailFrontAppViewTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function testInitialize(): void
    {
        $this->getRequest();
        $mailFrontAppView = new MailFrontAppView($this->getRequest());
        $this->assertNotEmpty($mailFrontAppView->Mail);
        $this->assertNotEmpty($mailFrontAppView->Mailfield);
        $this->assertNotEmpty($mailFrontAppView->Mailform);
    }
}