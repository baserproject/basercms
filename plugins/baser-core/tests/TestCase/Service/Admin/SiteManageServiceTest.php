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

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Service\Admin\SiteManageServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;

/**
 * Class SiteManageServiceTest
 * @package BaserCore\Test\TestCase\Service
 * @property SiteManageServiceInterface $SiteManage
 */
class SiteManageServiceTest extends BcTestCase
{

    /**
     * BcContainerTrait
     */
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.LoginStores'
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SiteManage = $this->getService(SiteManageServiceInterface::class);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UserGroups);
        parent::tearDown();
    }

    /**
     * Test getLangs
     */
    public function testGetLangs()
    {
        $langs = $this->SiteManage->getLangs();
        $this->assertEquals('english', key($langs));
    }

    /**
     * Test getDevices
     */
    public function testGetDevices()
    {
        $devices = $this->SiteManage->getDevices();
        $this->assertEquals('mobile', key($devices));
    }

}
