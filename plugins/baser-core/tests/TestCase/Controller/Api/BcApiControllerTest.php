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

namespace BaserCore\Test\TestCase\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * BaserCore\Controller\ApiControllerTest Test Case
 */
class BcApiControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testInitialize()
    {
        $controller = new BcApiController();
        $this->assertTrue(isset($controller->Authentication));
    }

}
