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
use BaserCore\Test\Factory\ContentFolderFactory;
use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Test\Factory\MailContentFactory;
use BcMail\Test\Factory\MailFieldsFactory;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MailMessagesControllerTest extends BcTestCase
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
     * test add - 受け付け停止中は403を返す
     */
    public function test_add_notAccepting(): void
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $MailMessagesService->createTable(1);

        PermissionFactory::make()->allowGuest('/baser/api/*')->persist();
        ContentFolderFactory::make(['id' => 1])->persist();
        ContentFactory::make([
            'id' => 1,
            'plugin' => 'BaserCore',
            'type' => 'ContentFolder',
            'url' => '/',
            'site_id' => 1,
            'entity_id' => 1,
            'parent_id' => null,
            'rght' => 6,
            'lft' => 1,
            'status' => true,
        ])->persist();
        MailContentFactory::make([
            'id' => 1,
            'publish_end' => '2000-01-01 00:00:00',
            'sender_name' => 'test',
            'subject_user' => 'subject',
            'subject_admin' => 'subject',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
        ])->persist();
        ContentFactory::make([
            'id' => 2,
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'お問い合わせ',
            'entity_id' => 1,
            'parent_id' => 1,
            'rght' => 3,
            'lft' => 2,
            'status' => true,
        ])->persist();

        $this->enableCsrfToken();
        $this->post('/baser/api/bc-mail/mail_messages/add/1.json', ['name_1' => 'Test']);
        $this->assertResponseCode(403);

        $MailMessagesService->dropTable(1);
    }

    /**
     * test validate
     */
    public function test_validate()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);

        //データを生成
        PermissionFactory::make()->allowGuest('/baser/api/*')->persist();
        MailFieldsFactory::make(['mail_content_id' => 1, 'field_name' => 'name_1', 'valid' => 1, 'use_field' => 1, 'valid_ex'=>''])->persist();
        $this->enableCsrfToken();
        //成功時のテスト
        $this->post("/baser/api/bc-mail/mail_messages/validate/1.json", [ 'name_1' => 'test']);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertTrue($result->success);
        $this->assertCount(0, $result->errors);

        //失敗時のテスト
        $this->post("/baser/api/bc-mail/mail_messages/validate/1.json", [ 'name_1' => '']);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertFalse($result->success);
        $this->assertEquals('必須項目です。', $result->errors->name_1->_empty);

        //不要なテーブルを削除
        $MailMessagesService->dropTable(1);
    }
}
