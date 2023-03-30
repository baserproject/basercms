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

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\UsersUserGroupsTable;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class UsersUserGroupsTableTest
 *
 * @property UsersUserGroupsTable $UsersUserGroups
 */
class UsersUserGroupsTableTest extends BcTestCase
{
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
    ];

    /**
     * Auto Fixtures
     * @var bool
     */
    public $autoFixtures = false;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures('Users', 'UserGroups', 'UsersUserGroups');
        $this->UsersUserGroups = $this->getTableLocator()->get('BaserCore.UsersUserGroups');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UsersUserGroups);
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {;
        $this->assertTrue($this->UsersUserGroups->hasBehavior('Timestamp'));
    }
}
