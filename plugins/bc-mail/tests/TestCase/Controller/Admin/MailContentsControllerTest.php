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

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcMail\Controller\Admin\MailContentsController;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Test\Factory\MailContentFactory;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MailContentsControllerTest extends BcTestCase
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
        $this->setFixtureTruncate();
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->getRequest('/baser/admin/bc-mail/mail_contents/');
        $request = $this->loginAdmin($request);
        $this->MailContentsController = new MailContentsController($request);
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
     * test initialize
     */
    public function test_initialize()
    {
        $controller = new MailContentsController($this->getRequest());
        // コンポーネントが設定されたかどうかを確認する
        $this->assertNotEmpty($controller->BcAdminContents);
        // 設定されたconfigを確認する
        $this->assertEquals('mailContent', $controller->BcAdminContents->getConfig('entityVarName'));
        $this->assertTrue($controller->BcAdminContents->getConfig('useForm'));
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test edit
     */
    public function testAdmin_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test redirectEditMail
     */
    public function test_redirectEditMail()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test redirectEditForm
     */
    public function test_redirectEditForm()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeEditEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
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
            'publish_begin' => null,
            'publish_end' => '2099-12-09 12:56:53',
        ])->persist();
        ContentFactory::make([
            'id' => 1,
            'parent_id' => null,
            'created_date' => '2023-02-16 16:41:37',
            'publish_begin' => null,
            'publish_end' => '2099-12-09 12:56:53',
        ])->persist();
        ContentFactory::make([
            'name' => 'name_test',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/contact/',
            'title' => 'お問い合わせ',
            'entity_id' => 1,
            'parent_id' => 1,
            'rght' => 1,
            'lft' => 2,
            'site_id' => 1,
            'created_date' => '2023-02-16 16:41:37',
            'publish_begin' => null,
            'publish_end' => '2099-12-09 12:56:53',
        ])->persist();
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcMail.MailContents.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['description'] = 'beforeEdit';
            $event->setData('data', $data);
        });
        //Postデータを生成
        $mailContent = $mailContentServices->get(1);
        $mailContent->description = 'this is api edit';
        $mailContent->content->title = 'edited';
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_contents/edit/1', $mailContent->toArray());
        //イベントに入るかどうか確認
        $customEntries = $this->getTableLocator()->get('BcMail.MailContents');
        $query = $customEntries->find()->where(['description' => 'beforeEdit']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterEditEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
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
            'publish_begin' => null,
            'publish_end' => '2099-12-09 12:56:53',
        ])->persist();
        ContentFactory::make([
            'id' => 1,
            'parent_id' => null,
            'created_date' => '2023-02-16 16:41:37',
            'publish_begin' => null,
            'publish_end' => '2099-12-09 12:56:53',
        ])->persist();
        ContentFactory::make([
            'name' => 'name_test',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/contact/',
            'title' => 'お問い合わせ',
            'entity_id' => 1,
            'site_id' => 1,
            'parent_id' => 1,
            'rght' => 1,
            'lft' => 2,
            'created_date' => '2023-02-16 16:41:37',
            'publish_begin' => null,
            'publish_end' => '2099-12-09 12:56:53',
        ])->persist();
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcMail.MailContents.afterEdit', function (Event $event) {
            $data = $event->getData('data');
            $mailContents = TableRegistry::getTableLocator()->get('BcMail.MailContents');
            $data->description = 'afterEdit';
            $mailContents->save($data);
        });
        //Postデータを生成
        $mailContent = $mailContentServices->get(1);
        $mailContent->description = 'this is api edit';
        $mailContent->content->title = 'edited';
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_contents/edit/1', $mailContent->toArray());
        //イベントに入るかどうか確認
        $mailContents = $this->getTableLocator()->get('BcMail.MailContents');
        $query = $mailContents->find()->where(['description' => 'afterEdit']);
        $this->assertEquals(1, $query->count());
    }
}
