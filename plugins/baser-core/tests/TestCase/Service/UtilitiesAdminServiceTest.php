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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\UtilitiesAdminService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class UtilitiesAdminServiceTest
 * @property UtilitiesAdminService $UtilitiesAdminService
 */
class UtilitiesAdminServiceTest extends BcTestCase
{

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UtilitiesAdminService = new UtilitiesAdminService();
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
     * test getViewVarsForInfo
     */
    public function test_getViewVarsForInfo()
    {
        $info = $this->UtilitiesAdminService->getViewVarsForInfo();
        $this->assertArrayHasKey('datasource', $info);
        $this->assertArrayHasKey('cakeVersion', $info);
        $this->assertArrayHasKey('baserVersion', $info);
    }

    /**
     * test _getDriver
     */
    public function test_getDriver()
    {
        $result = $this->execPrivateMethod($this->UtilitiesAdminService, '_getDriver');
        $this->assertEquals('MySQL', $result);
    }

    /**
     * test getViewVarsForLogMaintenance
     */
    public function test_getViewVarsForLogMaintenance()
    {
        $info = $this->UtilitiesAdminService->getViewVarsForLogMaintenance();
        $this->assertArrayHasKey('fileSize', $info);
    }

}
