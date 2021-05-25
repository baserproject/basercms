<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\AppController;

/**
 * BaserCore\Controller\AppController Test Case
 */
class AppControllerTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->AppController = new AppController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->AppController->BcMessage);
        $this->assertNotEmpty($this->AppController->Security);
        $this->assertNotEmpty($this->AppController->Paginator);
    }

}
