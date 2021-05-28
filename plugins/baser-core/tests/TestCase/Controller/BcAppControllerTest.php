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
use BaserCore\Controller\BcAppController;
use Cake\Event\Event;

/**
 * BaserCore\Controller\BcAppController Test Case
 */
class BcAppControllerTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAppController = new BcAppController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->BcAppController);
    }

    /**
     * Test construct
     *
     * @return void
     */
    public function testConstruct(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test beforeFilter
     *
     * @return void
     */
    public function testBeforeFilter(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}