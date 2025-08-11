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

use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Scenario\SuspendedUsersScenario;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\Test\Scenario\UserScenario;
use BaserCore\Test\Scenario\UsersUserGroupsScenario;
use Cake\Http\Response;
use Cake\Routing\Router;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\UsersService;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Table\LoginStoresTable;
use BaserCore\Utility\BcUtil;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use DateTime;

/**
 * Class UsersServiceTest
 * @property UsersService $Users
 */
class UsersServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * @var UsersService|null
     */
    public $Users = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(UserScenario::class);
        $this->loadFixtureScenario(UserGroupsScenario::class);
        $this->loadFixtureScenario(UsersUserGroupsScenario::class);
        $this->Users = new UsersService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Users);
        Router::reload();
        parent::tearDown();
    }

    /**
     * test construct
     * @return void
     */
    public function testConstruct(){
        $this->assertTrue(isset($this->Users->Users));
        $this->assertTrue(isset($this->Users->LoginStores));
    }

    /**
     * Test getNew
     */
    public function testGetNew()
    {
        $this->assertEquals(1, $this->Users->getNew()->user_groups[0]->id);
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $user = $this->Users->get(1);
        $this->assertEquals('baser admin', $user->name);
    }

    /**
     * Test getIndex
     */
    public function testGetIndex()
    {
        $request = $this->getRequest('/');

        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals('baser admin', $users->first()->name);

        $request = $this->getRequest('/?user_group_id=2');
        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals('baser operator', $users->first()->name);

        $request = $this->getRequest('/?name=baser');
        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals(3, $users->all()->count());

        $request = $this->getRequest('/?real_name=operator');
        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals(1, $users->all()->count());

        $request = $this->getRequest('/?limit=1');
        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals(1, $users->all()->count());
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $request = $this->getRequest('/');
        $this->loginAdmin($request);
        $request = $request->withParsedBody([
            'name' => 'ucmitz',
            'user_groups' => [
                '_ids' => [1]
            ],
            'email' => 'example@example.com',
            'real_name_1' => 'test',
            'password_1' => 'Testtest1234',
            'password_2' => 'Testtest1234'
        ]);
        $request = $request->withData('password', $request->getData('password_1'));
        $this->Users->create($request->getData());
        $request = $this->getRequest('/?name=ucmitz');
        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals(1, $users->all()->count());
    }

    /**
     * Test update
     */
    public function testUpdate()
    {
        $data = [
            'name' => 'ucmitz',
            'user_groups' => ['_ids' => [2]]
        ];
        $request = $this->loginAdmin($this->getRequest('/')->withParsedBody($data));
        $user = $this->Users->get(2);
        Router::setRequest($request);
        $this->Users->update($user, $request->getData());
        $request = $this->getRequest('/?name=ucmitz');
        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals(1, $users->all()->count());
    }

    /**
     * test checkUpdatePassword
     */
    public function testUpdatePassword()
    {
        $user = $this->Users->get(1);
        $beforePassword = $user->password;
        $beforeName = $user->name;
        $this->Users->updatePassword($user, [
            'password' => 'Testtest1234',
            'password_1' => 'Testtest1234',
            'password_2' => 'Testtest1234',
            'name' => 'new_name',
        ]);
        $this->assertEmpty($user->getErrors());
        $this->assertNotEquals($beforePassword, $user->password);
        $this->assertEquals($beforeName, $user->name);

        try {
            $this->Users->updatePassword($user, [
                'password' => '1',
                'password_1' => '2',
                'password_2' => '3',
            ]);
            throw new \Exception();
        } catch (\Exception $e) {
            $this->assertSame('Cake\ORM\Exception\PersistenceFailedException', get_class($e));
        }
    }

    /**
     * Test delete
     */
    public function testDelete()
    {
        $request = $this->getRequest('/');
        $this->loginAdmin($request);
        $this->Users->delete(3);
        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals(2, $users->all()->count());
    }

    /**
     * Test Last Admin Delete
     */
    public function testLastAdminDelete()
    {
        $this->loginAdmin($this->getRequest());
        $this->expectException("BaserCore\Error\BcException");
        $this->Users->delete(1);
    }

    /**
     * test Login
     */
    public function testLoginAndLogout()
    {
        $request = $this->getRequest('/baser/admin/baser-core/users/index');
        $authentication = $this->BaserCore->getAuthenticationService($request);
        $request = $request->withAttribute('authentication', $authentication);
        $response = new Response();
        $request = $this->Users->login($request, $response, 1)['request'];
        $this->assertEquals(1, $request->getAttribute('identity')->id);
        $this->assertEquals(1, $request->getSession()->read('AuthAdmin')->id);
        $this->Users->logout($request, $response, 1);
        $this->assertNull($request->getSession()->read('AuthAdmin'));
    }

    /**
     * test reLogin
     */
    public function testReLogin()
    {
        $request = $this->loginAdmin($this->getRequest('/baser/admin/baser-core/users/index'));
        $this->Users->update($request->getAttribute('identity')->getOriginalData(), ['name' => 'test']);
        $request = $this->Users->reLogin($request, new Response())['request'];
        $this->assertEquals('test', $request->getAttribute('identity')->name);
    }

    /**
     * test setCookieAutoLoginKey
     */
    public function testSetCookieAutoLoginKey()
    {
        $response = $this->Users->setCookieAutoLoginKey(new Response(), 1);
        $cookie = $response->getCookie(LoginStoresTable::KEY_NAME);
        $this->assertNotEmpty($cookie['value']);
    }

    /**
     * test checkAutoLogin
     */
    public function testCheckAutoLogin()
    {
        $response = $this->Users->setCookieAutoLoginKey(new Response(), 1);
        $request = $this->getRequest('/baser/admin/users/');
        $beforeCookie = $response->getCookie(LoginStoresTable::KEY_NAME);
        $request = $request->withCookieParams([LoginStoresTable::KEY_NAME => $beforeCookie['value']]);
        $result = $this->Users->checkAutoLogin($request, $response);
        $response = $this->Users->setCookieAutoLoginKey(new Response(), $result->id);
        $afterCookie = $response->getCookie(LoginStoresTable::KEY_NAME);
        $this->assertNotEmpty($afterCookie['value']);
        $this->assertNotEquals($beforeCookie['value'], $afterCookie['value']);
    }

    /**
     * test checkPasswordModified
     */
    public function testCheckPasswordModified()
    {
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $request = $this->getRequest();
        $user = $this->Users->get(1);

        $siteConfigsService->setValue('password_reset_days', 3);
        $user->password_modified = new DateTime('-5 days');
        $this->assertFalse($this->Users->checkPasswordModified($request, $user));
        $user->password_modified = new DateTime('-1 days');
        $this->assertTrue($this->Users->checkPasswordModified($request, $user));

        $siteConfigsService->setValue('password_reset_days', 0);
        $user->password_modified = new DateTime('-5 days');
        $this->assertTrue($this->Users->checkPasswordModified($request, $user));
        $user->password_modified = new DateTime('-1 days');
        $this->assertTrue($this->Users->checkPasswordModified($request, $user));
    }

    /**
     * test loginToAgent
     */
    public function testLoginToAgentAndReturnLoginUserFromAgent()
    {
        $request = $this->loginAdmin($this->getRequest('/baser/admin/baser-core/users/'));
        $response = new Response();
        $this->Users->loginToAgent($request, $response, 2);
        $this->assertSession(1, 'AuthAgent.User.id');
        $this->assertSession(2, 'AuthAdmin.id');
        $this->Users->returnLoginUserFromAgent($request, $response);
        $this->assertSession(null, 'AuthAgent.User.id');
        $this->assertSession(1, 'AuthAdmin.id');
    }

    /**
     * test reload
     *
     * @return void
     */
    public function testReload()
    {
        // 未ログイン
        $request = $this->loginAdmin($this->getRequest('/baser/admin/baser-core/users/index'));
        $noLoginUser = $this->Users->reload($request);
        $this->assertTrue($noLoginUser);

        $authentication = $this->BaserCore->getAuthenticationService($request);
        $request = $request->withAttribute('authentication', $authentication);
        $response = new Response();
        $request = $this->Users->login($request, $response, 1)['request'];

        // 通常読込
        $users = $this->getTableLocator()->get('Users');
        $user = $users->get(1);
        $user->name = 'modified name';
        $users->save($user);
        $this->Users->reload($request);
        $this->assertSession('modified name', 'AuthAdmin.name');

        // 削除
        $users->delete($user);
        $this->Users->reload($request);
        $this->assertSessionNotHasKey('AuthAdmin');
    }

    /**
     * test fixtureFactorySample
     * フィクスチャファクトリを利用したサンプル
     * フィクスチャファクトリが導入でき、`$this->loadFixtures()` を廃止できたら削除する
     */
    public function testFixtureFactorySample()
    {
        // ファクトリのメソッドを呼び出し無効ユーザーを100人追加
        UserFactory::make(100)->suspended()->persist();
        $this->assertEquals(103, UserFactory::find()->count());
        // シナリオを利用して無効ユーザーを50人追加
        $this->loadFixtureScenario(SuspendedUsersScenario::class, 50);
        $this->assertEquals(153, UserFactory::find()->count());
    }

    /**
     * test isAvailable
     */
    public function testIsAvailable()
    {
        $this->getRequest('/baser/admin');
        $this->assertTrue($this->Users->isAvailable(1));
        $this->assertFalse($this->Users->isAvailable(3));
    }

    /**
     * test createIndexConditions
     * UsersService::createIndexConditions の SQL条件生成を直接検証
     */
    public function test_createIndexConditions()
    {
        $query = $this->Users->Users->find();
        // user_group_id
        $result = $this->execPrivateMethod($this->Users, 'createIndexConditions', [$query, ['user_group_id' => 2]]);
        $this->assertStringContainsString('INNER JOIN user_groups UserGroups', $result->sql());
        $this->assertStringContainsString('UserGroups.id = :c0', $result->sql());
        $binder = $result->getValueBinder();
        $params = $binder->bindings();
        $found = false;
        foreach ($params as $param) {
            if ($param['value'] === 2) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'バインド値 2 が存在すること');

        // name
        $result = $this->execPrivateMethod($this->Users, 'createIndexConditions', [$query, ['name' => 'admin']]);
        $this->assertStringContainsString('name LIKE', $result->sql());
        $binder = $result->getValueBinder();
        $params = $binder->bindings();
        $found = false;
        foreach ($params as $param) {
            if ($param['value'] === '%admin%') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'バインド値 %admin% が存在すること');

        // email
        $result = $this->execPrivateMethod($this->Users, 'createIndexConditions', [$query, ['email' => 'test@example.com']]);
        $this->assertStringContainsString('email LIKE', $result->sql());
        $binder = $result->getValueBinder();
        $params = $binder->bindings();
        $found = false;
        foreach ($params as $param) {
            if ($param['value'] === '%test@example.com%') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'バインド値 %test@example.com% が存在すること');

        // real_name
        $result = $this->execPrivateMethod($this->Users, 'createIndexConditions', [$query, ['real_name' => '山田']]);
        $this->assertStringContainsString('real_name_1 LIKE', $result->sql());
        $this->assertStringContainsString('real_name_2 LIKE', $result->sql());
        $binder = $result->getValueBinder();
        $params = $binder->bindings();
        $found = false;
        foreach ($params as $param) {
            if ($param['value'] === '%山田%') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'バインド値 %山田% が存在すること');
    }

}
