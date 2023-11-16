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

use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcMail\Service\MailMessagesServiceInterface;
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
