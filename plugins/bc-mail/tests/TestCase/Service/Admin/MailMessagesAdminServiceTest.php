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

namespace BcMail\Test\TestCase\Service\Admin;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Test\Scenario\InitAppScenario;
use BcMail\Service\Admin\MailMessagesAdminService;
use BcMail\Service\MailMessagesService;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Test\Factory\MailMessagesFactory;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Test\Scenario\MailFieldsScenario;
use Cake\Datasource\Exception\RecordNotFoundException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * MailMessagesAdminServiceTest
 * @property MailMessagesAdminService $MailMessagesAdminService
 * @property MailMessagesService $MailMessagesService
 *
 */
class MailMessagesAdminServiceTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Contents',
        'plugin.BcMail.Factory/MailContents',
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BcMail.Factory/MailFields',
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->MailMessagesAdminService = new MailMessagesAdminService();
        $this->MailMessagesService = new MailMessagesService();
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test getViewVarsForView
     */
    public function test_getViewVarsForView()
    {
        // prepare
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        MailMessagesFactory::make(
            [
                'id' => 2,
            ]
        )->persist();
        // normal case
        $result = $this->MailMessagesAdminService->getViewVarsForView(1, 2);
        $this->assertEquals('description test', $result['mailContent']->description);
        $this->assertEquals(2, $result['mailMessage']->id);
        $this->assertCount(3, $result['mailFields']);
        $this->assertEquals('sex', $result['mailFields'][2]->field_name);
        // abnormal case
        $this->expectException(RecordNotFoundException::class);
        $this->MailMessagesAdminService->getViewVarsForView(99, 99);
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
        MailMessagesFactory::make(
            [
                'id' => 1,
            ]
        )->persist();
        MailMessagesFactory::make(
            [
                'id' => 2,
            ]
        )->persist();
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $mailMessages = $MailMessagesService->getIndex()->all();
        // normal case
        $result = $this->MailMessagesAdminService->getViewVarsForIndex(1, $mailMessages);
        $this->assertEquals('description test', $result['mailContent']->description);
        $this->assertCount(2, $result['mailMessages']);
        $this->assertCount(3, $result['mailFields']);
        $this->assertEquals('sex', $result['mailFields'][2]->field_name);
        // abnormal case
        $this->expectException(RecordNotFoundException::class);
        $this->MailMessagesAdminService->getViewVarsForIndex(99, $mailMessages);
    }

}
