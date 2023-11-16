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

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Test\Scenario\InitAppScenario;
use BcMail\Service\Admin\MailMessagesAdminService;
use BcMail\Service\MailMessagesService;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Test\Factory\MailMessagesFactory;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Test\Scenario\MailFieldsScenario;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
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

    /**
     * test getViewVarsForDownloadCsv
     */
    public function test_getViewVarsForDownloadCsv()
    {
        //データを生成
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);

        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        // 準備
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $mailMessageTable = TableRegistry::getTableLocator()->get('mail_message_1');
        $mailMessageTable->save(new Entity(['id' => 1]));
        $mailMessageTable->save(new Entity(['id' => 2]));

        // 正常系実行：Encodingを指定するケース
        $request = $this->getRequest()->withQueryParams([
            'Site' => SiteFactory::get(1),
            'encoding' => 'abc',
            'Content' => ContentFactory::get(1)
        ]);
        $this->loginAdmin($request);
        $result = $this->MailMessagesAdminService->getViewVarsForDownloadCsv(1, $request);
        $this->assertEquals('abc', $result['encoding']);

        // 正常系実行：Encodingを指定しないケース
        $request = $this->getRequest()->withQueryParams([
            'Content' => ContentFactory::get(1)
        ]);
        $this->loginAdmin($request);
        $result = $this->MailMessagesAdminService->getViewVarsForDownloadCsv(1, $request);
        $this->assertEquals('utf-8', $result['encoding']);
        $this->assertCount(2, $result['messages']);
        $this->assertEquals(1, $result['messages'][0]['MailMessage']['NO']);
        $this->assertEquals(2, $result['messages'][1]['MailMessage']['NO']);
        $this->assertArrayHasKey('contentName', $result);

        //不要テーブルを削除
        $MailMessagesService->dropTable(1);
    }

}
