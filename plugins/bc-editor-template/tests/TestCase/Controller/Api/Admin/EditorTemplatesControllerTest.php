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

namespace BcEditorTemplate\Test\TestCase\Controller\Api\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcEditorTemplate\Test\Scenario\EditorTemplatesScenario;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class EditorTemplatesControllerTest
 */
class EditorTemplatesControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BcEditorTemplate.Factory/EditorTemplates',
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
        $this->setFixtureTruncate();
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
    }

    /**
     * Tear Down
     *
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test index
     */
    public function test_index()
    {
        //データを生成
        $this->loadFixtureScenario(EditorTemplatesScenario::class);
        $this->get('/baser/api/admin/bc-editor-template/editor_templates/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        // エディターテンプレート一覧は全て3件が返す
        $this->assertCount(3, $result->editorTemplates);
    }

    /**
     * test view
     */
    public function test_view()
    {
        //データを生成
        $this->loadFixtureScenario(EditorTemplatesScenario::class);
        $this->get('/baser/api/admin/bc-editor-template/editor_templates/view/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('画像（左）とテキスト', $result->editorTemplate->name);

        //存在しないIDを指定の場合、
        $this->get('/baser/api/admin/bc-editor-template/editor_templates/view/10.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //メッセージ内容を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * test add
     */
    public function test_add()
    {
        //データを生成
        $data = [
            'name' => '画像（左）とテキスト',
            'image' => 'template1.gif',
            'description' => 'test description',
            'html' => 'test html'
        ];
        $this->post('/baser/api/admin/bc-editor-template/editor_templates/add.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('エディタテンプレート「画像（左）とテキスト」を追加しました。', $result->message);
        $this->assertEquals('画像（左）とテキスト', $result->editorTemplate->name);

        //テンプレート名を指定しない場合、
        $this->post('/baser/api/admin/bc-editor-template/editor_templates/add.json?token=' . $this->accessToken, ['name' => '']);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('テンプレート名を入力してください。', $result->errors->name->_empty);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->loadFixtureScenario(EditorTemplatesScenario::class);
        $this->post('/baser/api/admin/bc-editor-template/editor_templates/edit/1.json?token=' . $this->accessToken, ['name' => 'name edit']);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('エディタテンプレート「name edit」を更新しました。', $result->message);
        $this->assertEquals('name edit', $result->editorTemplate->name);

        //無効なIDを指定した場合、
        $this->post('/baser/api/admin/bc-editor-template/editor_templates/edit/10.json?token=' . $this->accessToken, ['name' => 'name edit']);
        //ステータスを確認
        $this->assertResponseCode(404);
        //メッセージ内容を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);

        //無効なIDを指定した場合、
        $this->post('/baser/api/admin/bc-editor-template/editor_templates/edit/1.json?token=' . $this->accessToken, ['name' => '']);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('テンプレート名を入力してください。', $result->errors->name->_empty);
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //データを生成
        $this->loadFixtureScenario(EditorTemplatesScenario::class);
        $this->post('/baser/api/admin/bc-editor-template/editor_templates/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('エディタテンプレート「画像（左）とテキスト」を削除しました。', $result->message);
        $this->assertEquals('画像（左）とテキスト', $result->editorTemplate->name);

        //無効なIDを指定した場合、
        $this->post('/baser/api/admin/bc-editor-template/editor_templates/delete/10.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //メッセージ内容を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * test batch
     */
    public function test_list()
    {
        //データを生成
        $this->loadFixtureScenario(EditorTemplatesScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/admin/bc-editor-template/editor_templates/list.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(get_object_vars($result->editorTemplates)[3], 'テキスト２段組');
    }
}
