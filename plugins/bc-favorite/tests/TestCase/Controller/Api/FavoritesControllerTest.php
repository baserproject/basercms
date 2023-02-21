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

namespace BcFavorite\Test\TestCase\Controller\Api;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;

class FavoritesControllerTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BcFavorite.Favorites',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Permissions',
    ];

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
        parent::tearDown();
    }

    /**
     * test View
     */
    public function testView(): void
    {
        $this->get('/baser/api/bc-favorite/favorites/view/2.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('新着情報管理', $result->favorite->name);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/api/bc-favorite/favorites/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('固定ページ管理', $result->favorites[0]->name);
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
            'name' => 'hogehoge',
            'user_id' => '1',
            'url' => '/baser/admin/contents/index',
        ];
        $this->post('/baser/api/bc-favorite/favorites/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $favorites = $this->getTableLocator()->get('BcFavorite.Favorites');
        $query = $favorites->find()->where(['name' => $data['name']]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * testAddWithError
     *
     * @return void
     */
    public function testAddWithError()
    {
        $data = [
            'name' => '',
            'user_id' => '1',
            'url' => '/baser/admin/contents/index',
        ];
        $this->post('/baser/api/bc-favorite/favorites/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseError();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals("タイトルは必須です。", $result->errors->name->_empty);
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = [
            'name' => 'Test_test_Man'
        ];
        $this->post('/baser/api/bc-favorite/favorites/edit/1.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();
        $favorites = $this->getTableLocator()->get('BcFavorite.Favorites');
        $query = $favorites->find()->where(['name' => $data['name']]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/api/bc-favorite/favorites/delete/2.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
        $favorites = $this->getTableLocator()->get('BcFavorite.Favorites');
        $query = $favorites->find()->where(['id' => 2]);
        $this->assertEquals(0, $query->count());
    }

    /**
     * testSave_favorite_box
     *
     * @return void
     */
    public function testSave_favorite_box()
    {
        $this->post('/baser/api/bc-favorite/favorites/save_favorite_box.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $this->assertEquals('', $_SESSION['Baser']['favorite_box_opened']);
        $this->post('/baser/api/bc-favorite/favorites/save_favorite_box/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $this->assertEquals('1', $_SESSION['Baser']['favorite_box_opened']);
        $this->post('/baser/api/bc-favorite/favorites/save_favorite_box/xxxxxxxxxxxxxxxxx.json?token=' . $this->accessToken);
        $this->assertResponseError();
    }
    /**
     * beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] よく使う項目を追加する（AJAX）
     */
    public function testAdmin_ajax_add()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] よく使う項目編集
     */
    public function testAdmin_ajax_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 削除
     */
    public function testAdmin_ajax_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 並び替えを更新する
     */
    public function testAdmin_update_sort()
    {
        $this->post('/baser/api/bc-favorite/favorites/change_sort.json?token=' . $this->accessToken, [
            'id' => 1,
            'offset' => 1
        ]);
        $this->assertResponseOk();
    }
}
