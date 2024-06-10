<?php

namespace BcMail\Test\TestCase\Controller;

use BaserCore\TestSuite\BcTestCase;
use BcMail\Controller\MailFrontAppController;
use Cake\Event\Event;

class MailFrontAppControllerTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->MailFrontAppController = new MailFrontAppController($this->getRequest());
    }

    public function tearDown(): void
    {
        unset($this->MailFrontAppController);
        parent::tearDown();
    }

    public function test_beforeRender()
    {
        $this->MailFrontAppController->beforeRender(new Event('beforeRender'));
        $this->assertEquals('BcMail.MailFrontApp', $this->MailFrontAppController->viewBuilder()->getClassName());
    }
}