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
use BcMail\Controller\Admin\MailContentsController;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Test\Scenario\MailContentsScenario;
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
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
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
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        $mailContentServices = $this->getService(MailContentsServiceInterface::class);
        //データを生成
        $this->loadFixtureScenario(MailContentsScenario::class);
        //Postデータを生成
        $mailContent = $mailContentServices->get(1);
        $mailContent->description = 'this is api edit';
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_contents/edit/1', $mailContent->toArray());
        $this->assertResponseCode(302);
        $this->assertFlashMessage('メールフォーム「お問い合わせ」を更新しました。');

        //エラーを発生した場合
        $this->post('/baser/admin/bc-mail/mail_contents/edit/222', $mailContent->toArray());
        $this->assertResponseCode(404);
    }

    /**
     * test redirectEditMail
     */
    public function test_redirectEditMail()
    {
        $rs = $this->execPrivateMethod($this->MailContentsController, 'redirectEditMail', ['default']);
        $this->assertEquals(302, $rs->getStatusCode());
        $this->assertEquals(
            ['https://localhost/baser/admin/bc-theme-file/theme_files/edit/BcFront/BcMail/email/text/default.php'],
            $rs->getHeader('Location')
        );
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
        $this->loadFixtureScenario(MailContentsScenario::class);
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcMail.MailContents.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['description'] = 'beforeEdit';
            $event->setData('data', $data);
        });
        //Postデータを生成
        $mailContent = $mailContentServices->get(1);
        $mailContent->description = 'this is api edit';
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
        $this->loadFixtureScenario(MailContentsScenario::class);
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
        //対象URLをコル
        $this->post('/baser/admin/bc-mail/mail_contents/edit/1', $mailContent->toArray());
        //イベントに入るかどうか確認
        $mailContents = $this->getTableLocator()->get('BcMail.MailContents');
        $query = $mailContents->find()->where(['description' => 'afterEdit']);
        $this->assertEquals(1, $query->count());
    }
}
