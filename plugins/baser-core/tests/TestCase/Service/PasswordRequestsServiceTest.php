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

use BaserCore\Service\PasswordRequestsService;
use BaserCore\Service\PasswordRequestsServiceInterface;
use BaserCore\Test\Factory\PasswordRequestFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PasswordRequestsServiceTest
 * @property PasswordRequestsService $service
 */
class PasswordRequestsServiceTest extends BcTestCase
{

    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/PasswordRequests'
    ];

    /**
     * Trait
     */
    use BcContainerTrait;
    use ScenarioAwareTrait;

    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->service = $this->getService(PasswordRequestsServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->service);
    }

    /**
     * Test getEnableRequestData
     */
    public function testGetEnableRequestData()
    {
        PasswordRequestFactory::make(['id' => 3, 'user_id' => 1, 'request_key' => 'testkey1'])->persist();
        $passwordRequest = $this->service->getEnableRequestData('testkey1');
        $this->assertEquals(3, $passwordRequest->id);
    }

    /**
     * Test updatePassword
     */
    public function testUpdatePassword()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        PasswordRequestFactory::make(['id' => 3, 'user_id' => 1, 'request_key' => 'testkey1'])->persist();
        $usersTable = $this->getTableLocator()->get('BaserCore.Users');
        // 変更前のパスワードを取得
        $user = $usersTable
            ->find()
            ->where(['id' => 1])
            ->first();
        $beforePassword = $user->password;

        $passwordRequest = $this->service->get(3);
        $this->assertNotEmpty($this->service->updatePassword($passwordRequest, [
            'password_1' => 'testtest',
            'password_2' => 'testtest'
        ]));

        $passwordRequest = $this->service->PasswordRequests
            ->find()
            ->where(['id' => 3])
            ->first();
        $this->assertEquals(1, $passwordRequest->used);

        // 変更後のパスワードを取得して比較
        $user = $usersTable
            ->find()
            ->where(['id' => 1])
            ->first();
        $afterPassword = $user->password;

        $this->assertNotEquals($beforePassword, $afterPassword);

        // 変更後のパスワードでログインw
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post(Configure::read('BcPrefixAuth.Admin.loginAction'), [
            'email' => 'admin@example.com',
            'password' => 'testtest'
        ]);
        $this->assertSession(1, 'AuthAdmin.id');
    }

    /**
     * test getNew
     */
    public function testGetNew()
    {
        $this->assertEmpty($this->service->getNew()->toArray());
    }

    /**
     * test update
     */
    public function testUpdate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test get
     */
    public function testGet()
    {
        PasswordRequestFactory::make(['id' => 1])->persist();
        $this->assertEquals(1, $this->service->get(1)->id);
    }
}
