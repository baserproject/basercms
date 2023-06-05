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

namespace BcMail\Test\TestCase\Service;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Test\Scenario\MailFieldsScenario;
use BcMail\Service\MailFieldsService;
use BcMail\Service\MailFieldsServiceInterface;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * MailContentsServiceTest
 *
 * @property MailFieldsService $MailFieldsService
 */
class MailFieldsServiceTest extends BcTestCase
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
        $this->MailFieldsService = $this->getService(MailFieldsServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->MailFieldsService);
    }

    /**
     * test constructor
     */
    public function testConstruct()
    {
        $this->MailFieldsService->__construct();
        $this->assertTrue(isset($this->MailFieldsService->MailFields));
        $this->assertTrue(isset($this->MailFieldsService->MailMessagesService));
        $this->assertInstanceOf("BcMail\Service\MailMessagesService", $this->MailFieldsService->MailMessagesService);
        $this->assertInstanceOf("BcMail\Model\Table\MailFieldsTable", $this->MailFieldsService->MailFields);
    }

    /**
     * test get
     */
    public function test_get()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getIndex
     */
    public function testGetIndex()
    {
        //　準備
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        // パラメータなしでテストする
        $queryParams = [];
        $result = $this->MailFieldsService->getIndex(1, $queryParams);
        $this->assertCount(3, $result->all());
        // レコード数を制限するのをテストする
        $queryParams = [
            'limit' => '1'
        ];
        $result = $this->MailFieldsService->getIndex(1, $queryParams);
        $this->assertCount(1, $result->all());
        // フィールドを利用するレコードの取得をテストする
        $queryParams = [
            'use_field' => 1
        ];
        $result = $this->MailFieldsService->getIndex(1, $queryParams);
        $this->assertCount(3, $result->all());
        // フィールドを利用しないレコードの取得をテストする
        $queryParams = [
            'use_field' => 0
        ];
        $result = $this->MailFieldsService->getIndex(1, $queryParams);
        $this->assertCount(0, $result->all());
        // ステータスが公開するレコードの取得をテストする
        $queryParams = [
            'status' => 'publish'
        ];
        $result = $this->MailFieldsService->getIndex(1, $queryParams);
        $this->assertCount(3, $result->all());
    }

    /**
     * test getList
     */
    public function test_getList()
    {
        //データを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $rs = $this->MailFieldsService->getList(1);
        //戻る値を確認
        $this->assertEquals('性',$rs[1]);
        $this->assertEquals('名',$rs[2]);
        $this->assertEquals('性別',$rs[3]);
    }

    /**
     * test getNew
     */
    public function testGetNew()
    {
        $result = $this->MailFieldsService->getNew(1);
        $this->assertInstanceOf('BcMail\Model\Entity\MailField', $result);
        $result = $this->MailFieldsService->getNew(99);
        $this->assertEquals(99, $result->mail_content_id);
    }

    /**
     * test create
     */
    public function testCreate()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $postData = [
            'id' => '1',
            'mail_content_id' => '1',
            'no' => '1',
            'name' => '姓漢字',
            'field_name' => 'test',
            'type' => 'text',
            'head' => 'お名前',
            'attention' => '',
            'before_attachment' => '<small>[姓]</small>',
            'after_attachment' => '',
            'options' => '',
            'class' => '',
            'default_value' => '',
            'description' => '',
            'group_field' => 'name',
            'group_valid' => 'name',
            'valid_ex' => '',
            'use_field' => 1,
            'sort' => '1',
        ];
        $result = $this->MailFieldsService->create($postData);
        $this->assertEquals('name_1', $result->field_name);
    }

    /**
     * test update
     */
    public function test_update()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test delete
     */
    public function testDelete()
    {
        //データを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
        $BcDatabaseService->addColumn('mail_message_1', 'name_1', 'text');
        $mailField = $this->MailFieldsService->get(1);
        $this->assertEquals(1, $mailField->id);
        //正常系実行
        $this->assertTrue($this->MailFieldsService->delete(1));
        //カラムの削除を確認する
        $this->assertFalse($BcDatabaseService->columnExists('mail_message_1', 'name_1'));
        //レコードの削除を確認する
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->MailFieldsService->get(1);
    }

    /**
     * test copy
     */
    public function test_copy()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test publish
     */
    public function test_publish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test unpublish
     */
    public function test_unpublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getTitlesById
     */
    public function test_getTitlesById()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test batch
     */
    public function testBatch()
    {
        //データを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
        $BcDatabaseService->addColumn('mail_message_1', 'name_1', 'text');
        $BcDatabaseService->addColumn('mail_message_1', 'name_2', 'text');

        $mailField1 = $this->MailFieldsService->get(1);
        $this->assertEquals(1, $mailField1->id);
        $mailField2 = $this->MailFieldsService->get(2);
        $this->assertEquals(2, $mailField2->id);
        $this->assertTrue($this->MailFieldsService->batch('delete', [1, 2]));
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->MailFieldsService->get(1);
    }

    /**
     * test changeSort
     */
    public function test_changeSort()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
