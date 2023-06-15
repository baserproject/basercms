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
use BcMail\Controller\MailFrontAppController;
use BcMail\Model\Entity\MailConfig;
use BcMail\Model\Entity\MailMessage;
use BcMail\Service\Front\MailFrontService;
use BcMail\Service\Front\MailFrontServiceInterface;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailFieldsServiceInterface;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Test\Factory\MailContentFactory;
use BcMail\Test\Factory\MailFieldsFactory;
use BcMail\Test\Factory\MailMessagesFactory;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Test\Scenario\MailFieldsScenario;
use Cake\Filesystem\File;
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
     * test getAdminMail
     */
    public function test_getAdminMail()
    {
        // prepare
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        MailContentFactory::make([
            'id' => 111,
            'description' => 'description test',
            'sender_1' => 'sender_1',
            'sender_name' => 'name 111',
            'subject_user' => 'subject_user 111',
            'subject_admin' => 'subject_admin 111',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'redirect_url' => '/',
            'ssl_on' => 0,
            'save_info' => 1,
        ])->persist();
        ContentFactory::make([
            'id' => 111,
            'name' => 'name_test',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'title111',
            'entity_id' => 111,
            'parent_id' => 1,
            'rght' => 1,
            'lft' => 2,
            'status' => true,
            'created_date' => '2023-02-16 16:41:37',
        ])->persist();
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $MailContentsService->get(111);

        // normal case
        $result = $this->MailFrontService->getAdminMail($mailContent);
        $this->assertEquals('sender_1', $result);

        // abnormal case
        $mailContent = $MailContentsService->get(1);
        $this->expectException('TypeError');
        $result = $this->MailFrontService->getAdminMail($mailContent);
    }

    /**
     * test __construct
     */
    public function test_construct()
    {
        $this->MailFrontService->__construct();
        $this->assertInstanceOf(MailContentsServiceInterface::class, $this->MailFrontService->MailContentsService);
    }

    /**
     * test setupPreviewForIndex
     */
    public function test_setupPreviewForIndex()
    {
        // prepare
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $MailContentsService->get(1)->toArray();
        $controller = new MailFrontAppController(
            $this->getRequest('/contact/')
                ->withParam('entityId', 2)
                ->withParsedBody($mailContent)
        );
        // normal case
        $this->MailFrontService->setupPreviewForIndex($controller);
        $this->assertEquals('Mail/default/index', $controller->viewBuilder()->getTemplate());
        $vars = $controller->viewBuilder()->getVars();
        $this->assertArrayHasKey('mailContent', $vars);
        $this->assertArrayHasKey('title', $vars);
        $this->assertArrayHasKey('freezed', $vars);
        $this->assertArrayHasKey('mailFields', $vars);
        $this->assertArrayHasKey('mailMessage', $vars);
        $this->assertArrayHasKey('editLink', $vars);
        $this->assertArrayHasKey('currentWidgetAreaId', $vars);
        $this->assertEquals(1, $vars['mailContent']->id);
        $this->assertEquals('お問い合わせ', $vars['title']);
    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        // prepare
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $MailContentsService->get(1);
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        MailMessagesFactory::make(
            [
                'id' => 1,
            ]
        )->persist();
        $mailMessage = $MailMessagesService->get(1);
        // normal case
        $result = $this->MailFrontService->getViewVarsForIndex($mailContent, $mailMessage);
        $this->assertEquals(false, $result['freezed']);
        $this->assertEquals(1, $result['mailContent']->id);
        $this->assertCount(3, $result['mailFields']);
        $this->assertEquals(1, $result['mailMessage']->id);
        $this->assertEquals(null, $result['editLink']);
    }

    /**
     * test getViewVarsForConfirm
     */
    public function test_getViewVarsForConfirm()
    {
        // prepare
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $MailContentsService->get(1);
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        MailMessagesFactory::make(
            [
                'id' => 1,
            ]
        )->persist();
        $mailMessage = $MailMessagesService->get(1);
        // normal case
        $result = $this->MailFrontService->getViewVarsForConfirm($mailContent, $mailMessage);
        $this->assertEquals(false, $result['error']);
        $this->assertEquals(true, $result['freezed']);
        $this->assertEquals(1, $result['mailContent']->id);
        $this->assertCount(3, $result['mailFields']);
        $this->assertEquals(1, $result['mailMessage']->id);
        $this->assertEquals(null, $result['editLink']);
    }

    /**
     * test _checkDirectoryRraversal
     */
    public function test_checkDirectoryRraversal()
    {
        // prepare
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        MailFieldsFactory::make(
            [
                'id' => '99',
                'mail_content_id' => 1,
                'no' => '19',
                'name' => 'ルートその他',
                'field_name' => 'file_1',
                'type' => 'file',
                'before_attachment' => '',
                'after_attachment' => '',
                'options' => 'maxFileSize|1|fileExt|jpg',
                'class' => '',
                'default_value' => '',
                'description' => '',
                'group_field' => 'root',
                'valid_ex' => 'VALID_MAX_FILE_SIZE,VALID_FILE_EXT',
                'auto_convert' => '',
                'not_empty' => 0,
                'use_field' => 1,
                'no_send' => 0,
            ],
        )->persist();
        $pathTest = TMP . 'test' . DS;
        //テストファイルを作成
        new File($pathTest . 'test.txt', true);
        $testFile = $pathTest . 'test.txt';

        // normal case
        $postData = [
            'name_1' => '1',
            'name_2' => '2',
            'file_1' => $testFile
        ];
        $result = $this->execPrivateMethod($this->MailFrontService, '_checkDirectoryRraversal', [1, $postData]);
        $this->assertTrue($result);
    }

    /**
     * test confirm
     */
    public function test_confirm()
    {
        // prepare
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $MailContentsService->get(1);

        // normal case
        $postData = [
            'name_1' => 'Nghiem 1',
            'name_2' => 'Nghiem 2',
        ];
        $result = $this->MailFrontService->confirm($mailContent, $postData);
        $this->assertInstanceOf(MailMessage::class, $result);
        $this->assertEquals('Nghiem 1', $result['name_1']);
    }
}
