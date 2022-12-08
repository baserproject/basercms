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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] CSVダウンロード
     */
    public function testDownload()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
