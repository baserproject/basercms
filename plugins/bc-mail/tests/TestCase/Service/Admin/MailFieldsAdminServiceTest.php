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


use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMail\Service\Admin\MailFieldsAdminService;
use BcMail\Service\MailFieldsService;
use BcMail\Service\MailFieldsServiceInterface;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Test\Scenario\MailFieldsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * MailFieldsServiceTest
 * @property MailFieldsAdminService $MailFieldsAdminService
 * @property MailFieldsService $MailFieldsService
 *
 */
class MailFieldsAdminServiceTest extends BcTestCase
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
        $this->MailFieldsService = new MailFieldsService();
        $this->MailFieldsAdminService = new MailFieldsAdminService();
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        // €”õ
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $mailField = $this->MailFieldsService->get(1);
        // ³íŒnŽÀs
        $result = $this->MailFieldsAdminService->getViewVarsForEdit(1, $mailField);
        $this->assertEquals(1, $result['mailContent']->id);
        $this->assertEquals(1, $result['mailField']->id);
        $this->assertEquals('https://localhost/contact/', $result['publishLink']);
        $this->assertNotNull($result['autoCompleteOptions']);
        // ˆÙíŒnŽÀs
        $mailField = $this->MailFieldsService->get(1);
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->MailFieldsAdminService->getViewVarsForEdit(99, $mailField);
    }
}
