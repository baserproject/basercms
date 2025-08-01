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

namespace BcCustomContent\Test\TestCase\Controller\Admin\Api;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class CustomContentsControllerTest
 */
class CustomContentsControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

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
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/admin/bc-custom-content/custom_contents/index.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(3, $result->customContents);
    }

    /**
     * test add
     */
    public function test_add()
    {
        $data = [
            'custom_table_id' => 1,
            'description' => 'test custom content add',
            'template' => 'template_add',
            'content' => [
                'title' => 'custom content add',
                'site_id' => 1,
                'parent_id' => 0,
                'content' => 'add content'
            ]
        ];
        //APIを呼ぶ
        $this->post('/baser/api/admin/bc-custom-content/custom_contents/add.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('カスタムコンテンツ「custom content add」を追加しました。', $result->message);
        $this->assertNotNull($result->customContent);
        $this->assertNotNull($result->content);

        //コンテンツを指定しない場合、
        $data = [
            'custom_table_id' => 1,
            'description' => 'test custom content add',
            'template' => 'template_add'
        ];
        //APIを呼ぶ
        $this->post('/baser/api/admin/bc-custom-content/custom_contents/add.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('関連するコンテンツがありません', $result->errors->content->_required);
    }

    /**
     * test view
     */
    public function test_view()
    {
        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/admin/bc-custom-content/custom_contents/view/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($result->customContent->description, 'サービステスト');
        $this->assertEquals($result->customContent->content->url, '/test/');

        //エラーを発生した時の確認
        $this->get('/baser/api/admin/bc-custom-content/custom_contents/view/10.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $data = [
            'custom_table_id' => 1,
            'description' => 'test custom content change',
            'template' => 'template_change',
            'list_count' => 1,
            'content' => [
                'title' => 'custom content change'
            ]
        ];
        $this->post('/baser/api/admin/bc-custom-content/custom_contents/edit/1.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('カスタムコンテンツ「custom content change」を更新しました。', $result->message);
        $this->assertEquals('test custom content change', $result->customContent->description);
        $this->assertEquals('custom content change', $result->content->title);

        //無効なIDを指定した場合、
        $this->post('/baser/api/admin/bc-custom-content/custom_contents/edit/11.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseCode(404);
        //メッセージ内容を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);

        //無効なIDを指定した場合、
        $this->post('/baser/api/admin/bc-custom-content/custom_contents/edit/1.json?token=' . $this->accessToken, [
            'custom_table_id' => 1,
            'list_count' => 'abc'
        ]);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('一覧表示件数は100までの数値で入力してください。', $result->errors->list_count->range);
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->loadFixtureScenario(CustomContentsScenario::class);

        $this->post('/baser/api/admin/bc-custom-content/custom_contents/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('カスタムコンテンツ「サービスタイトル」を削除しました。', $result->message);
        $this->assertNotNull($result->customContent);
        $this->assertNotNull($result->content);

        //無効なIDを指定した場合、
        $this->post('/baser/api/admin/bc-custom-content/custom_contents/delete/11.json?token=' . $this->accessToken);
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
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/admin/bc-custom-content/custom_contents/list.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(get_object_vars($result->customContents)[1], 'サービスタイトル');
        $this->assertEquals(get_object_vars($result->customContents)[2], '求人タイトル');
    }
}
