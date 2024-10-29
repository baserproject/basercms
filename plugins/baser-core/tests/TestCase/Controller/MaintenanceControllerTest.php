<?php

namespace BaserCore\Test\TestCase\Controller;

use BaserCore\Controller\MaintenanceController;
use BaserCore\TestSuite\BcTestCase;

class MaintenanceControllerTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->MaintenanceController = new MaintenanceController($this->getRequest());
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test index
     */
    public function testIndex()
    {
        $this->get('/maintenance');
        $this->assertResponseOk();

        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals('メンテナンス中', $vars['title']);
    }
}
