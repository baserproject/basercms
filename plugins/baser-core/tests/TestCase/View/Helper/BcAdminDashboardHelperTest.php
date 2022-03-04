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

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\View\BcAdminAppView;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\Helper\BcAdminDashboardHelper;

/**
 * Class BcAdminDashboardHelperTest
 * @property BcAdminDashboardHelper $BcAdminDashboard
 */
class BcAdminDashboardHelperTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Dblogs',
        'plugin.BaserCore.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $BcAdminAppView = new BcAdminAppView($this->getRequest()->withParam('controller', 'users'));
        $BcAdminAppView->setTheme('BcAdminThird');
        $this->BcAdminDashboard = new BcAdminDashboardHelper($BcAdminAppView);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdminDashboard);
        parent::tearDown();
    }

    /**
     * Test getDblogs
     */
    public function testGetDblogs()
    {
        $dblogs = $this->BcAdminDashboard->getDblogs(2);
        $this->assertEquals(2, count($dblogs));
    }

}
