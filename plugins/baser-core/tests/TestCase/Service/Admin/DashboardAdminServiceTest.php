<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Service\Admin\DashboardAdminService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class DashboardAdminServiceTest
 * @property DashboardAdminService $DashboardAdmin
 */
class DashboardAdminServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Dblogs',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->DashboardAdmin = new DashboardAdminService();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->DashboardAdmin);
        parent::tearDown();
    }

    /**
     * Test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        $vars = $this->DashboardAdmin->getViewVarsForIndex(2);
        $this->assertTrue(isset($vars['dblogs']));
        $this->assertTrue(isset($vars['panels']));
        $this->assertTrue(isset($vars['contentsInfo']));
    }

    /**
     * test getPanels
     */
    public function test_getPanels()
    {
        $result = $this->DashboardAdmin->getPanels();
        $this->assertContains([
            'baser_news' => 'baser_news',
            'contents_info' => 'contents_info',
            'update_log' => 'update_log',
        ], $result);
    }

}
