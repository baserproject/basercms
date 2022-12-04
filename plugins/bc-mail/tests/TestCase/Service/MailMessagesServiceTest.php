<?php
return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

//namespace BcMail\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;

/**
 * MailMessagesServiceTest
 */
class MailMessagesServiceTest extends BcTestCase
{

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
     * 初期値の設定をする
     *
     * @param string $type
     * @dataProvider getNewDataProvider
     */
    public function testGetNew($type)
    {
        $this->markTestIncomplete('こちらのテストは未実装です。MailMessagesTable::getDefaultValue()より移植');
        // 初期化
        $this->MailMessage->mailFields = [
            [
                'MailField' => [
                    'field_name' => 'value',
                    'use_field' => true,
                    'default_value' => 'default',
                    'type' => $type,
                ]
            ]
        ];
        $data = ['MailMessage' => [
            'key1' => 'hoge1',
            'key2' => 'hoge2',
        ]];

        // 実行
        $result = $this->MailMessage->getDefaultValue($data);

        if ($type != 'multi_check') {
            $expected = [
                'MailMessage' => [
                    'value' => 'default',
                    'key1' => 'hoge1',
                    'key2' => 'hoge2'
                ]
            ];
            $this->assertEquals($expected, $result);
        } else {
            $this->assertEquals('default', $result['MailMessage']['value'][0]);
        }
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
