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
use BcMail\Service\Admin\MailContentsAdminService;
use BcMail\Service\MailContentsService;
use BcMail\Test\Scenario\MailContentsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * MailContentsAdminServiceTest
 * @property MailContentsAdminService $MailContentsAdminService
 * @property MailContentsService $MailContentsService
 */
class MailContentsAdminServiceTest extends BcTestCase
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
        $this->MailContentsAdminService = new MailContentsAdminService();
        $this->MailContentsService = new MailContentsService();
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
        //データを生成
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $mailContent = $this->MailContentsService->get(1);
        //正常系実行
        $result = $this->MailContentsAdminService->getViewVarsForEdit($mailContent);
        $this->assertEquals(1, $result['mailContent']->id);
        $this->assertEquals('https://localhost/contact/', $result['publishLink']);
    }

    /**
     * test getPublishLink
     */
    public function test_getPublishLink()
    {
        //準備
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $mailContent = $this->MailContentsService->get(1);
        //正常系実行
        $result = $this->MailContentsAdminService->getPublishLink($mailContent);
        $this->assertEquals('https://localhost/contact/', $result);
    }

}
