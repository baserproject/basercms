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

namespace BaserCore\Test\TestCase\Model\Table;

use Authentication\AuthenticationService;
use BaserCore\Model\Table\PasswordRequestsTable;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\BcApplication;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Http\ServerRequestFactory;

/**
 * BaserCore\Model\Table\PasswordRequestsTable Test Case
 *
 * @property UsersTable $Users
 */
class PasswordRequestsTableTest extends BcTestCase
{

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
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('PasswordRequests') ? [] : ['className' => 'BaserCore\Model\Table\PasswordRequestsTable'];
        $this->PasswordRequests = $this->getTableLocator()->get('PasswordRequests', $config);

        $config = $this->getTableLocator()->exists('Users') ? [] : ['className' => 'BaserCore\Model\Table\UsersTable'];
        $this->Users = $this->getTableLocator()->get('Users', $config);
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
    public function testValidationDefault() {
        $passwordRequest = $this->PasswordRequests->newEntity([
             'email' => '',
        ]);
        $this->assertSame([
            'email' => ['_empty' => 'Eメールを入力してください。'],
        ] , $passwordRequest->getErrors());

        $passwordRequest = $this->PasswordRequests->newEntity([
             'email' => 'test',
        ]);
        $this->assertSame([
            'email' => ['email' => 'Eメールの形式が不正です。'],
        ] , $passwordRequest->getErrors());

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
        ] , $passwordRequest->getErrors());
    }

    /**
     * Test getEnableRequestData
     */
    public function testGetEnableRequestData() {
        $passwordRequest = $this->PasswordRequests->getEnableRequestData('testkey1');
        $this->assertEquals(3, $passwordRequest->id);
    }

    /**
     * Test updatePassword
     */
    public function testUpdatePassword() {
        // 変更前のパスワードを取得
        $user = $this->Users
            ->find()
            ->where(['id' => 1])
            ->first();
        $beforePassword = $user->password;

        $passwordRequest = $this->PasswordRequests->newEntity(['id' => 3]);
        $this->assertNotEmpty($this->PasswordRequests->updatePassword($passwordRequest, 'test'));

        $passwordRequest = $this->PasswordRequests
            ->find()
            ->where(['id' => 3])
            ->first();
        $this->assertEquals(1, $passwordRequest->used);

        // 変更後のパスワードを取得して比較
        $user = $this->Users
            ->find()
            ->where(['id' => 1])
            ->first();
        $afterPassword = $user->password;

        $this->assertNotEquals($beforePassword, $afterPassword);

        // 変更後のパスワードでログイン
        $user = $this->login(['email' => 'Lorem ipsum dolor sit amet', 'password' => 'test']);
        $this->assertEquals(1, $user->id);
    }

    /**
     * ログイン
     */
    private function login($requestData) {
        $authSetting = Configure::read('BcPrefixAuth.Admin');
        $request = ServerRequestFactory::fromGlobals(
            ['REQUEST_URI' => $authSetting['loginAction']],
            [],
            $requestData
        );
        $fields = [
            'username' => $authSetting['username'],
            'password' => $authSetting['password']
        ];

        $service = new AuthenticationService([
            'identifiers' => [
                'Authentication.Password' => [
                    'fields' => $fields,
                ],
            ],
            'authenticators' => [
                'Authentication.Form' => [
                    'fields' => $fields,
                    'loginUrl' => $authSetting['loginAction'],
                ],
            ],
        ]);

        $result = $service->authenticate($request);
        if (!$result->isValid()) {
            return false;
        }
        return $result->getData();
    }

}
