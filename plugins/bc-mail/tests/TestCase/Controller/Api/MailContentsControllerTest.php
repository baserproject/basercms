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
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Test\Factory\MailContentFactory;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MailContentsControllerTest extends BcTestCase
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
        'plugin.BcMail.Factory/MailFields',
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
     * [API] メールコンテンツ API 一覧取得
     */
    public function testIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] メールコンテンツ API 単一データ取得
     */
    public function testView()
    {
        //データを生成
        MailContentFactory::make([
            'id' => 1,
            'description' => 'description test',
            'sender_name' => 'baserCMSサンプル',
            'subject_user' => '【baserCMS】お問い合わせ頂きありがとうございます。',
            'subject_admin' => '【baserCMS】お問い合わせを受け付けました',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'redirect_url' => '/',
        ])->persist();

        //APIを呼ぶ
        $this->get("/baser/api/bc-mail/mail_contents/view/1.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('description test', $result->mailContent->description);
        $this->assertEquals('baserCMSサンプル', $result->mailContent->sender_name);
        $this->assertEquals('【baserCMS】お問い合わせ頂きありがとうございます。', $result->mailContent->subject_user);
    }

    /**
     * [API] メールコンテンツ API リスト取得
     */
    public function testList()
    {
        //データを生成
        $this->loadFixtureScenario(MailContentsScenario::class);
        //APIを呼ぶ
        $this->get("/baser/api/bc-mail/mail_contents/list/1.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->mailContents);
    }

    /**
     * [API] メールコンテンツ API 新規追加
     */
    public function testAdd()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] メールコンテンツ API 編集
     */
    public function testEdit()
    {
        //メールのコンテンツサービスをコル
        $mailContentServices = $this->getService(MailContentsServiceInterface::class);
        //データを生成
        MailContentFactory::make([
            'id' => 1,
            'description' => 'description test',
            'sender_name' => 'baserCMSサンプル',
            'subject_user' => '【baserCMS】お問い合わせ頂きありがとうございます。',
            'subject_admin' => '【baserCMS】お問い合わせを受け付けました',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'redirect_url' => '/',
        ])->persist();
        ContentFactory::make([
            'name' => 'name_test',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/contact/',
            'title' => 'お問い合わせ',
            'entity_id' => 1,
            'rght' => 1,
            'lft' => 2,
            'created_date' => '2023-02-16 16:41:37',
        ])->persist();
        //Postデータを準備
        $mailContent = $mailContentServices->get(1);
        $mailContent->description = 'this is api edit';
        $mailContent->content->title = 'edited';
        //APIを呼ぶ
        $this->post("/baser/api/bc-mail/mail_contents/edit/1.json?token=" . $this->accessToken, $mailContent->toArray());
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        // メールコンテンツが変更できるか確認すること
        $this->assertEquals($result->mailContent->description, 'this is api edit');
        // コンテンツのタイトルが変更できるか確認すること
        $this->assertEquals($result->content->title, 'edited');
        // 戻るメッセージを確認
        $this->assertEquals($result->message, 'メールフォーム「edited」を更新しました。');

        //コンテンツがない場合はエラーを返す
        $mailContent->content->title = '';
        //APIを呼ぶ
        $this->post("/baser/api/bc-mail/mail_contents/edit/1.json?token=" . $this->accessToken, $mailContent->toArray());
        // レスポンスコードを確認する
        $this->assertResponseCode(400);
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        // 戻るメッセージを確認
        $this->assertEquals($result->message, '入力エラーです。内容を修正してください。');
        $this->assertEquals($result->errors->content->title->_empty, 'タイトルを入力してください。');
    }

    /**
     * [API] メールコンテンツ API 削除
     */
    public function testDelete()
    {
        //メールのコンテンツサービスをコル
        $mailContentServices = $this->getService(MailContentsServiceInterface::class);
        //データを生成
        MailContentFactory::make([
            'id' => 1,
            'description' => 'description test',
            'sender_name' => 'baserCMSサンプル',
            'subject_user' => '【baserCMS】お問い合わせ頂きありがとうございます。',
            'subject_admin' => '【baserCMS】お問い合わせを受け付けました',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'redirect_url' => '/',
        ])->persist();
        ContentFactory::make([
            'name' => 'name_test',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/contact/',
            'title' => 'お問い合わせ',
            'entity_id' => 1,
            'rght' => 1,
            'lft' => 2,
            'created_date' => '2023-02-16 16:41:37',
        ])->persist();
        //APIを呼ぶ
        $this->post("/baser/api/bc-mail/mail_contents/delete/1.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->mailContent);
        $this->assertNotNull($result->content);
        // 戻るメッセージを確認
        $this->assertEquals($result->message, 'メールフォーム「お問い合わせ」を削除しました。');

        //削除したメールコンテンツが存在しないか確認すること
        $mailContent = $this->getTableLocator()->get('MailContents');
        $query = $mailContent->find()->where(['id' => 1]);
        $this->assertEquals(0, $query->count());
    }

    /**
     * [API] メールコンテンツ API コピー
     */
    public function testCopy()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
