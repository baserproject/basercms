<?php

namespace BcMail\Test\TestCase\Controller\Admin;

use BaserCore\TestSuite\BcTestCase;
use BcMail\Controller\Admin\MailAdminAppController;
use Cake\Event\Event;

class MailAdminAppControllerTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->Controller = new MailAdminAppController($this->getRequest());
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testAdmin_beforeRender()
    {
        $this->Controller->beforeRender(new Event('beforeRender'));
        $this->assertEquals('BcMail.MailAdminApp', $this->Controller->viewBuilder()->getClassName());
        $this->Controller->setRequest($this->Controller->getRequest()->withQueryParams(['preview' => 'default']));
        $this->Controller->viewBuilder()->setClassName('');
        $this->Controller->beforeRender(new Event('beforeRender'));
        $this->assertNotEquals('BcMail.MailAdminApp', $this->Controller->viewBuilder()->getClassName());
    }
}