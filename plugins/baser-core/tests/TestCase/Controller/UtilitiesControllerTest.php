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
use BaserCore\Controller\Admin\UtilitiesController;

/**
 * class UtilitiesControllerTest
 * @package Cake\TestSuite\BcTestCase;
 * @package BaserCore\Controller\Admin\UtilitiesController;
 */
class UtilitiesControllerTest extends BcTestCase
{
    use IntegrationTestTrait;
    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UtilitiesController = new UtilitiesController();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test clear_cache
     *
     * @return void
     */
    public function testClear_cache(): void
    {

        $this->get('/baser/admin/utilities/clear_cache');
        $this->assertResponseCode(302);
    }

    /**
     * test ajax_save_search_box
     *
     * @return void
     */
    public function testAjax_save_search_box(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
