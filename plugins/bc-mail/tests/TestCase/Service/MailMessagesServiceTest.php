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
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Test\Factory\MailFieldsFactory;
use BcMail\Test\Factory\MailMessagesFactory;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Test\Scenario\MailFieldsScenario;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;


/**
 * MailMessagesServiceTest
 */
class MailMessagesServiceTest extends BcTestCase
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
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test setup
     */
    public function testSetup()
    {
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $MailMessagesService->setup(1);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $this->assertEquals('1', $mailMessageTable->mailFields->toArray()[0]->id);
        $this->assertCount(3, $mailMessageTable->mailFields);

    }

    /**
     * test batch
     */
    public function testBatch()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailMessageTable->setup(1);
        $mailMessageTable->save(new Entity(['id' => 1]));
        $mailMessageTable->save(new Entity(['id' => 2]));
        $result = $MailMessagesService->batch('delete', []);
        $this->assertTrue($result);

        $result = $MailMessagesService->get(1);
        $this->assertEquals(1, $result->id);
        $result = $MailMessagesService->batch('delete', [1]);
        $this->assertTrue($result);
        $this->expectException(RecordNotFoundException::class);
        $MailMessagesService->get(1);
    }

    /**
     * test delete
     */
    public function testDelete()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailMessageTable->setup(1);
        $mailMessageTable->save(new Entity(['id' => 1]));

        $result = $MailMessagesService->get(1);
        $this->assertEquals(1, $result->id);
        $result = $MailMessagesService->delete(1);
        $this->assertTrue($result);
        $this->expectException(RecordNotFoundException::class);
        $MailMessagesService->delete(1);
    }

    /**
     * test get
     */
    public function testGet()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        MailMessagesFactory::make(
            [
                'id' => 99,
            ]
        )->persist();
        $result = $MailMessagesService->get(99);
        $this->assertEquals(99, $result->id);
        $this->expectException(RecordNotFoundException::class);
        $MailMessagesService->get(1);
    }

    /**
     * test update
     */
    public function testUpdate()
    {
        //準備
        $mailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailMessageTable->setup(1);
        $mailMessageTable->save(new Entity(['id' => 1, 'name_1' => 'name 1']));
        $mailMessage = $mailMessagesService->get(1);
        $mailMessage->name_1 = 'name update';

        //正常実行
        $rs = $mailMessagesService->update($mailMessage, $mailMessage->toArray());
        $this->assertEquals('name update', $rs->name_1);
    }

    /**
     * test getIndex
     */
    public function testGetIndex()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailMessageTable->setup(1);
        $mailMessageTable->save(new Entity(['id' => 1]));
        $mailMessageTable->save(new Entity(['id' => 2]));
        $result = $MailMessagesService->getIndex();
        $this->assertCount(2, $result->all());
        $this->assertEquals(1, $result->all()->toArray()[0]->id);
    }

    /**
     * test create
     */
    public function testCreate()
    {
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $MailContentsService = $this->getService(MailContentsServiceInterface::class);
        $postData = [];
        $mailContent = $MailContentsService->get(1);
        $MailMessagesService->create($mailContent, $postData);
        $result = $MailMessagesService->get(1);
        $this->assertEquals(1, $result->id);

        $postData = [
            'id' => 2,
            'name_1' => 'value 1',
        ];
        $result = $MailMessagesService->create($mailContent, $postData);
        $this->assertEquals('value 1', $result->name_1);

        $result = $MailMessagesService->get(2);
        $this->assertEquals(2, $result->id);

    }

    /**
     * メッセージファイルのフィールドを追加/名前変更/削除する
     */
    public function testAddRenameDelMessageField()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $db = $this->MailMessage->getDataSource();
        switch ($db->config['datasource']) {
            case 'Database/BcPostgres':
                $this->markTestIncomplete('このテストは、まだ実装されていません。');
                $command = '\d+';
                break;
            case 'Database/BcMysql':
                $command = 'DESCRIBE';
                break;
            case 'Database/BcSqlite':
                $this->markTestIncomplete('このテストは、まだ実装されていません。');
                $command = '.schema';
            default:
        }

        // 初期化
        $id = 1;
        $fullTable = $this->MailMessage->createFullTableName($id);
        $fieldName = 'hogeField';
        $toFieldName = 'hogeField_renamed';

        $this->MailMessage->createTable($id);
        $this->MailMessage->construction($id);

        // フィールド追加
        $this->MailMessage->addMessageField($id, $fieldName);
        $sql = $command . " $fullTable $fieldName";
        $this->assertNotEmpty($this->MailMessage->query($sql), 'メッセージファイルにフィールドを正しく追加できません');

        // フィールド名変更
        $this->MailMessage->renameMessageField($id, $fieldName, $toFieldName);
        $sql = $command . " $fullTable $toFieldName";
        $this->assertNotEmpty($this->MailMessage->query($sql), 'メッセージファイルのフィールド名を正しく変更できません');

        // フィールド削除
        $this->MailMessage->deleteMessageField($id, $toFieldName);
        $sql = $command . " $fullTable $toFieldName";
        $this->assertEmpty($this->MailMessage->query($sql), 'メッセージファイルのフィールドを正しく削除できません');

        $this->MailMessage->dropTable($id);
    }

    /**
     * メッセージテーブルを作成する
     */
    public function testCreateTable()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $result = $MailMessagesService->createTable(99);
        $this->assertTrue($result);
        $BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
        $checkExist = $BcDatabaseService->tableExists('mail_message_99');
        $this->assertTrue($checkExist);
        $dropTable = $BcDatabaseService->dropTable('mail_message_99');
        $this->assertTrue($dropTable);
    }

    /**
     *
     * test construction
     *
     */
    public function testConstruction()
    {
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
        $this->assertFalse($BcDatabaseService->columnExists('mail_message_1', 'name_1'));
        $this->assertFalse($BcDatabaseService->columnExists('mail_message_1', 'name_2'));
        $this->assertFalse($BcDatabaseService->columnExists('mail_message_1', 'sex'));
        $this->assertTrue($MailMessagesService->construction(1));
        $this->assertTrue($BcDatabaseService->columnExists('mail_message_1', 'name_1'));
        $this->assertTrue($BcDatabaseService->columnExists('mail_message_1', 'name_2'));
        $this->assertTrue($BcDatabaseService->columnExists('mail_message_1', 'sex'));
        $BcDatabaseService->removeColumn('mail_message_1', 'name_1');
        $BcDatabaseService->removeColumn('mail_message_1', 'name_2');
        $BcDatabaseService->removeColumn('mail_message_1', 'sex');

    }

    /**
     * メッセージテーブルを削除する
     */
    public function testDropTable()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
        $MailMessagesService->createTable(99);
        $this->assertTrue($BcDatabaseService->tableExists('mail_message_99'));
        $this->assertTrue($MailMessagesService->dropTable(99));
        $this->assertFalse($BcDatabaseService->tableExists('mail_message_99'));
    }

    /**
     *
     * test getNew
     */
    public function testGetNew()
    {
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $result = $MailMessagesService->getNew(1, []);
        $this->assertNull($result->mail_content_id);

        MailFieldsFactory::make([
            'id' => 4,
            'mail_content_id' => 1,
            'field_name' => 'test1',
            'use_field' => 1,
            'default_value' => 'Nghiem',
        ])->persist();
        $result = $MailMessagesService->getNew(1, []);
        $this->assertEquals('Nghiem', $result->test1);

        MailFieldsFactory::make([
            'id' => 5,
            'mail_content_id' => 1,
            'type' => 'multi_check',
            'field_name' => 'test2',
            'use_field' => 1,
            'default_value' => 'hehe',
        ])->persist();
        $result = $MailMessagesService->getNew(1, []);
        $this->assertIsArray($result->test2);
        $this->assertEquals('hehe', $result->test2[0]);

        $mailMessage = [
            'field1' => BcUtil::base64UrlSafeEncode('https://book.cakephp.org'),
        ];
        $result = $MailMessagesService->getNew(1, $mailMessage);
        $this->assertEquals('https://book.cakephp.org', $result->field1);
    }

    public static function getNewDataProvider()
    {
        return [
            [null],
            ['multi_check'],
        ];
    }

    /**
     * test autoConvert
     */
    public function testAutoConvert()
    {
        $this->loadFixtureScenario(MailContentsScenario::class);
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        MailFieldsFactory::make([
            'id' => 4,
            'mail_content_id' => 1,
            'field_name' => 'name_3',
            'use_field' => 1,
            'auto_convert' => 'CONVERT_HANKAKU',
        ])->persist();
        $data = [
            'name_1' => '   hello world   ',
            'name_2' => '<!--hello world',
            'name_3' => 'ｈｅｌｌｏ　ｗｏｒｌｄー',
            'test' => '   Nghiem   ',
        ];
        $result = $MailMessagesService->autoConvert(1, $data);
        $this->assertEquals('hello world', $result['name_1']);
        $this->assertEquals('&lt;!--hello world', $result['name_2']);
        $this->assertEquals('hello　world-', $result['name_3']);
        $this->assertEquals('   Nghiem   ', $result['test']);
    }

    /**
     * test createTableName
     */
    public function testCreateTableName()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $result = $MailMessagesService->createTableName(1);
        $this->assertEquals('mail_message_1', $result);

        $result = $MailMessagesService->createTableName(99);
        $this->assertEquals('mail_message_99', $result);

        $this->expectException(\TypeError::class);
        $MailMessagesService->createTableName('a');
    }

    /**
     * test addMessageField
     */
    public function testAddMessageField()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $result = $MailMessagesService->addMessageField(1, 'Nghiem');
        $this->assertTrue($result);

        $BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
        $result = $BcDatabaseService->columnExists('mail_message_1', 'Nghiem');
        $this->assertTrue($result);
        $BcDatabaseService->removeColumn('mail_message_1', 'Nghiem');

        $this->expectExceptionMessage("Base table or view not found: 1146 Table 'test_basercms.mail_message_99' doesn't exist");
        $MailMessagesService->addMessageField(99, 'Nghiem');

    }

    /**
     * test deleteMessageField
     */
    public function testDeleteMessageField()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
        $this->assertTrue($MailMessagesService->addMessageField(1, 'Nghiem'));
        $this->assertTrue($BcDatabaseService->columnExists('mail_message_1', 'Nghiem'));
        $this->assertTrue($MailMessagesService->deleteMessageField(1, 'Nghiem'));
        $this->assertFalse($BcDatabaseService->columnExists('mail_message_1', 'Nghiem'));
    }

    /**
     * test renameMessageField
     */
    public function testRenameMessageField()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);

        $MailMessagesService->addMessageField(1, 'Nghiem');
        $result = $BcDatabaseService->columnExists('mail_message_1', 'Nghiem');
        $this->assertTrue($result);

        $result = $MailMessagesService->renameMessageField(1, 'Nghiem', 'Test');
        $this->assertTrue($result);
        $this->assertTrue($BcDatabaseService->columnExists('mail_message_1', 'Test'));
        $this->assertFalse($BcDatabaseService->columnExists('mail_message_1', 'Nghiem'));

        $BcDatabaseService->removeColumn('mail_message_1', 'Test');

        $this->expectExceptionMessage("The specified column doesn't exist: Nghiem");
        $MailMessagesService->renameMessageField(1, 'Nghiem', 'Test');

    }

    /**
     * test renameMessageField
     */
    public function testRenameMessageField_TableNotExist()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);


        $this->expectExceptionMessage("Base table or view not found: 1146 Table 'test_basercms.mail_message_99' doesn't exist");
        $MailMessagesService->addMessageField(99, 'Test', 'Test1');

    }

    /**
     * test __construct
     */
    public function testConstruct()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $this->assertTrue(isset($MailMessagesService->BcDatabaseService));
        $this->assertTrue(isset($MailMessagesService->MailMessages));
    }


    public static function autoConvertDataProvider()
    {
        return [
            ['CONVERT_HANKAKU', '１２３ａｂｃ', '123abc', '半角変換が正しく処理されていません'],
            ['CONVERT_ZENKAKU', '123abc', '１２３ａｂｃ', '全角変換が正しく処理されていません'],
            [null, '<!-- hoge', '&lt;!-- hoge', 'サニタイズが正しく処理されていません'],
            [null, '    hoge    ', 'hoge', '空白削除が正しく処理されていません'],
        ];
    }

}
