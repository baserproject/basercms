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

namespace BcMail\Test\TestCase\Controller\Api;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMail\Test\Factory\MailContentFactory;
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
        'plugin.BaserCore.Factory/Contents',
        'plugin.BcMail.Factory/MailContents',
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
     * [API] 受信メール一覧
     */
    public function testIndex()
    {
        // メールメッセージのデータを作成する
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        // mail_message_1テーブルに１件のレコードを追加する
        $mailMessageTable->save(new Entity(['id' => 2]));

        // 受信メール一覧のAPIを叩く
        $this->get("/baser/api/bc-mail/mail_messages/index/$mailContentId.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // レスポンスのメールメッセージデータを確認する
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotEmpty($result->mailMessages);
    }

    /**
     * [API] 受信メール詳細
     */
    public function testView()
    {
        // メールメッセージのデータを作成する
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        // mail_message_1テーブルに１件のレコードを追加する
        $mailMessageTable->save(new Entity(['id' => 2]));

        // 受信メール詳細のAPIを叩く
        $this->get("/baser/api/bc-mail/mail_messages/view/$mailContentId/2.json?token=" . $this->accessToken);
        // レスポンスのコードを確認する
        $this->assertResponseOk();
        // レスポンスのメールメッセージデータを確認する
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(2, $result->mailMessage->id);
    }

    /**
     * [API] 受信メール追加
     */
    public function testAdd()
    {
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
        $data = ['id' => 1, 'message' => 'test message'];

        // 受信メール追加のAPIを叩く
        $this->post("/baser/api/bc-mail/mail_messages/add/1.json?token=$this->accessToken", $data);
        $result = json_decode((string)$this->_response->getBody());
        // レスポンスのコードを確認する
        $this->assertResponseOk();
        // レスポンスのメッセージ内容を確認する
        $this->assertEquals('お問い合わせ(※関連Fixture未完了) への受信データ NO「1」を追加しました。', $result->message);
        // 追加したメールメッセージ内容を確認する
        $this->assertEquals('test message', $result->mailMessage->message);

        // 無効なメールメッセージデータの場合、エラーになる
        $data = ['id' => 'text'];
        $this->post("/baser/api/bc-mail/mail_messages/add/1.json?token=$this->accessToken", $data);
        $result = json_decode((string)$this->_response->getBody());
        // レスポンスのコードを確認する
        $this->assertResponseCode(500);
        // レスポンスのメッセージ内容を確認する
        $this->assertEquals('データベース処理中にエラーが発生しました。Cannot convert value of type `string` to integer', $result->message);
    }

    /**
     * [API] 受信メール編集
     */
    public function testEdit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] 受信メール削除
     */
    public function testDelete()
    {
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
        $this->post("/baser/api/bc-mail/mail_messages/delete/$mailContentId/1.json?token=$this->accessToken");
        $result = json_decode((string)$this->_response->getBody());
        // レスポンスのコードを確認する
        $this->assertResponseOk();
        // レスポンスのメッセージ内容を確認する
        $this->assertEquals('お問い合わせ(※関連Fixture未完了) への受信データ NO「1」を削除しました。', $result->message);
        // 削除の結果を確認する
        $this->assertTrue($result->mailMessage);
    }

    /**
     * [API] CSVダウンロード
     */
    public function testDownload()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
