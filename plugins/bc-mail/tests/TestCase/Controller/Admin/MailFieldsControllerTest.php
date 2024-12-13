<?php

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

namespace BcMail\Test\TestCase\Controller\Admin;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use BcMail\Controller\Admin\MailFieldsController;
use BcMail\Service\Admin\MailFieldsAdminServiceInterface;
use BcMail\Service\MailFieldsServiceInterface;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Test\Factory\MailFieldsFactory;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Test\Scenario\MailFieldsScenario;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MailFieldsControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
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
        $request = $this->getRequest('/baser/admin/bc-mail/mail_fields/');
        $request = $this->loginAdmin($request);
        $this->MailFieldsController = new MailFieldsController($request);
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
     * Test initialize
     */
    public function testInitialize()
    {
        $controller = new MailFieldsController($this->getRequest());
        $this->assertNotEmpty($controller->BcAdminContents);
        $this->assertEquals('mailContent', $controller->BcAdminContents->getConfig('entityVarName'));
    }

    /**
     * beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * beforeRender
     */
    public function testBeforeRender()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] メールフィールド一覧
     */
    public function testAdmin_index()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_fields/index/1');
        $this->assertResponseOk();
    }

    /**
     * [ADMIN] メールフィールド追加
     */
    public function testAdmin_add()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        //テストデータベースを生成
        $this->loadFixtureScenario(MailContentsScenario::class);
        $data = [
            'id' => 1,
            'mail_content_id' => 1,
            'name' => 'test',
            'field_name' => 'name_1',
            'type' => 'text',
            'source' => '資料請求|問い合わせ|その他'
        ];
        $this->post('/baser/admin/bc-mail/mail_fields/add/1', $data);
        //check response code
        $this->assertResponseCode(302);
        //check redirect
        $this->assertRedirect('/baser/admin/bc-mail/mail_fields/index/1');
        //異常系のテスト
        $data = [
            'id' => 1,
            'mail_content_id' => 1,
            'name' => null,
            'field_name' => 'name_1',
            'type' => 'text',
            'source' => '資料請求|問い合わせ|その他'
        ];
        $this->post('/baser/admin/bc-mail/mail_fields/add/1', $data);
        //check response code
        $this->assertResponseCode(200);
        $this->assertResponseContains('入力エラーです。内容を修正してください。');
        //テストデータベースを削除
        $MailMessagesService->dropTable(1);
    }

    /**
     * [ADMIN] 編集処理
     */
    public function testAdmin_edit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //テストデータベースを生成
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        //data edit
        $data = [
            'name' => 'edit',
            'type' => 'text'
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_fields/edit/1/1', $data);
        //check response code
        $this->assertResponseCode(302);
        //check flash message
        $this->assertFlashMessage('メールフィールド「edit」を更新しました。');
        //check redirect
        $this->assertRedirect('/baser/admin/bc-mail/mail_fields/index/1');
        //case error
        $data = [
            'name' => null,
            'type' => 'text'
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_fields/edit/1/1', $data);
        //check response code
        $this->assertResponseCode(200);
        $this->assertResponseContains('入力エラーです。内容を修正してください。');
    }

    /**
     * [ADMIN] 削除処理（Ajax）
     */
    public function testAdmin_ajax_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 削除処理
     */
    public function testAdmin_delete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //メールメッセージサービスをコル
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $MailMessagesService->createTable(1);
        $MailMessagesService->addMessageField(1, 'name_1');

        //データを生成
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);

        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_fields/delete/1/1');
        $this->assertResponseCode(302);
        $this->assertFlashMessage('メールフィールド「性」を削除しました。');
        $this->assertRedirect('/baser/admin/bc-mail/mail_fields/index/1');

        //check case error
        $this->post('/baser/admin/bc-mail/mail_fields/delete/1/999');
        $this->assertResponseCode(302);
        $this->assertFlashMessage('データベース処理中にエラーが発生しました。Record not found in table `mail_fields`.');
        $this->assertRedirect('/baser/admin/bc-mail/mail_fields/index/1');

        //テストデータベースを削除
        $MailMessagesService->dropTable(1);
    }

    /**
     * フィールドデータをコピーする
     */
    public function testAdmin_ajax_copy()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test copy
     */
    public function testAdmin_copy()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //データを生成
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);

        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $BcDatabaseService->addColumn('mail_message_1', 'name_1', 'text');

        //データを生成
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);

        //正常系実行
        $this->post("/baser/admin/bc-mail/mail_fields/copy/1/1");
        $this->assertResponseCode(302);
        $this->assertFlashMessage('メールフィールド「性」をコピーしました。');
        $this->assertRedirect('/baser/admin/bc-mail/mail_fields/index/1');

        //システム実行エラー
        $this->post("/baser/admin/bc-mail/mail_fields/copy/1/999");
        $this->assertResponseCode(302);
        $this->assertFlashMessage('データベース処理中にエラーが発生しました。Record not found in table `mail_fields`.');

        //テストデータベースを削除
        $MailMessagesService->dropTable(1);
    }

    /**
     * 並び替えを更新する [AJAX]
     */
    public function testAdmin_ajax_update_sort()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 無効状態にする（AJAX）
     */
    public function testAdmin_ajax_unpublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 有効状態にする（AJAX）
     */
    public function testAdmin_ajax_publish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
    /**
     * Test beforeAddEvent
     */
    public function testBeforeAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $this->loadFixtureScenario(MailContentsScenario::class);
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcMail.MailFields.beforeAdd', function (Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'beforeAdd';
            $event->setData('data', $data);
        });
        //追加データを準備
        $data = [
            'mail_content_id' => 1,
            'field_name' => 'name_add_1',
            'type' => 'text',
            'name' => '性',
            'source' => '資料請求|問い合わせ|その他'
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_fields/add/1', $data);
        //check response code
        $this->assertResponseCode(302);
        //イベントに入るかどうか確認
        $mailFields = $this->getTableLocator()->get('BcMail.MailFields');
        $query = $mailFields->find()->where(['name' => 'beforeAdd']);
        $this->assertEquals(1, $query->count());
        //テストデータベースを削除
        $MailMessagesService->dropTable(1);
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterAddEvent()
    {
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcMail.MailFields.afterAdd', function (Event $event) {
            $data = $event->getData('data');
            $contentLinks = TableRegistry::getTableLocator()->get('BcMail.MailFields');
            $data->name = 'afterAdd';
            $contentLinks->save($data);
        });
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $MailMessagesService->createTable(1);

        //テストデータベースを生成
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //正常系実行
        $data = [
            'mail_content_id' => 1,
            'field_name' => 'name_add_1',
            'type' => 'text',
            'name' => '性',
            'source' => '資料請求|問い合わせ|その他'
        ];
        $this->post('/baser/admin/bc-mail/mail_fields/add/1', $data);
        $this->assertResponseCode(302);

        //イベントに入るかどうか確認
        $mailFields = $this->getTableLocator()->get('BcMail.MailFields');
        $query = $mailFields->find()->where(['name' => 'afterAdd']);
        $this->assertEquals(1, $query->count());

        //テストデータベースを削除
        $MailMessagesService->dropTable(1);
    }

    /**
     * Test beforeEditEvent
     */
    public function testBeforeEditEvent()
    {
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcMail.MailFields.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'beforeEdit';
            $event->setData('data', $data);
        });
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        //データを生成
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $data = ['name' => 'editedName', 'type' => 'text'];
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_fields/edit/1/1', $data);
        //check response code
        $this->assertResponseCode(302);
        //イベントに入るかどうか確認
        $mailFields = $this->getTableLocator()->get('BcMail.MailFields');
        $query = $mailFields->find()->where(['name' => 'beforeEdit']);
        $this->assertEquals(1, $query->count());
        //テストデータベースを削除
        $MailMessagesService->dropTable(1);
    }

    /**
     * Test afterAddEvent
     */
    public function testAfterEditEvent()
    {
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcMail.MailFields.afterEdit', function (Event $event) {
            $data = $event->getData('data');
            $mailFields = TableRegistry::getTableLocator()->get('BcMail.MailFields');
            $data->name = 'afterEdit';
            $mailFields->save($data);
        });
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //データを生成
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);

        //正常系実行
        $data = ['name' => 'afterEdit', 'type' => 'text'];
        $this->post('/baser/admin/bc-mail/mail_fields/edit/1/1', $data);
        $this->assertResponseCode(302);

        //イベントに入るかどうか確認
        $mailFields = $this->getTableLocator()->get('BcMail.MailFields');
        $query = $mailFields->find()->where(['name' => 'afterEdit']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test publish
     */
    public function testAdmin_publish()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データを生成
        $this->loadFixtureScenario(MailContentsScenario::class);
        MailFieldsFactory::make([
            'id' => 1,
            'mail_content_id' => 1,
            'name' => '性',
            'field_name' => 'name_1',
            'type' => 'text',
            'use_field' => 0
        ])->persist();
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_fields/publish/1/1');
        //check response code
        $this->assertResponseCode(302);
        //check Flash message
        $this->assertFlashMessage('メールフィールド「性」を有効状態にしました。');
        //check redirect
        $this->assertRedirect('/baser/admin/bc-mail/mail_fields/index/1');
    }

    /**
     * Test unpublish
     */
    public function testAdmin_unpublish()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(MailContentsScenario::class);
        //メールフィルドのデータを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_fields/unpublish/1/1');
        //check response code
        $this->assertResponseCode(302);
        //check Flash message
        $this->assertFlashMessage('メールフィールド「性」を無効状態にしました。');
        //check redirect
        $this->assertRedirect('/baser/admin/bc-mail/mail_fields/index/1');
    }

    /**
     * _checkEnv
     */
    public function test_checkEnv()
    {
        $folderPath = '/var/www/html/webroot/files/mail/limited';
        $folder = new BcFolder($folderPath);
        $folder->delete();

        $this->execPrivateMethod($this->MailFieldsController, '_checkEnv');
        $this->assertTrue(is_dir($folderPath));
        $file = new BcFile($folderPath . DS . '.htaccess');
        $this->assertTextContains('Order allow', $file->read());
    }
}
