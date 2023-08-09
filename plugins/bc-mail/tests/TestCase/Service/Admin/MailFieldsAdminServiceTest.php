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
use BcMail\Model\Entity\MailField;
use BcMail\Service\Admin\MailFieldsAdminService;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailFieldsService;
use BcMail\Service\MailFieldsServiceInterface;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Test\Scenario\MailFieldsScenario;
use Cake\ORM\Entity;
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
        // 準備
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $mailField = $this->MailFieldsService->get(1);
        // 正常系実行
        $result = $this->MailFieldsAdminService->getViewVarsForEdit(1, $mailField);
        $this->assertEquals(1, $result['mailContent']->id);
        $this->assertEquals(1, $result['mailField']->id);
        $this->assertEquals('https://localhost/contact/', $result['publishLink']);
        $this->assertNotNull($result['autoCompleteOptions']);
        // 異常系実行
        $mailField = $this->MailFieldsService->get(1);
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->MailFieldsAdminService->getViewVarsForEdit(99, $mailField);
    }

    /**
     * test getViewVarsForAdd
     */
    public function test_getViewVarsForAdd()
    {
        // 準備
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $mailField = new MailField();
        // 正常系実行
        $result = $this->MailFieldsAdminService->getViewVarsForAdd(1, $mailField);
        $this->assertInstanceOf(MailField::class, $result['mailField']);
        $this->assertEquals(1, $result['mailContent']->id);
        $this->assertEquals('https://localhost/contact/', $result['publishLink']);
        $this->assertNotNull($result['autoCompleteOptions']);
        // 異常系実行
        $mailField = $this->MailFieldsService->get(1);
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->MailFieldsAdminService->getViewVarsForEdit(0, $mailField);

    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        // 準備
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->getRequest('/baser/admin/bc-mail/mail_fields/index?sortmode=1');
        $this->loginAdmin($request);
        // 正常系実行
        $result = $this->MailFieldsAdminService->getViewVarsForIndex($request, 1);
        $this->assertEquals(1, $result['mailContent']->id);
        $this->assertCount(3, $result['mailFields']);
        $this->assertEquals('https://localhost/contact/', $result['publishLink']);
        $this->assertEquals(1, $result['sortmode']);
    }

    /**
     * test getPublishLink
     */
    public function test_getPublishLink()
    {
        // 準備
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $mailContentsService = $this->getService(MailContentsServiceInterface::class);
        $mailContent = $mailContentsService->get(1);
        // 正常系実行
        $result = $this->MailFieldsAdminService->getPublishLink($mailContent);
        $this->assertEquals('https://localhost/contact/', $result);
    }

}
