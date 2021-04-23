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
use Cake\TestSuite\TestCase;
use BaserCore\Controller\AppController;
use BaserCore\Model\Table\LoginStoresTable;

/**
 * BaserCore\Controller\AppController Test Case
 */
class AppControllerTest extends TestCase
{
    use IntegrationTestTrait;

    public $autoFixtures = false;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test setCookieAutoLoginKey method
     *
     * @return void
     */
    public function testSetCookieAutoLoginKey()
    {
        // $key = "testkey";
        // $appController = new AppController();
        // $appController->setCookieAutoLoginKey($key);
        $this->markTestIncomplete('Not implemented yet.');
    }

}
