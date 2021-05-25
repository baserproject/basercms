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

namespace BaserCore\Test\TestCase\View;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;

/**
 * Class BcAdminAppViewTest
 * @package BaserCore\Test\TestCase\View;
 * @property BcAdminAppView $BcAdminAppView
 */
class BcAdminAppViewTest extends BcTestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdminAppView = new BcAdminAppView();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdminAppView);
        parent::tearDown();
    }


    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcAdminAppView->BcAdminForm);
        $this->assertNotEmpty($this->BcAdminAppView->BcBaser);
        $this->assertNotEmpty($this->BcAdminAppView->BcAuth);
        $this->assertNotEmpty($this->BcAdminAppView->BcAdmin);
        $this->assertNotEmpty($this->BcAdminAppView->BcUserManage);
        $this->assertEquals($this->BcAdminAppView->get('title'), 'Undefined');

    }
}
