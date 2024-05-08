<?php

namespace BcMail\Test\TestCase\View;

use BaserCore\Test\TestCase\BcPluginTest;
use BcMail\View\MailAdminAppView;

class MailAdminAppViewTest extends BcPluginTest
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_initialize(): void
    {
        $this->getRequest();
        $mailAdminAppView = new MailAdminAppView($this->getRequest());
        $this->assertNotEmpty($mailAdminAppView->Mail);
        $this->assertNotEmpty($mailAdminAppView->Mailfield);
        $this->assertNotEmpty($mailAdminAppView->Maildata);
        $this->assertNotEmpty($mailAdminAppView->BcArray);
        $this->assertNotEmpty($mailAdminAppView->BcCsv);
    }
}