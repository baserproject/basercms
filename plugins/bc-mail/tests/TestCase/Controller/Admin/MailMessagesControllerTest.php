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
use BaserCore\Utility\BcFile;
use BcMail\Controller\Admin\MailMessagesController;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Test\Factory\MailContentFactory;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
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
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        // メールメッセージのデータを作成する
        ContentFactory::make([
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'お問い合わせ',
            'entity_id' => 1,
        ])->persist();
        MailContentFactory::make(['id' => 1])->persist();
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);

        //正常テスト
        $this->get('/baser/admin/bc-mail/mail_messages/index/1');
        $this->assertResponseOk();
        $MailMessagesService->dropTable(1);
    }

    /**
     * [ADMIN] 受信メール詳細
     */
    public function testView()
    {
        // メールメッセージのデータを作成する
        ContentFactory::make([
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'お問い合わせ',
            'entity_id' => 1,
        ])->persist();
        MailContentFactory::make(['id' => 1])->persist();
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        $mailMessageTable->save(new Entity(['id' => 1]));

        //正常テスト
        $this->get('/baser/admin/bc-mail/mail_messages/view/1/1');
        $this->assertResponseCode(200);

        //異常テスト
        $this->get('/baser/admin/bc-mail/mail_messages/view/1/2');
        $this->assertResponseCode(404);

        $MailMessagesService->dropTable(1);
    }

    /**
     * [ADMIN] 受信メール削除
     */
    public function testDelete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        // メールメッセージのデータを作成する
        ContentFactory::make([
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'お問い合わせ',
            'entity_id' => 1,
        ])->persist();
        MailContentFactory::make(['id' => 1])->persist();
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        $mailMessageTable->setup($mailContentId);
        $mailMessageTable->save(new Entity(['id' => 1]));

        //正常テスト
        $this->post('/baser/admin/bc-mail/mail_messages/delete/1/1');
        $this->assertResponseCode(302);
        $this->assertRedirect(['action' => 'index', 1]);
        $this->assertFlashMessage('お問い合わせ への受信データ NO「1」 を削除しました。');

        //異常テスト
        $this->get('/baser/admin/bc-mail/mail_messages/delete/1/1');
        $this->assertResponseCode(405);

        $MailMessagesService->dropTable(1);
    }

    /**
     * メールフォームに添付したファイルを開く
     */
    public function testAttachment()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $filePath = WWW_ROOT . 'files' . DS . 'mail' . DS . 'limited' . DS . '1' . DS . 'messages' . DS . '00000002_tel.jpg';
        $file = new BcFile($filePath);
        $file->create();
        $file->write('test');

        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $MailMessagesService->createTable(1);
        MailContentFactory::make(['id' => 1])->persist();
        ContentFactory::make(['name' => 'name_test', 'plugin' => 'BcMail', 'type' => 'MailContent', 'entity_id' => 1])->persist();
        $mailMessageTable = TableRegistry::getTableLocator()->get('mail_message_1');
        $mailMessageTable->save(new Entity(['id' => 1, 'created' => '2016-07-29 18:02:53', 'modified' => '2020-09-14 21:10:41']));
        $mailMessageTable->save(new Entity(['id' => 2, 'created' => '2016-07-29 18:02:53', 'modified' => '2020-09-14 21:10:41']));

        $this->get('/baser/admin/bc-mail/mail_messages/attachment/1/00000002_tel.jpg');
        $this->assertResponseCode(200);
        $this->assertResponseNotEmpty();

        //不要テーブルを削除
        $MailMessagesService->dropTable(1);
        //不要ファイルを削除
        (new BcFile($filePath))->delete();
    }

    /**
     * test download_csv
     */
    public function testDownloadCsv()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データを生成
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $MailMessagesService->createTable(1);
        MailContentFactory::make(['id' => 1])->persist();
        ContentFactory::make(['name' => 'name_test', 'plugin' => 'BcMail', 'type' => 'MailContent', 'entity_id' => 1])->persist();
        $mailMessageTable = TableRegistry::getTableLocator()->get('mail_message_1');
        $mailMessageTable->save(new Entity(['id' => 1, 'created' => '2016-07-29 18:02:53', 'modified' => '2020-09-14 21:10:41']));
        $mailMessageTable->save(new Entity(['id' => 2, 'created' => '2016-07-29 18:02:53', 'modified' => '2020-09-14 21:10:41']));

        //対象メソッドをテスト
        $this->get('/baser/admin/bc-mail/mail_messages/download_csv/1');
        $this->assertResponseCode(200);

        //不要テーブルを削除
        $MailMessagesService->dropTable(1);
    }
}
