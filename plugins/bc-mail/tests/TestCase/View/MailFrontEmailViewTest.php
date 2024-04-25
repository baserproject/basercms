<?php

namespace BcMail\Test\TestCase\View;

use BaserCore\TestSuite\BcTestCase;
use BcMail\View\MailFrontEmailView;

class MailFrontEmailViewTest extends BcTestCase
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
    public function testInitialize()
    {
        $this->getRequest();
        $mailFrontEmailView = new MailFrontEmailView($this->getRequest());
        $this->assertNotEmpty($mailFrontEmailView->Mailfield);
        $this->assertNotEmpty($mailFrontEmailView->Maildata);

    }
}