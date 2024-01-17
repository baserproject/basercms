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

namespace BcMail\Test\TestCase\Controller\Api\Admin;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Service\MailFieldsServiceInterface;
use BcMail\Test\Factory\MailFieldsFactory;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Test\Scenario\MailFieldsScenario;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MailFieldsControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;
    use BcContainerTrait;

    /**
     * set up
     *
     * @return void
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
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * [API] メールフィールド API リスト取得
     */
    public function testList()
    {
        //データを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        //APIを呼ぶ
        $this->get("/baser/api/admin/bc-mail/mail_fields/list/1.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->mailFields);
    }

    /**
     * [API] メールフィールド API 新規追加
     */
    public function testAdd()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(10);
        //データを生成
        $data = [
            'mail_content_id' => 10,
            'field_name' => 'name_add_1',
            'type' => 'text',
            'name' => '性',
            'head' => 'お名前',
            'valid' => 0,
            'not_empty' => 0,
            'valid_ex' => null,
            'attention' => 1,
            'before_attachment' => 1,
            'after_attachment' => 1,
            'description' => 'test',
            'source' => '',
            'size' => null,
            'text_rows' => null,
            'maxlength' => 255,
            'group_field' => 1,
            'group_valid' => 1,
            'options' => 1,
            'class' => 1,
            'default_value' => 1,
            'auto_convert' => null,
            'use_field' => 1,
            'no_send' => 0,
        ];
        //APIを呼ぶ
        $this->post("/baser/api/admin/bc-mail/mail_fields/add.json?token=" . $this->accessToken, $data);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->mailField);
        $this->assertEquals($result->message, '新規メールフィールド「性」を追加しました。');

        //テストデータベースを削除
        $MailMessagesService->dropTable(10);
    }

    /**
     * [API] メールフィールド API 編集
     */
    public function testEdit()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        //データを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $data = ['name' => 'name_edited', 'source' => '', 'valid_ex' => '', 'type' => 'text'];
        //APIを呼ぶ
        $this->post("/baser/api/admin/bc-mail/mail_fields/edit/1.json?token=" . $this->accessToken, $data);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($result->mailField->name, 'name_edited');
        $this->assertEquals($result->message, 'メールフィールド「name_edited」を更新しました。');

        //エラーを発生した場合、
        //データを生成
        $data = ['name' => '', 'source' => '', 'valid_ex' => '', 'type' => 'text'];
        //APIを呼ぶ
        $this->post("/baser/api/admin/bc-mail/mail_fields/edit/1.json?token=" . $this->accessToken, $data);
        // レスポンスコードを確認する
        $this->assertResponseCode(400);
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        //メッセージを確認、
        $this->assertEquals($result->message, '入力エラーです。内容を修正してください。');
        //エラー内容を確認、
        $this->assertEquals($result->errors->name->_empty, '項目名を入力してください。');
    }

    /**
     * [API] メールフィールド API 削除
     */
    public function testDelete()
    {
        //データを生成
        //メールメッセージサービスをコル
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        //メールメッセージフィルドを追加
        $MailMessagesService->addMessageField(1, 'name_1');
        //メールフィルドのデータを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        //APIを呼ぶ
        $this->post("/baser/api/admin/bc-mail/mail_fields/delete/1.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->mailField);
        $this->assertEquals($result->message, 'メールフィールド「性」を削除しました。');
    }

    /**
     * [API] メールフィールド API 削除
     */
    public function testCopy()
    {
        //データを生成
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);

        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $BcDatabaseService->addColumn('mail_message_1', 'name_1', 'text');
        //メールメッセージサービスをコル
        $MailFieldsService = $this->getService(MailFieldsServiceInterface::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        //メールフィルドのデータを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        //APIを呼ぶ
        $this->post("/baser/api/admin/bc-mail/mail_fields/copy/1/1.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->mailField);
        $this->assertEquals($result->message, 'メールフィールド「性」をコピーしました。');
        //メールフィルドがコピーできるか確認
        $mailFields = $MailFieldsService->getIndex(1)->toArray();
        $this->assertEquals('性_copy', $mailFields[count($mailFields) - 1]['name']);

        //不要テーブルを削除
        $MailMessagesService->dropTable(1);
    }

    /**
     * [API] メールフィールドのバッチ処理
     */
    public function testBatch()
    {
        //データを生成
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $BcDatabaseService->addColumn('mail_message_1', 'name_1', 'text');
        $data = [
            'batch' => 'delete',
            'batch_targets' => [1],
        ];
        //APIを呼ぶ
        $this->post("/baser/api/admin/bc-mail/mail_fields/batch.json?token=" . $this->accessToken, $data);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($result->message, '一括処理が完了しました。');

        // DBログに保存するかどうか確認する
        $dbLogService = $this->getService(DblogsServiceInterface::class);
        $dbLog = $dbLogService->getDblogs(1)->toArray()[0];
        $this->assertEquals('メールフィールド「性」を 削除 しました。', $dbLog->message);
        $this->assertEquals(1, $dbLog->id);
        $this->assertEquals('MailFields', $dbLog->controller);
        $this->assertEquals('batch', $dbLog->action);

        $MailMessagesService->dropTable(1);

        //削除したメールフィルドが存在するか確認すること
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        MailFieldsFactory::get(1);
    }

    /**
     * [API] 並び替えを更新する
     */
    public function testUpdateSort()
    {
        //データを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        //APIを呼ぶ
        $this->post("/baser/api/admin/bc-mail/mail_fields/update_sort/1.json?token=" . $this->accessToken, ['id' => 1, 'offset' => 3]);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($result->message, 'メールフィールド「性」の並び替えを更新しました。');
    }
}
