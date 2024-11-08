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

use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMail\Controller\Api\MailMessagesController;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Test\Factory\MailContentFactory;
use BcMail\Test\Scenario\MailContentsScenario;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MailMessagesControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

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
     * test beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->MailMessagesController = new MailMessagesController($this->getRequest());
        $event = new Event('Controller.beforeFilter', $this->MailMessagesController);
        $this->MailMessagesController->beforeFilter($event);
        $config = $this->MailMessagesController->FormProtection->getConfig('validate');
        $this->assertFalse($config);
    }

    /**
     * [API] 受信メール一覧
     */
    public function testIndex()
    {
        // メールメッセージのデータを作成する
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        $mailMessageTable->save(new Entity(['id' => 2]));

        // 受信メール一覧のAPIを叩く
        $this->get("/baser/api/admin/bc-mail/mail_messages/index.json?mail_content_id=1&token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // レスポンスのメールメッセージデータを確認する
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotEmpty($result->mailMessages);

        $MailMessagesService->dropTable(1);
    }

    /**
     * [API] 受信メール詳細
     */
    public function testView()
    {
        // メールメッセージのデータを作成する
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        $mailMessageTable->save(new Entity(['id' => 2]));

        // 受信メール詳細のAPIを叩く
        $this->get("/baser/api/admin/bc-mail/mail_messages/view/2.json?mail_content_id=1&token=" . $this->accessToken);
        // レスポンスのコードを確認する
        $this->assertResponseOk();
        // レスポンスのメールメッセージデータを確認する
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(2, $result->mailMessage->id);

        //不要なテーブルを削除
        $MailMessagesService->dropTable(1);
    }

    /**
     * [API] 受信メール追加
     */
    public function testAdd()
    {
        // メールメッセージのデータを作成する
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        $mailMessageTable->save(new Entity(['id' => 2]));

        // テストデータを作成する
        ContentFactory::make([
            'id' => 9,
            'name' => 'contact',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'entity_id' => 1,
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'お問い合わせ(※関連Fixture未完了)',
            'status' => true,
        ])->persist();
        MailContentFactory::make(['id' => 1, 'save_info' => 1])->persist();
        $data = [
            'description' => 'description test',
            'sender_name' => 'baserCMSサンプル',
            'subject_user' => '【baserCMS】お問い合わせ頂きありがとうございます。',
            'subject_admin' => '【baserCMS】お問い合わせを受け付けました',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'redirect_url' => '/',
            'ssl_on' => 0,
            'save_info' => 1
        ];

        // 受信メール追加のAPIを叩く
        $this->post("/baser/api/admin/bc-mail/mail_messages/add.json?mail_content_id=1&token=$this->accessToken", $data);
        $result = json_decode((string)$this->_response->getBody());
        // レスポンスのコードを確認する
        $this->assertResponseOk();
        // レスポンスのメッセージ内容を確認する
        $this->assertEquals('お問い合わせ(※関連Fixture未完了) への受信データ NO「3」を追加しました。', $result->message);
        // 追加したメールメッセージ内容を確認する
        $this->assertEquals('description test', $result->mailMessage->description);

        // 無効なメールメッセージデータの場合、エラーになる
        $data = ['id' => 'text'];
        $this->post("/baser/api/admin/bc-mail/mail_messages/add.json?mail_content_id=1&token=$this->accessToken", $data);
        $result = json_decode((string)$this->_response->getBody());
        // レスポンスのコードを確認する
        $this->assertResponseCode(500);
        // レスポンスのメッセージ内容を確認する
        $this->assertEquals('データベース処理中にエラーが発生しました。Cannot convert value `text` of type `string` to int', $result->message);

        //404エラーテスト
        $data = ['id' => 2, 'message' => 'test message'];
        $this->post("/baser/api/admin/bc-mail/mail_messages/add.json?mail_content_id=111&token=$this->accessToken", $data);
        $result = json_decode((string)$this->_response->getBody());
        // レスポンスのコードを確認する
        $this->assertResponseCode(404);
        // レスポンスのメッセージ内容を確認する
        $this->assertEquals('データが見つかりません。', $result->message);

        //不要なテーブルを削除
        $MailMessagesService->dropTable(1);
    }

    /**
     * [API] 受信メール編集
     */
    public function testEdit()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        $mailMessageTable->save(new Entity(['id' => 2]));

        // テストデータを作成する
        ContentFactory::make([
            'id' => 9,
            'name' => 'contact',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'entity_id' => 1,
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'お問い合わせ(※関連Fixture未完了)',
            'status' => true,
        ])->persist();
        MailContentFactory::make(['id' => 1, 'save_info' => 1])->persist();
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailMessageTable->setup(1);
        // mail_message_1テーブルに１件のレコードを追加する
        $mailMessageTable->save(new Entity(['id' => 1, 'message' => 'message before']));

        // 受信メール追加のAPIを叩く
        $data = ['id' => 1, 'message' => 'message after'];
        $this->post("/baser/api/admin/bc-mail/mail_messages/edit/1.json?mail_content_id=1&token=$this->accessToken", $data);
        $result = json_decode((string)$this->_response->getBody());
        // レスポンスのコードを確認する
        $this->assertResponseOk();
        // レスポンスのメッセージ内容を確認する
        $this->assertEquals('お問い合わせ(※関連Fixture未完了) への受信データ NO「1」を更新しました。', $result->message);
        // 追加したメールメッセージ内容を確認する
        $this->assertEquals('message after', $result->mailMessage->message);

        // 無効なメールメッセージデータの場合、エラーになる
        $data = ['id' => 'text'];
        $this->post("/baser/api/admin/bc-mail/mail_messages/edit/1.json?mail_content_id=1&token=$this->accessToken", $data);
        $result = json_decode((string)$this->_response->getBody());
        // レスポンスのコードを確認する
        $this->assertResponseCode(500);
        // レスポンスのメッセージ内容を確認する
        $this->assertEquals('データベース処理中にエラーが発生しました。Cannot convert value `text` of type `string` to int', $result->message);

        //不要なテーブルを削除
        $MailMessagesService->dropTable(1);
    }

    /**
     * [API] 受信メール削除
     */
    public function testDelete()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        $mailMessageTable->save(new Entity(['id' => 2]));

        // テストデータを作成する
        ContentFactory::make([
            'id' => 9,
            'name' => 'contact',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'entity_id' => 1,
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'お問い合わせ(※関連Fixture未完了)',
            'status' => true,
        ])->persist();
        MailContentFactory::make(['id' => 1, 'save_info' => 1])->persist();
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        // mail_message_1テーブルに１件のレコードを追加する
        $mailMessageTable->save(new Entity(['id' => 1]));

        // 受信メール追加のAPIを叩く
        $this->post("/baser/api/admin/bc-mail/mail_messages/delete/1.json?mail_content_id=1&token=$this->accessToken");
        $result = json_decode((string)$this->_response->getBody());
        // レスポンスのコードを確認する
        $this->assertResponseOk();
        // レスポンスのメッセージ内容を確認する
        $this->assertEquals('お問い合わせ(※関連Fixture未完了) への受信データ NO「1」を削除しました。', $result->message);
        // 削除の結果を確認する
        $this->assertTrue($result->mailMessage);
        //不要なテーブルを削除
        $MailMessagesService->dropTable(1);
    }

    /**
     * [API] 受信メール一括削除
     */
    public function testBatch()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        $mailMessageTable->save(new Entity(['id' => 2]));

        // テストデータを作成する
        ContentFactory::make([
            'id' => 9,
            'name' => 'contact',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'entity_id' => 1,
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'お問い合わせ(※関連Fixture未完了)',
            'status' => true,
        ])->persist();
        MailContentFactory::make(['id' => 1, 'save_info' => 1])->persist();
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        // mail_message_1テーブルに２件のレコードを追加する
        $mailMessageTable->save(new Entity(['id' => 1]));
        $mailMessageTable->save(new Entity(['id' => 2]));

        // 受信メール一括削除のAPIを叩く
        $data = ['batch_targets' => [1, 2], 'batch' => 'delete'];
        $this->post("/baser/api/admin/bc-mail/mail_messages/batch/1.json?token=$this->accessToken", $data);
        $result = json_decode((string)$this->_response->getBody());
        // レスポンスのコードを確認する
        $this->assertResponseOk();
        // レスポンスのメッセージ内容を確認する
        $this->assertEquals('一括処理が完了しました。', $result->message);

        // DBログに保存したかどうか確認する
        $dbLogService = $this->getService(DblogsServiceInterface::class);
        $dbLog = $dbLogService->getDblogs(1)->toArray()[0];
        $this->assertEquals('メールメッセージ No 1, 2 を 削除 しました。', $dbLog->message);
        $this->assertEquals(1, $dbLog->id);
        $this->assertEquals('MailMessages', $dbLog->controller);
        $this->assertEquals('batch', $dbLog->action);

        // 一括削除が失敗の場合のテスト
        $data = ['batch_targets' => ['invalid id'], 'batch' => 'delete'];
        // 受信メール一括削除のAPIを叩く
        $this->post("/baser/api/admin/bc-mail/mail_messages/batch/1.json?token=$this->accessToken", $data);
        // レスポンスのコードを確認する
        $this->assertResponseCode(500);
        // レスポンスのメッセージ内容を確認する
        $result = json_decode((string)$this->_response->getBody());
        $this->assertStringContainsString('($id) must be of type int, string given', $result->message);

        //不要なテーブルを削除
        $MailMessagesService->dropTable(1);
    }

    /**
     * [API] CSVダウンロード
     */
    public function testDownload()
    {
        $this->markTestSkipped('このテストは未確認です');
        // メールメッセージのデータを作成する
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        $mailMessageTable->save(new Entity(['id' => 2]));

        ob_start();
        $this->get("/baser/api/admin/bc-mail/mail_messages/download/1.json?token=$this->accessToken");
        $actual = ob_get_clean();
        $this->assertNotEmpty($actual);
    }
}
