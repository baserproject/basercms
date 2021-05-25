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

namespace BaserCore\Test\TestCase\Model\Entity;

use BaserCore\Model\Entity\User;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class UserTest
 * @package BaserCore\Test\TestCase\Model\Entity
 */
class UserTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
    ];

    /**
     * @var User
     */
    public $User;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Users')? [] : ['className' => 'BaserCore\Model\Table\UsersTable'];
        $this->User = $this->getTableLocator()->get('Users', $config)->get(1, ['contain' => 'UserGroups']);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->User);
        parent::tearDown();
    }

    /**
     * Test _setPassword
     */
    public function testSetPassword()
    {
        $this->User->set('password', 'testtest');
        $this->assertNotEquals('testtest', $this->User->password);
    }

    /**
     * Test isAdmin
     */
    public function testIsAdmin()
    {
        $this->assertTrue($this->User->isAdmin());
    }

    /**
     * ユーザー名を整形して表示する
     * @param string $nickname
     * @param string $realName1
     * @param string $realName2
     * @param string $expect
     * @return void
     * @dataProvider getUserNameDataProvider
     */
    public function testGetDisplayName($nickname, $realName1, $realName2, $expect)
    {
        $userTable = $this->getTableLocator()->get('Users');
        $user = $userTable->newEntity([
            'nickname' => $nickname,
            'real_name_1' => $realName1,
            'real_name_2' => $realName2,
        ]);
        $result = $user->getDisplayName();
        $this->assertEquals($expect, $result);
    }

    public function getUserNameDataProvider()
    {
        return [
            ['aiueo', 'yamada', 'tarou', 'aiueo'],
            ['', 'yamada', 'tarou', 'yamada tarou'],
            ['', '', '', ''],
        ];
    }
}
