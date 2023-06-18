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

namespace BcMail\Test\TestCase\Service\Front;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMail\Model\Entity\MailConfig;
use BcMail\Service\Front\MailFrontService;
use BcMail\Service\Front\MailFrontServiceInterface;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailFieldsServiceInterface;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Test\Factory\MailFieldsFactory;
use BcMail\Test\Factory\MailMessagesFactory;
use BcMail\Test\Factory\MailContentFactory;
use BcMail\Service\MailFieldsServiceInterface;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Test\Factory\MailMessagesFactory;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Test\Scenario\MailFieldsScenario;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * MailContentsServiceTest
 *
 * @property MailFrontService $MailFrontService
 */
class MailFrontServiceTest extends BcTestCase
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
        'plugin.BcMail.Factory/MailFields',
        'plugin.BcMail.Factory/MailContents',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups'
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->MailFrontService = $this->getService(MailFrontServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->MailFrontService);
    }

    /**
     * test getThanksTemplate
     */
    public function test_getThanksTemplate()
    {
        // prepare
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $MailContentsService->get(1);
        $result = $this->MailFrontService->getThanksTemplate($mailContent);
        // normal case
        $this->assertEquals('Mail/default/submit', $result);
    }

    /**
     * test createMailData
     */
    public function test_createMailData()
    {
        // prepare
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        MailMessagesFactory::make(
            [
                'id' => 1,
            ]
        )->persist();
        $mailMessages = $MailMessagesService->get(1);
        $mailConfig = new MailConfig([
            'name' => 'test_name',
            'value' => 'test value',
        ]);
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $MailContentsService->get(1);
        $MailFieldsService = $this->getService(MailFieldsServiceInterface::class);
        $mailFields = $MailFieldsService->getIndex(1)->all();
        // normal case
        $result = $this->MailFrontService->createMailData($mailConfig, $mailContent, $mailFields, $mailMessages, []);
        $this->assertEquals(1, $result['message']->id);
        $this->assertEquals('name_test', $result['content']->name);
        $this->assertCount(3, $result['mailFields']);
        $this->assertEquals('description test', $result['mailContent']->description);
        $this->assertEquals('test_name', $result['mailConfig']->name);
    }

    /**
     * test getUserMail
     */
    public function test_getUserMail()
    {
        // prepare
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        // add mail field with type = email
        MailFieldsFactory::make([
            'id' => 99,
            'mail_content_id' => 1,
            'name' => 'email',
            'field_name' => 'email_1',
            'type' => 'email',
            'use_field' => 1,
        ])->persist();
        $MailFieldsService = $this->getService(MailFieldsServiceInterface::class);
        // get mail field list
        $mailFields = $MailFieldsService->getIndex(1)->all();
        // create mail message
        MailMessagesFactory::make(
            [
                'id' => 1,
            ]
        )->persist();
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $MailMessagesService->construction(1);
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $MailContentsService->get(1);
        $postData = [
            'id' => 2,
            'test' => 'Nghiem1',
            'name_1' => 'Nghiem2',
            'name_2' => 'Nghiem3',
            'sex' => 'Nghiem4',
            'email_1' => 'Nghiem',
        ];
        $mailMessage = $MailMessagesService->create($mailContent, $postData);

        // normal case
        $result = $this->MailFrontService->getUserMail($mailFields, $mailMessage);
        $this->assertEquals('Nghiem', $result);

        // abnormal case
        $mailMessage = $MailMessagesService->get(1);
        $result = $this->MailFrontService->getUserMail($mailFields, $mailMessage);
        $this->assertEquals('', $result);
    }

    /**
     * test getEditLink
     */
    public function test_getEditLink()
    {
        // prepare
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $result = $this->MailFrontService->getEditLink(1);

        // normal case
        $this->assertEquals(
            [
                'prefix' => 'Admin',
                'plugin' => 'BcMail',
                'controller' => 'MailContents',
                'action' => 'edit',
                1
            ], $result
        );
    }

    /**
     * test createMailData
     */
    public function test_createMailData()
    {
        // prepare
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        MailMessagesFactory::make(
            [
                'id' => 1,
            ]
        )->persist();
        $mailMessages = $MailMessagesService->get(1);
        $mailConfig = new MailConfig([
            'name' => 'test_name',
            'value' => 'test value',
        ]);
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $MailContentsService->get(1);
        $MailFieldsService = $this->getService(MailFieldsServiceInterface::class);
        $mailFields = $MailFieldsService->getIndex(1)->all();
        // normal case
        $result = $this->MailFrontService->createMailData($mailConfig, $mailContent, $mailFields, $mailMessages, []);
        $this->assertEquals(1, $result['message']->id);
        $this->assertEquals('name_test', $result['content']->name);
        $this->assertCount(3, $result['mailFields']);
        $this->assertEquals('description test', $result['mailContent']->description);
        $this->assertEquals('test_name', $result['mailConfig']->name);
    }

    /**
     * test getUnpublishTemplate
     */
    public function test_getUnpublishTemplate()
    {
        // prepare
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $MailContentsService->get(1);
        $result = $this->MailFrontService->getUnpublishTemplate($mailContent);

        // normal case
        $this->assertEquals('Mail/default/unpublish', $result);
    }

    /**
     * test getConfirmTemplate
     */
    public function test_getConfirmTemplate()
    {
        // 準備
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $MailContentsService->get(1);
        $result = $this->MailFrontService->getConfirmTemplate($mailContent);
        // 正常系実行
        $this->assertEquals('Mail/default/confirm', $result);
    }

    /**
     * test getIndexTemplate
     */
    public function test_getIndexTemplate()
    {
        // 準備
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $MailContentsService->get(1);
        $result = $this->MailFrontService->getIndexTemplate($mailContent);
        // 正常系実行
        $this->assertEquals('Mail/default/index', $result);
    }

    /**
     * test isAccepting
     */
    public function test_isAccepting()
    {
        // 準備
        $this->loadFixtureScenario(InitAppScenario::class);
        MailContentFactory::make([
            'id' => 99,
            'description' => 'description test 99',
            'sender_name' => '送信先名を入力してください',
            'subject_user' => 'お問い合わせ頂きありがとうございます',
            'subject_admin' => 'お問い合わせを頂きました',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'redirect_url' => '/',
            'publish_begin' => strtotime(date('Y-m-d H:i:s') . "-1 week"),
            'publish_end' => strtotime(date('Y-m-d H:i:s') . "+1 week"),
        ])->persist();
        ContentFactory::make([
            'name' => 'name_test',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/form/',
            'title' => 'テスト',
            'entity_id' => 99,
            'rght' => 1,
            'lft' => 2,
            'status' => true,
            'created_date' => '2023-02-16 16:41:37',
        ])->persist();
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $MailContentsService->get(99);
        // 正常系実行
        $result = $this->MailFrontService->isAccepting($mailContent);
        $this->assertTrue($result);
        // 非常系実行
        MailContentFactory::make([
            'id' => 999,
            'description' => 'description test 999',
            'sender_name' => '送信先名を入力してください',
            'subject_user' => 'お問い合わせ頂きありがとうございます',
            'subject_admin' => 'お問い合わせを頂きました',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'redirect_url' => '/',
            'publish_begin' => strtotime(date('Y-m-d H:i:s') . "+1 week"),
            'publish_end' => strtotime(date('Y-m-d H:i:s') . "-1 week"),
        ])->persist();
        ContentFactory::make([
            'name' => 'name_test',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/form/',
            'title' => 'テスト',
            'entity_id' => 999,
            'rght' => 1,
            'lft' => 2,
            'status' => true,
            'created_date' => '2023-02-16 16:41:37',
        ])->persist();
        $mailContent = $MailContentsService->get(999);
        $result = $this->MailFrontService->isAccepting($mailContent);
        $this->assertFalse($result);
    }

}
