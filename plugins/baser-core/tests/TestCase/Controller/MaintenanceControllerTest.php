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
        $this->MaintenanceController->index();
        $status = $this->MaintenanceController->getResponse()->withStatus(503);
        $this->assertEquals(503, $status->getStatusCode());

        $var = $this->MaintenanceController->viewBuilder()->getVars();
        $this->assertEquals('メンテナンス中', $var['title']);
    }
}
