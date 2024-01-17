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

namespace BaserCore\Test\TestCase\Controller\Api\Admin;

use BaserCore\Service\ContentFoldersService;
use BaserCore\Test\Scenario\ContentFoldersScenario;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BaserCore\Controller\Api\ContentFoldersController Test Case
 */
class ContentFoldersControllerTest extends BcTestCase
{
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

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
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(ContentFoldersScenario::class);
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
        $this->ContentFoldersService = new ContentFoldersService();
    }

    /**
     * test index
     */
    public function test_index()
    {
        //準備
        $this->loginAdmin($this->getRequest());
        //正常系実行
        $this->get('/baser/api/admin/baser-core/content_folders/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(10, $result->contentFolders);
    }


    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->loginAdmin($this->getRequest());
        $data = [
            'folder_template' => 'テストcreate',
            'content' => [
                "parent_id" => "1",
                "title" => "新しい フォルダー",
                "plugin" => 'BaserCore',
                "type" => "ContentFolder",
                "site_id" => "1",
                "alias_id" => "",
                "entity_id" => "",
            ]
        ];
        $this->post('/baser/api/admin/baser-core/content_folders/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $ContentFolders = $this->getTableLocator()->get('ContentFolders');
        $query = $ContentFolders->find()->where(['folder_template' => $data['folder_template']]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * test view
     */
    public function test_view()
    {
        //準備
        $this->loginAdmin($this->getRequest());
        //正常系実行
        $this->get('/baser/api/admin/baser-core/content_folders/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('baserCMSサンプル', $result->contentFolder->folder_template);
        $this->assertEquals(1, $result->content->id);
        //異常系実行
        //存在しないIDを指定した場合、
        $this->get('/baser/api/admin/baser-core/content_folders/view/99.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }


    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->delete('/baser/api/admin/baser-core/content_folders/delete/1.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $data = $this->ContentFoldersService->getIndex(['folder_template' => "testEdit"])->first();
        $data->content->name = "contentFolderTestUpdate";
        $id = $data->id;
        $this->post("/baser/api/admin/baser-core/content_folders/edit/{$id}.json?token=" . $this->accessToken, $data->toArray());
        $this->assertResponseSuccess();
        $query = $this->ContentFoldersService->getIndex(['folder_template' => $data['folder_template']]);
        $this->assertEquals(1, $query->all()->count());
        $this->assertEquals("contentFolderTestUpdate", $query->all()->first()->content->name);
    }
}
