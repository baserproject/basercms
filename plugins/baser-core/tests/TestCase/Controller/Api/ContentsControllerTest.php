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

namespace BaserCore\Test\TestCase\Controller\Api;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;

class ContentsControllerTest extends \BaserCore\TestSuite\BcTestCase
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
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Sites'
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
        Configure::clear();
        parent::tearDown();
    }

    /**
     * testView
     *
     * @return void
     */
    public function testView(): void
    {
        $this->get('/baser/api/baser-core/contents/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('baserCMSサンプル', $result->content->title);
    }

    /**
     * testViewTrash
     *
     * @return void
     */
    public function testViewTrash(): void
    {
        $this->get('/baser/api/baser-core/contents/viewTrash/16.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('削除済みフォルダー(親)', $result->trash->title);
    }
    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        // indexテスト
        $this->get('/baser/api/baser-core/contents/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('', $result->contents[0]->name);
        // trashテスト
        $this->get('/baser/api/baser-core/contents/index/trash.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->contents[0]->deleted_date);
        // treeテスト
        $this->get('/baser/api/baser-core/contents/index/tree.json?site_id=1&token=' . $this->accessToken);
        $this->assertResponseOk();
        // tableテスト
        $this->get('/baser/api/baser-core/contents/index/table.json?site_id=1&folder_id=6&name=サービス&type=Page&token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(3, count($result->contents));
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        // 子要素を持たない場合
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/api/baser-core/contents/delete/4.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals("コンテンツ: indexを削除しました。", $result->message);
        $this->get('/baser/api/baser-core/contents/view/4.json?token=' . $this->accessToken);
        $this->assertResponseError();
        // 子要素を持つ場合
        $this->post('/baser/api/baser-core/contents/delete/6.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $this->get('/baser/api/baser-core/contents/view/6.json?token=' . $this->accessToken); // 親要素削除チェック
        $this->assertResponseError();
        $this->get('/baser/api/baser-core/contents/view/11.json?token=' . $this->accessToken); // 子要素削除チェック
        $this->assertResponseError();
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDeleteTrash()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/api/baser-core/contents/deleteTrash/16.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals("ゴミ箱: 削除済みフォルダー(親) を削除しました。", $result->message);
        $this->get('/baser/api/baser-core/contents/viewTrash/16.json?token=' . $this->accessToken);
        $this->assertResponseError();
    }

    /**
     * testTrashEmpty
     *
     * @return void
     */
    public function testTrashEmpty()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/api/baser-core/contents/trashEmpty.json?type=ContentFolder&token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals("ゴミ箱: 削除済みフォルダー(親)(ContentFolder)を削除しました。削除済みフォルダー(子)(ContentFolder)を削除しました。", $result->message);
        $this->get('/baser/api/baser-core/contents/index/trash.json?type=ContentFolder&token=' . $this->accessToken);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEmpty($result->contents);
    }
}
