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
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Test\Factory\MailFieldsFactory;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Test\Scenario\MailFieldsScenario;
use BcMail\Service\MailFieldsService;
use BcMail\Service\MailFieldsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
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
    public function testGet()
    {
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $result = $this->MailFieldsService->get(1);
        $this->assertEquals(1, $result->id);
        $this->expectException(RecordNotFoundException::class);
        $result = $this->MailFieldsService->get(99);
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
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $postData = [
            'id' => '1',
            'mail_content_id' => '1',
            'source' => '正社員
派遣
アルバイト',
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
        $this->assertEquals('test', $result->field_name);
        $MailMessagesService->dropTable(1);
    }

    /**
     * test update
     */
    public function test_update()
    {
        // 準備
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $mailField = $this->MailFieldsService->get(1);
        $postData = [
            'name' => 'Nghiem',
            'type' => 'radio',
            'source' => '1|2|3'
        ];
        // 正常系実行
        $result = $this->MailFieldsService->update($mailField, $postData);
        $this->assertEquals('Nghiem', $result->name);
        $this->assertEquals('radio', $result->type);
        $this->assertEquals("1\n2\n3", $result->source);
        // 異常系実行
        $postData = [
            'field_name' => '',
            'type' => 'text',
            'name' => '',
        ];
        $this->expectException("Cake\ORM\Exception\PersistenceFailedException");
        $this->MailFieldsService->update($mailField, $postData);
    }

    /**
     * test delete
     */
    public function testDelete()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(1);
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

        $MailMessagesService->dropTable(1);
    }

    /**
     * test copy
     */
    public function testCopy()
    {
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $result = $this->MailFieldsService->copy(1, 1);
        $this->assertTrue($result);
        $mailField = $this->MailFieldsService->get(4);
        $this->assertEquals('name_1_copy', $mailField->field_name);
        $BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
        $this->assertTrue($BcDatabaseService->columnExists('mail_message_1', 'name_1_copy'));
        $BcDatabaseService->removeColumn('mail_message_1', 'name_1_copy');
        $BcDatabaseService->removeColumn('mail_message_1', 'name_1');
        $BcDatabaseService->removeColumn('mail_message_1', 'name_2');
        $BcDatabaseService->removeColumn('mail_message_1', 'sex');
    }

    /**
     * test publish
     */
    public function test_publish()
    {
        // prepare
        $this->loadFixtureScenario(MailContentsScenario::class);
        MailFieldsFactory::make([
            'id' => 99,
            'mail_content_id' => 1,
            'field_name' => 'name_99',
            'type' => 'text',
            'use_field' => 0,
        ])->persist();
        // normal case
        $result = $this->MailFieldsService->publish(99);
        $this->assertEquals(1, $result->use_field);
        // abnormal case
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->MailFieldsService->publish(0);

    }

    /**
     * test unpublish
     */
    public function test_unpublish()
    {
        //データを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        // 準備
        $mailField = $this->MailFieldsService->get(1);
        // 今の公開ステータスを確認する
        $this->assertTrue($mailField->use_field);
        // 正常系実行
        $this->MailFieldsService->unpublish(1);
        //非公開したかを確認する
        $mailField = $this->MailFieldsService->get(1);
        $this->assertFalse($mailField->use_field);
    }

    /**
     * test getTitlesById
     */
    public function test_getTitlesById()
    {
        // 準備
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        // 正常系実行
        $result = $this->MailFieldsService->getTitlesById([1, 2]);
        $this->assertEquals('性', $result[1]);
        $this->assertEquals('名', $result[2]);
        //　異常系実行
        $result = $this->MailFieldsService->getTitlesById([99]);
        $this->assertEquals([], $result);
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

        $this->assertTrue($this->MailFieldsService->batch('delete', [1, 2]));
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->MailFieldsService->get(1);
    }

    /**
     * test changeSort
     */
    public function testChangeSort()
    {
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $mailField = $this->MailFieldsService->get(2);
        $this->assertEquals(2, $mailField->sort);

        $this->assertTrue($this->MailFieldsService->changeSort(2, 1));
        $mailField = $this->MailFieldsService->get(2);
        $this->assertEquals(3, $mailField->sort);

        $this->assertTrue($this->MailFieldsService->changeSort(2, -1));
        $mailField = $this->MailFieldsService->get(2);
        $this->assertEquals(2, $mailField->sort);
    }

}
