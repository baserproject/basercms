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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\PermissionsMockService;
use BaserCore\TestSuite\BcTestCase;

/**
 * BaserCore\Model\Table\PermissionsTable Test Case
 *
 * @property PermissionsService $PermissionsService
 */
class PermissionsServiceTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var PermissionsService
     */
    public $Permissions;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.UserGroups',
    ];

        /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PermissionsService = new PermissionsMockService();
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
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}