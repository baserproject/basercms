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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcMail\Controller\Admin\MailFieldsController;
use BcMail\Service\Admin\MailFieldsAdminServiceInterface;
use BcMail\Service\MailMessagesServiceInterface;
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
        $this->setFixtureTruncate();
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] メールフィールド追加
     */
    public function testAdmin_add()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 編集処理
     */
    public function testAdmin_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * フィールドデータをコピーする
     */
    public function testAdmin_ajax_copy()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(10);

        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcMail.MailFields.beforeAdd', function (Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'beforeAdd';
            $event->setData('data', $data);
        });
        //追加データを準備
        $data = [
            'field_name' => 'name_add_1',
            'type' => 'text',
            'name' => '性',
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_fields/add/10', $data);
        //イベントに入るかどうか確認
        $mailFields = $this->getTableLocator()->get('BcMail.MailFields');
        $query = $mailFields->find()->where(['name' => 'beforeAdd']);
        $this->assertEquals(1, $query->count());

        //テストデータベースを削除
        $MailMessagesService->dropTable(10);
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterAddEvent()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(10);

        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcMail.MailFields.afterAdd', function (Event $event) {
            $data = $event->getData('data');
            $contentLinks = TableRegistry::getTableLocator()->get('BcMail.MailFields');
            $data->name = 'afterAdd';
            $contentLinks->save($data);
        });
        //Postデータを生成
        $data = [
            'field_name' => 'name_add_1',
            'type' => 'text',
            'name' => '性',
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_fields/add/10', $data);
        //イベントに入るかどうか確認
        $mailFields = $this->getTableLocator()->get('BcMail.MailFields');
        $query = $mailFields->find()->where(['name' => 'afterAdd']);
        $this->assertEquals(1, $query->count());
        //テストデータベースを削除
        $MailMessagesService->dropTable(10);
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeEditEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcMail.MailFields.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'beforeEdit';
            $event->setData('data', $data);
        });
        //メールのコンテンツサービスをコル
        $mailFieldsService = $this->getService(MailFieldsAdminServiceInterface::class);
        $data = $mailFieldsService->get(1);
       //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_fields/edit/1/1', $data->toArray());
        //イベントに入るかどうか確認
        $mailFields = $this->getTableLocator()->get('BcMail.MailFields');
        $query = $mailFields->find()->where(['name' => 'beforeEdit']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterEditEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcMail.MailFields.afterEdit', function (Event $event) {
            $data = $event->getData('data');
            $mailFields = TableRegistry::getTableLocator()->get('BcMail.MailFields');
            $data->name = 'afterEdit';
            $mailFields->save($data);
        });
        //メールのコンテンツサービスをコル
        $mailFieldsService = $this->getService(MailFieldsAdminServiceInterface::class);
        $data = $mailFieldsService->get(1);
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_fields/edit/1/1', $data->toArray());
        //イベントに入るかどうか確認
        $mailFields = $this->getTableLocator()->get('BcMail.MailFields');
        $query = $mailFields->find()->where(['name' => 'afterEdit']);
        $this->assertEquals(1, $query->count());
    }
}
