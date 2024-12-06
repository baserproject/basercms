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

namespace BcMail\Test\TestCase\Controller\Admin;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMail\Controller\Admin\MailMessagesController;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MailMessagesControllerTest extends BcTestCase
{
    /**
     * Trait
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
        $this->MailMessagesController = new MailMessagesController($this->loginAdmin($this->getRequest()));
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
     * initialize
     */

    public function testInitialize()
    {
        $this->assertNotEmpty($this->MailMessagesController->BcAdminContents);

    }

    /**
     * beforeFilter
     */
    public function testBeforeFilter()
    {
        ContentFactory::make([
            'name' => 'name_test',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'お問い合わせ',
            'entity_id' => 1
        ])->persist();

        //正常テスト・エラーにならない
        $request = $this->getRequest('/baser/admin/bc-mail/mail_messages/view/1/1');
        $request = $this->loginAdmin($request);
        $this->MailMessagesController = new MailMessagesController($request);
        $event = new Event('filter');
        $this->MailMessagesController->beforeFilter($event);

        //異常テスト
        $request = $this->getRequest('/baser/admin/bc-mail/mail_messages/view/2222/1');
        $request = $this->loginAdmin($request);
        $this->MailMessagesController = new MailMessagesController($request);
        $event = new Event('filter');
        $this->expectExceptionMessage('コンテンツデータが見つかりません。');
        $this->expectException('BaserCore\Error\BcException');
        $this->MailMessagesController->beforeFilter($event);
    }

    /**
     * beforeRender
     */
    public function testBeforeRender()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 受信メール一覧
     */
    public function testIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 受信メール詳細
     */
    public function testView()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 受信メール削除
     */
    public function testDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * メールフォームに添付したファイルを開く
     */
    public function testAttachment()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
