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

namespace BaserCore\Test\TestCase\Controller\Api;

use BaserCore\Service\PermissionsService;
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BaserCore\Controller\Api\PermissionsController Test Case
 */
class PermissionsControllerTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Dblogs'
    ];

    /**
     * autoFixtures
     * @var bool
     */
    public $autoFixtures = false;

    /**
     * Access Token
     * @var string
     */
    public $accessToken = null;

    /**
     * Refresh Token
     * @var null
     */
    public $refreshToken = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures(
            'Users',
            'UsersUserGroups',
            'UserGroups',
            'Permissions',
            'Sites',
            'SiteConfigs'
        );
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        Configure::clear();
        parent::tearDown();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->post('/baser/api/baser-core/permissions/index/1.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $this->get('/baser/api/baser-core/permissions/index/2.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(15, count($result->permissions));
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = [
            'no' => 1,
            'sort' => 2,
            'name' => 'test',
            'user_group_id' => 2,
            'url' => '/baser/admin/baser-core/contents/index',
            'auth' => true,
            'method' => 'ALL',
            'status' => true,
            'modified' => time(),
            'created' => time(),
        ];
        $this->post('/baser/api/baser-core/permissions/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();
        $table = $this->getTableLocator()->get('BaserCore.Permissions');
        $query = $table->find()->where(['name' => $data['name']]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $permissionsService = new PermissionsService();
        $data = $permissionsService->getIndex(['name' => 'システム管理'])->first();
        $data->name = "システム管理 Update";
        $id = $data->id;

        $this->post("/baser/api/baser-core/permissions/edit/${id}.json?token=". $this->accessToken, $data->toArray());
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $permission = $permissionsService->getIndex(['id' => $id])->first();
        $this->assertEquals($result->permission->name, $permission->name);
        $this->assertEquals('アクセスルール「システム管理 Update」を更新しました。', $result->message);


        $dataError["test"] = "システム管理 Update";

        $this->post("/baser/api/baser-core/permissions/edit/1.json?token=". $this->accessToken, $dataError);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $permissionsService = new PermissionsService();
        $newPermission = [
            'no' => 10,
            'sort' => 20,
            'name' => 'test delete',
            'user_group_id' => 2,
            'url' => '/baser/admin/baser-core/contents/index',
            'auth' => true,
            'method' => 'ALL',
            'status' => true,
            'modified' => time(),
            'created' => time(),
        ];
        $newPermission = $permissionsService->create($newPermission);

        $id = $newPermission->id;
        $data = $permissionsService->get($id);


        $this->post("/baser/api/baser-core/permissions/delete/{$id}.json?token=" . $this->accessToken);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($result->permission->name, $data->name);
        $this->assertEquals('アクセスルール「test delete」を削除しました。', $result->message);

        $this->post("/baser/api/baser-core/permissions/delete/2222.json?token=" . $this->accessToken);
        $this->assertResponseCode(404);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * Test View
     */
    public function testView()
    {
        $this->get('/baser/api/baser-core/permissions/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('システム管理', $result->permission->name);
        $this->assertEquals('2', $result->permission->user_group_id);
        $this->assertEquals('/baser/admin/*', $result->permission->url);
    }

    /**
     * test copy
     * @return void
     */
    public function testCopy()
    {
        $this->get('/baser/api/baser-core/permissions/copy/1.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $this->post('/baser/api/baser-core/permissions/copy/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('アクセスルール「システム管理」をコピーしました。', $result->message);
        $this->assertEquals('システム管理', $result->permission->name);

        $this->post('/baser/api/baser-core/permissions/copy/2222.json?token=' . $this->accessToken);
        $this->assertResponseCode(404);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }


    /**
     * 表示順変更
     */
    public function testUpdate_sort()
    {
        $this->post('/baser/api/baser-core/permissions/update_sort/2.json?token=' . $this->accessToken);
        $this->assertResponseFailure();

        $data = [
            'id' => 1,
            'offset' => 2
        ];
        $permissions = $this->getTableLocator()->get('Permissions');
        $permissionList = $permissions
            ->find()
            ->order(['sort' => 'ASC'])
            ->select('id')
            ->limit(3)
            ->all();
        $beforeOrderId = [];
        foreach($permissionList as $permission) {
            $beforeOrderId[] = $permission->id;
        }
        $this->post('/baser/api/baser-core/permissions/update_sort/2.json?token=' . $this->accessToken, $data);
        $permissionList = $permissions
            ->find()
            ->order(['sort' => 'ASC'])
            ->select('id')
            ->limit(3)
            ->all();

        $afterOrderId = [];
        foreach($permissionList as $permission) {
            $afterOrderId[] = $permission->id;
        }
        $this->assertNotEquals($beforeOrderId, $afterOrderId);
    }

    /**
     * 一括処理
     *
     */
    public function testBatch()
    {
        $permissions = $this->getTableLocator()->get('Permissions');

        // 空データ送信
        $this->post('/baser/api/baser-core/permissions/batch.json?token=' . $this->accessToken, []);
        $this->assertResponseFailure();

        // unpublish
        $data = [
            'batch' => 'unpublish',
            'batch_targets' => [1],
        ];
        $this->post('/baser/api/baser-core/permissions/batch.json?token=' . $this->accessToken, $data);
        $this->assertResponseNotEmpty();

        $permission = $permissions->find()->where(['id' => 1])->all()->last();
        $this->assertFalse($permission->status);

        // publish
        $data = [
            'batch' => 'publish',
            'batch_targets' => [1],
        ];
        $this->post('/baser/api/baser-core/permissions/batch.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();

        $permission = $permissions->find()->where(['id' => 1])->all()->last();
        $this->assertTrue($permission->status);

        // delete
        $data = [
            'batch' => 'delete',
            'batch_targets' => [1],
        ];
        $this->post('/baser/api/baser-core/permissions/batch.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();

        $permission = $permissions->find()->where(['id' => 1])->all()->last();
        $this->assertNull($permission);
    }

}
