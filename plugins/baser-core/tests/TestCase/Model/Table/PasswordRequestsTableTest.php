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

use BaserCore\Model\Table\PasswordRequestsTable;
use BaserCore\Model\Table\UsersTable;
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BaserCore\Model\Table\PasswordRequestsTable Test Case
 *
 * @property UsersTable $Users
 */
class PasswordRequestsTableTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * @var PasswordRequestsTable
     */
    public $PasswordRequests;

    /**
     * @var Users
     */
    public $Users;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.PasswordRequests',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Sites',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PasswordRequests = $this->getTableLocator()->get('BaserCore.PasswordRequests');
        $this->Users = $this->getTableLocator()->get('BaserCore.Users');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PasswordRequests);
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertTrue($this->PasswordRequests->hasBehavior('Timestamp'));
    }

    /**
     * Test validationDefault
     */
    public function testValidationDefault()
    {
        $passwordRequest = $this->PasswordRequests->newEntity([
            'email' => '',
        ]);
        $this->assertSame([
            'email' => ['_empty' => 'Eメールを入力してください。'],
        ], $passwordRequest->getErrors());

        $passwordRequest = $this->PasswordRequests->newEntity([
            'email' => 'test',
        ]);
        $this->assertSame([
            'email' => ['email' => 'Eメールの形式が不正です。'],
        ], $passwordRequest->getErrors());

        $passwordRequest = $this->PasswordRequests->newEntity([
            'email' => 'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest'
                . 'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest'
                . 'testtesttesttesttesttesttesttesttesttesttesttesttesttesttestesttesttestttest',
        ]);
        $this->assertSame([
            'email' => [
                'email' => 'Eメールの形式が不正です。',
                'maxLength' => 'Eメールは255文字以内で入力してください。',
            ],
        ], $passwordRequest->getErrors());
    }

}
