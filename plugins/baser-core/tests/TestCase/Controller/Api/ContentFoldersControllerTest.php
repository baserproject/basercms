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

use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\Service\ContentFolderService;

/**
 * BaserCore\Controller\Api\ContentFoldersController Test Case
 */
class ContentFoldersControllerTest extends BcTestCase
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
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
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
        $this->ContentFolderService = new ContentFolderService();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/api/baser-core/content_folders/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals("フォルダーテンプレート1", $result->contentFolders[0]->folder_template);
    }

    /**
     * Test View
     */
    public function testView()
    {
        $this->get('/baser/api/baser-core/content_folders/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals("フォルダーテンプレート1", $result->contentFolders->folder_template);
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
            'folder_template' => 'テストcreate',
            'content' => [
                "parent_id" => "1",
                "title" => "新しい フォルダー",
                "plugin" => 'BaserCore',
                "type" => "ContentFolder",
                "site_id" => "0",
                "alias_id" => "",
                "entity_id" => "",
            ]
        ];
        $this->post('/baser/api/baser-core/content_folders/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $ContentFolders = $this->getTableLocator()->get('ContentFolders');
        $query = $ContentFolders->find()->where(['folder_template' => $data['folder_template']]);
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
        $this->delete('/baser/api/baser-core/content_folders/delete/1.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
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
        $data = $this->ContentFolderService->getIndex(['folder_template' => "testEdit"])->first();
        $data->content->name = "contentFolderTestUpdate";
        $id = $data->id;
        $this->post("/baser/api/baser-core/content_folders/edit/${id}.json?token=". $this->accessToken, $data->toArray());
        $this->assertResponseSuccess();
        $query = $this->ContentFolderService->getIndex(['folder_template' => $data['folder_template']]);
        $this->assertEquals(1, $query->all()->count());
        $this->assertEquals("contentFolderTestUpdate", $query->all()->first()->content->name);
    }
}
