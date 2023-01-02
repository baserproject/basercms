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

namespace BcWidgetArea\Test\TestCase\Controller\Api;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcWidgetArea\Test\Factory\WidgetAreaFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class WidgetAreasControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

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
        'plugin.BcWidgetArea.Factory/WidgetAreas',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $token = $this->apiLoginAdmin();
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * [API] 一覧取得
     */
    public function testIndex()
    {
        // テストデータを作る
        WidgetAreaFactory::make(['id' => 1, 'name' => 'test', 'widgets' => 'test'])->persist();

        // ウィジェットエリア一覧のAPIを叩く
        $this->get("/baser/api/bc-widget-area/widget_areas/index.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // レスポンスのウィジェットエリアデータを確認する
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotEmpty($result->widgetAreas);
    }

    /**
     * [API] 新規追加
     */
    public function testAdd()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] 編集
     */
    public function testEdit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] 削除
     */
    public function testDelete()
    {
        // テストデータを生成
        WidgetAreaFactory::make([
            'id' => 1,
            'name' => 'test',
            'widgets' => serialize([
                [
                    1 => 'test 1',
                    2 => 'test 2'
                ]
            ])
        ])->persist();

        //APIを呼ぶ
        $this->post("/baser/api/bc-widget-area/widget_areas/delete/1.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        //メッセージを確認
        $this->assertEquals('ウィジェットエリア「test」を削除しました。', $result->message);
        //ウィジェットエリアの変化のを確認
        $this->assertNotEmpty($result->widgetArea);
        //削除したウィジェットエリアが存在するかどうかを確認
        $this->assertEquals(0, WidgetAreaFactory::count());

        //存在しないウィジェットエリアを削除の場合、
        //APIを呼ぶ
        $this->post("/baser/api/bc-widget-area/widget_areas/delete/1.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseCode(400);
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        //メッセージを確認
        $this->assertEquals('データベース処理中にエラーが発生しました。Record not found in table "widget_areas"', $result->message);
    }

    /**
     * [API] 一括処理
     */
    public function testBatch()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] ウィジェットエリア名を更新
     */
    public function testUpdate_title()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] ウィジェット名を更新
     */
    public function testUpdate_widget()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] ウィジェットの並び替え
     */
    public function testUpdate_sort()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] ウィジェットを削除
     */
    public function testDelete_widget()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] 単一データ取得のテスト
     */
    public function test_view()
    {
        // テストデータを作る
        WidgetAreaFactory::make([
            'id' => 1,
            'name' => 'test',
            'widgets' => serialize([
                [
                    1 => 'test 1',
                    2 => 'test 2'
                ]
            ])
        ])->persist();
        //APIを呼ぶ
        $this->get('/baser/api/bc-widget-area/widget_areas/view/1.json?token=' . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotEmpty($result->widgetArea);
    }
}
