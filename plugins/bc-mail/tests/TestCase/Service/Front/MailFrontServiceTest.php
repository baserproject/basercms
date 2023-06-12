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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMail\Model\Entity\MailConfig;
use BcMail\Service\Front\MailFrontService;
use BcMail\Service\Front\MailFrontServiceInterface;
use BcMail\Service\MailContentsServiceInterface;
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
}
