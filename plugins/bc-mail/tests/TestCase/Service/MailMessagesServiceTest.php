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

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use BcMail\Model\Entity\MailContent;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailMessagesService;
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
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BcMail.Factory/MailFields',
        'plugin.BcMail.Factory/MailMessages',
        'plugin.BcMail.Factory/MailContents',
    ];

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
        $mailMessageTable->save(new Entity(['id' => 2]));

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
    public function test_create()
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * メッセージ保存用テーブルのフィールドを最適化する
     * 初回の場合、id/created/modifiedを追加する
     * 2回目以降の場合は、最後のカラムに追加する
     *
     * @param array $dbConfig
     * @param int $mailContentId
     * @return boolean
     */
    public function testConstruction()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $db = $this->MailMessage->getDataSource();

        switch ($db->config['datasource']) {
            case 'Database/BcPostgres':
                $this->markTestIncomplete('このテストは、まだ実装されていません。');
                break;
            case 'Database/BcMysql':
                $command = 'EXPLAIN';
                break;
            case 'Database/BcSqlite':
                $this->markTestIncomplete('このテストは、まだ実装されていません。');
                $command = '.schema';
            default:
        }

        $id = 1;
        $fullTable = $this->MailMessage->createFullTableName(1);

        $this->MailMessage->dropTable($id);

        // 一回目
        $this->MailMessage->construction($id);
        $this->assertTrue($this->MailMessage->tableExists($fullTable), 'メッセージテーブルを正しく作成できません');

        $expectColumns = ['id', 'modified', 'created'];
        $sql = $command . " $fullTable";
        $resultColumns = [];
        foreach ($this->MailMessage->query($sql) as $key => $value) {
            $resultColumns[] = $value['COLUMNS']['Field'];
        }
        foreach ($expectColumns as $column) {
            $this->assertContains($column, $resultColumns, '正しくカラムが追加されていません');
        }

        // 二回目
        $this->MailMessage->construction($id);

        $this->MailField = ClassRegistry::init('BcMail.MailField');
        $expectColumns = $this->MailField->find('list', [
            'fields' => 'field_name',
            'conditions' => ['mail_content_id' => 1],
        ]);
        array_unshift($expectColumns, 'id', 'modified', 'created');

        $sql = $command . " $fullTable";
        $resultColumns = [];
        foreach ($this->MailMessage->query($sql) as $key => $value) {
            $resultColumns[] = $value['COLUMNS']['Field'];
        }
        $this->assertEquals($expectColumns, $resultColumns, '正しくカラムが追加されていません');
    }

    /**
     * メッセージテーブルを削除する
     */
    public function testDropTable()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * メール受信テーブルを全て再構築
     *
     * @return boolean
     */
    public function testReconstructionAll()
    {
        $id = 1;
        $fullTable = $this->MailMessage->createFullTableName($id);
        $this->MailMessage->dropTable($id);
        $this->assertTrue($this->MailMessage->reconstructionAll());
        $this->assertTrue($this->MailMessage->tableExists($fullTable));
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
            'field1' => BcUtil::base64UrlsafeEncode('https://book.cakephp.org'),
        ];
        $result = $MailMessagesService->getNew(1, $mailMessage);
        $this->assertEquals('https://book.cakephp.org', $result->field1);
    }

    public function getNewDataProvider()
    {
        return [
            [null],
            ['multi_check'],
        ];
    }

    /**
     * 自動変換
     * 確認画面で利用される事も踏まえてバリデートを通す為の
     * 可能な変換処理を行う。
     *
     * @param string $auto_convert 変換タイプ
     * @param string $value 入力値
     * @param string $expected 期待値
     * @param string $message テスト失敗時に表示されるメッセージ
     * @dataProvider autoConvertDataProvider
     */
    public function testAutoConvert($auto_convert, $value, $expected, $message)
    {
        $this->markTestIncomplete('こちらのテストは未実装です。MailMessagesTable::autoConvert()より移植');
        // 初期化
        $this->MailMessage->mailFields = [
            [
                'MailField' => [
                    'field_name' => 'value',
                    'auto_convert' => $auto_convert,
                    'use_field' => true,
                ]
            ]
        ];
        $data = ['MailMessage' => [
            'value' => $value
        ]];

        // 実行
        $result = $this->MailMessage->autoConvert($data);

        $this->assertEquals($expected, $result['MailMessage']['value'], $message);
    }

    public function autoConvertDataProvider()
    {
        return [
            ['CONVERT_HANKAKU', '１２３ａｂｃ', '123abc', '半角変換が正しく処理されていません'],
            ['CONVERT_ZENKAKU', '123abc', '１２３ａｂｃ', '全角変換が正しく処理されていません'],
            [null, '<!-- hoge', '&lt;!-- hoge', 'サニタイズが正しく処理されていません'],
            [null, '    hoge    ', 'hoge', '空白削除が正しく処理されていません'],
        ];
    }

}
