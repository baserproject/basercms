<?php
// TODO ucmitz  : コード確認要
return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */
App::uses('BcApp', 'Model');
App::uses('Content', 'Model');

/**
 * Class BcAppTest
 *
 * @property BcAppModel $BcApp
 * @property Page $Page
 * @property SiteConfig $SiteConfig
 * @property Content $Content
 * @property User $User
 */
class BcAppTest extends BaserTestCase
{

    public $fixtures = [
        'baser.Default.Page',
        'baser.Default.Dblog',
        'baser.Default.SiteConfig',
        'baser.Default.User',
        'baser.Default.UserGroup',
        'baser.Default.Permission',
        'baser.Default.SearchIndex',
        'baser.Default.Content',
        'baser.Default.Site'
    ];

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->BcApp = ClassRegistry::init('BcApp');
        $this->Page = ClassRegistry::init('Page');
        $this->SiteConfig = ClassRegistry::init('SiteConfig');
        $this->Dblog = ClassRegistry::init('Dblog');
        $this->User = ClassRegistry::init('User');
        $this->Content = ClassRegistry::init('Content');

    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BcApp);
        unset($this->Page);
        unset($this->SiteConfig);
        unset($this->Dblog);
        parent::tearDown();
    }

    /**
     * コンストラクタ
     */
    public function test__construct()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * beforeSave
     *
     * @return    boolean
     * @access    public
     */
    public function testBeforeSave()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->Page->save([
            'Page' => [
                'name' => 'test',
                'page_category_id' => null,
                'title' => '',
                'url' => '',
                'description' => '',
                'status' => 1,
                'modified' => '',
            ]
        ]);

        $LastID = $this->Page->getLastInsertID();
        $result = $this->Page->find('first', [
                'conditions' => ['id' => $LastID],
                'fields' => ['created'],
                'recursive' => -1
            ]
        );

        $this->BcApp->beforeSave(['type' => 'date']);
    }

    /**
     * Saves model data to the database. By default, validation occurs before save.
     *
     * @param array $data Data to save.
     * @param boolean $validate If set, validation will be done before the save
     * @param array $fieldList List of fields to allow to be written
     * @return    mixed    On success Model::$data if its not empty or true, false on failure
     */
    public function testSave($data = null, $validate = true, $fieldList = [])
    {
        $this->Page->save([
            'Page' => [
                'name' => 'test',
                'page_category_id' => null,
                'title' => '',
                'url' => '',
                'description' => '',
                'status' => 1,
                'modified' => null,
                'created' => '2015-02-22 22:22:22'
            ]
        ]);
        $now = date('Y-m-d H');

        $LastID = $this->Page->getLastInsertID();
        $result = $this->Page->find('first', [
                'conditions' => ['id' => $LastID],
                'fields' => ['created', 'modified'],
                'recursive' => -1
            ]
        );
        $created = date('Y-m-d H', strtotime($result['Page']['created']));
        $modified = date('Y-m-d H', strtotime($result['Page']['modified']));

        $message = 'created,modifiedを更新できません';
        $this->assertEquals($now, $created, $message);
        $this->assertEquals($now, $modified, $message);
    }

    /**
     * 配列の文字コードを変換する
     *
     * @param array    変換前のデータ
     * @param string    変換後の文字コード
     * @param string    変換元の文字コード
     * @dataProvider convertEncodingByArrayDataProvider
     */
    public function testConvertEncodingByArray($data, $outenc, $inenc)
    {
        $result = $this->BcApp->convertEncodingByArray($data, $outenc, $inenc);
        foreach($result as $key => $value) {
            $encode = mb_detect_encoding($value);
            $this->assertEquals($outenc, $encode);
        }
    }

    public function convertEncodingByArrayDataProvider()
    {
        return [
            [["テスト1"], "ASCII", "SJIS"],
            [["テスト1", "テスト2"], "UTF-8", "SJIS"],
            [["テスト1", "テスト2"], "SJIS-win", "UTF-8"],
        ];
    }

    /**
     * データベースログを記録する
     */
    public function testSaveDbLog()
    {

        // Dblogにログを追加
        $message = 'テストです';
        $this->BcApp->saveDblog($message);

        // 最後に追加したログを取得
        $LastID = $this->Dblog->getLastInsertID();
        $result = $this->Dblog->find('first', [
                'conditions' => ['Dblog.id' => $LastID],
                'fields' => 'name',
            ]
        );
        $this->assertEquals($message, $result['Dblog']['name']);

    }

    /**
     * 子カテゴリのIDリストを取得する
     *
     * @dataProvider getChildIdsListDataProvider
     */
    public function testGetChildIdsList($id, $expects)
    {
        $result = $this->Content->getChildIdsList($id);
        $this->assertEquals($expects, array_values($result));
    }

    public function getChildIdsListDataProvider()
    {
        return [
            [1, [2, 9, 17, 19, 3, 10, 11, 12, 13, 14, 18, 20, 4, 5, 6, 7, 8, 15, 16]],    // PC
            [2, [9, 17, 19]],    // モバイル
            [3, [10, 11, 12, 13, 14, 18, 20]],    // スマホ
            [4, []],    // 固定ページ
            ['', [1, 2, 9, 17, 19, 3, 10, 11, 12, 13, 14, 18, 20, 4, 5, 6, 7, 8, 15, 16]],    // 全体
            [false, [1, 2, 9, 17, 19, 3, 10, 11, 12, 13, 14, 18, 20, 4, 5, 6, 7, 8, 15, 16]],    // 異常系
        ];
    }

    /**
     * 機種依存文字の変換処理
     *
     * @param string 変換対象文字列
     * @param string 変換後予想文字列
     * @dataProvider replaceTextDataProvider
     */
    public function testReplaceText($str, $expect)
    {
        $result = $this->BcApp->replaceText($str);
        $this->assertEquals($expect, $result);
    }

    public function replaceTextDataProvider()
    {
        return [
            ["\xE2\x85\xA0", "I"],
            ["\xE2\x91\xA0", "(1)"],
            ["\xE3\x8D\x89", "ミリ"],
            ["\xE3\x88\xB9", "(代)"],
        ];
    }

    /**
     * データベース初期化
     *
     * @param $pluginName
     * @param $options
     * @param $expected
     *
     * @dataProvider initDbDataProvider
     *
     * MEMO: pluginNameが実在する場合が未実装
     */
    public function testInitDb($pluginName, $options, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $result = $this->BcApp->initDb($pluginName, $options);
        $this->assertEquals($expected, $result);
    }

    public function initDbDataProvider()
    {
        return [
            ['', [], true],
            ['hoge', ['dbDataPattern' => true], 1]
        ];
    }

    /**
     * スキーマファイルを利用してデータベース構造を変更する
     */
    public function testLoadSchema()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $path = BASER_CONFIGS . 'Schema';
        $result = $this->BcApp->loadSchema('test', $path);
        $expected = true;
        var_dump($result);
        $this->assertEquals($expected, $result);
    }

    /**
     * CSVを読み込む
     */
    public function testLoadCsv()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $result = $this->BcApp->loadCsv('test', 'test');
    }

    /**
     * 範囲を指定しての長さチェック
     *
     * @param mixed $check
     * @param int $min
     * @param int $max
     * @param boolean $expect
     * @dataProvider betweenDataProvider
     */
    public function testBetween($check, $min, $max, $expect)
    {
        $result = $this->BcApp->between($check, $min, $max);
        $this->assertEquals($expect, $result);
    }

    public function betweenDataProvider()
    {
        return [
            ["あいう", 2, 4, true],
            ["あいう", 3, 3, true],
            ["あいう", 4, 3, false],
            [["あいう", "あいうえお"], 2, 4, true],
        ];
    }

    /**
     * 指定フィールドのMAX値を取得する
     */
    public function testGetMax()
    {
        $result = $this->Page->getMax('Page\.id');
        $this->assertEquals(11, $result, '指定フィールドのMAX値を取得できません');
    }

    /**
     * テーブルにフィールドを追加する
     */
    public function testAddField()
    {
        $options = [
            'field' => 'testField',
            'column' => [
                'type' => 'text',
                'null' => true,
                'default' => null,
            ],
            'table' => 'pages',
        ];
        $this->Page->addField($options);
        $columns = $this->Page->getColumnTypes();
        $this->assertEquals(isset($columns['testField']), true);
    }

    /**
     * フィールド構造を変更する
     */
    public function testEditField()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $options = [
            'field' => 'testField',
            'column' => [
                'name' => 'testColumn',
            ],
        ];
        $this->BcApp->editField($options);
        $columns = $this->Page->getColumnTypes();
    }

    /**
     * フィールド名を変更する
     */
    public function testRenameField()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }


    /**
     * フィールドを削除する
     */
    public function testDelField()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * テーブルの存在チェックを行う
     *
     * @param string $tableName
     * @param boolean $expect
     * @dataProvider tableExistsDataProvider
     */
    public function testTableExists($tableName, $expect)
    {
        $db = ConnectionManager::getDataSource('default');
        $prefix = $db->config['prefix'];

        $result = $this->BcApp->tableExists($prefix . $tableName);
        $this->assertEquals($expect, $result);
    }

    public function tableExistsDataProvider()
    {
        return [
            ["users", true],
            ["notexist", false],
        ];
    }

    /**
     * 英数チェック
     *
     * @param string $check チェック対象文字列
     * @param boolean $expect
     * @dataProvider alphaNumericDataProvider
     */
    public function testAlphaNumeric($check, $expect)
    {
        $result = $this->BcApp->alphaNumeric($check);
        $this->assertEquals($expect, $result);
    }

    public function alphaNumericDataProvider()
    {
        return [
            [["aiueo"], true],
            [["12345"], true],
            [["あいうえお"], false],
        ];
    }

    /**
     * データの重複チェックを行う
     */
    public function testDuplicate()
    {
        $check = ['id' => 1];
        $result = $this->Page->duplicate($check);
        $this->assertEquals(false, $result);

        $check = ['id' => 100];
        $result = $this->Page->duplicate($check);
        $this->assertEquals(true, $result);
    }

    /**
     * 一つ位置を上げる
     */
    public function testSortup()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 一つ位置を下げる
     */
    public function testSortdown()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 並び順を変更する
     */
    public function testChangeSort()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Key Value 形式のテーブルよりデータを取得して
     * １レコードとしてデータを展開する
     */
    public function testFindExpanded()
    {
        $result = $this->SiteConfig->findExpanded();

        $message = 'Key Value 形式のテーブルよりデータを取得して１レコードとしてデータを展開することができません';
        $this->assertEquals('baserCMS inc. [デモ]', $result['name'], $message);
        $this->assertEquals('baser,CMS,コンテンツマネジメントシステム,開発支援', $result['keyword'], $message);
    }

    /**
     * Key Value 形式のテーブルにデータを保存する
     */
    public function testSaveKeyValue()
    {
        $data = [
            'SiteConfig' => [
                'test1' => 'テストです1',
                'test2' => 'テストです2',
            ]
        ];
        $this->SiteConfig->saveKeyValue($data);
        $result = $this->SiteConfig->findExpanded();

        $message = 'Key Value 形式のテーブルにデータを保存することができません';
        $this->assertEquals('テストです1', $result['test1'], $message);
        $this->assertEquals('テストです2', $result['test2'], $message);

    }

    /**
     * Deconstructs a complex data type (array or object) into a single field value.
     */
    public function testDeconstruct()
    {
        $field = 'Page.contents';
        $data = [
            'wareki' => true,
            'year' => 'h-27',
        ];
        $result = $this->Page->deconstruct($field, $data);

        $expected = [
            'wareki' => true,
            'year' => 2015
        ];

        $this->assertEquals($expected, $result, 'deconstruct が 和暦に対応していません');
    }

    /**
     * 指定したモデル以外のアソシエーションを除外する
     *
     * @param array $auguments アソシエーションを除外しないモデル
     * @param array $expectedHasKey 期待する存在するキー
     * @param array $expectedNotHasKey 期待する存在しないキー
     * @dataProvider reduceAssociationsDataProvider
     */
    public function testReduceAssociations($arguments, $expectedHasKeys, $expectedNotHasKeys)
    {
        $this->User->reduceAssociations($arguments);
        $result = $this->User->find('first', ['conditions' => ['User.id' => 2], 'recursive' => 2]);

        // 存在するキー
        foreach($expectedHasKeys as $key) {
            $this->assertArrayHasKey($key, $result, '指定したモデル以外のアソシエーションを除外できません');
        }

        // 存在しないキー
        foreach($expectedNotHasKeys as $key) {
            $this->assertArrayNotHasKey($key, $result, '指定したモデル以外のアソシエーションを除外できません');
        }
    }

    public function reduceAssociationsDataProvider()
    {
        return [
            [[], ['User'], ['UserGroup', 'Favorite']],
            [['UserGroup'], ['User', 'UserGroup'], ['Favorite']],
            [['UserGroup.Permission'], [], ['Permission']],
            [['User', 'UserGroup', 'Favorite'], [], ['Permission']],
        ];
    }

    /**
     * Deletes multiple model records based on a set of conditions.
     */
    public function testDeleteAll()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Updates multiple model records based on a set of conditions.
     */
    public function testUpdateAll()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Used to report user friendly errors.
     * If there is a file app/error.php or app/app_error.php this file will be loaded
     * error.php is the AppError class it should extend ErrorHandler class.
     */
    public function testCakeError()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Queries the datasource and returns a result set array.
     */
    public function testFind()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * イベントを発火
     */
    public function testDispatchEvent()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * データが公開済みかどうかチェックする
     *
     * @param boolean $status 公開ステータス
     * @param string $publishBegin 公開開始日時
     * @param string $publishEnd 公開終了日時
     * @param boolean $expect
     * @dataProvider isPublishDataProvider
     */
    public function testIsPublish($status, $publishBegin, $publishEnd, $expect)
    {
        $result = $this->BcApp->isPublish($status, $publishBegin, $publishEnd);
        $this->assertEquals($expect, $result);
    }

    public function isPublishDataProvider()
    {
        return [
            [true, null, null, true],
            [false, null, null, false],
            [true, '2015-01-01 00:00:00', null, true],
            [true, '3000-01-01 00:00:00', null, false],
            [true, null, '2015-01-01 00:00:00', false],
            [true, null, '3000-01-01 00:00:00', true],
            [true, '2015-01-01 00:00:00', '3000-01-01 00:00:00', true],
            [true, '2015-01-01 00:00:00', '2015-01-02 00:00:00', false],
        ];
    }

    /**
     * ツリーより再帰的に削除する
     */
    public function testRemoveFromTreeRecursive()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testExists()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testDataIter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * @test モデルのモックが作成できるかテスト
     */
    public function testGetMock()
    {
        $expect = 'do not save!';
        $Model = $this->getMockForModel('BcAppModel', ['save']);
        $Model->expects($this->any())
            ->method('save')
            ->will($this->returnValue($expect));

        $actual = $Model->save(['Hoge' => ['name' => 'fuga']]);
        $this->assertEquals($expect, $actual, 'スタブが正しく実行されること');

    }

    /**
     * 公開済の conditions を取得
     */
    public function testGetConditionAllowPublish()
    {
        $result = $this->BcApp->getConditionAllowPublish();
        $pattern = '/' . '([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})' . '/';
        $this->assertMatchesRegularExpression($pattern, $result[0]['or']['0']['BcApp.publish_begin <=']);
        $this->assertEquals($result[0]['or']['1']['BcApp.publish_begin'], null);
        $this->assertEquals($result[0]['or']['2']['BcApp.publish_begin'], '0000-00-00 00:00:00');
        $this->assertMatchesRegularExpression($pattern, $result[1]['or']['0']['BcApp.publish_end >=']);
        $this->assertEquals($result[1]['or']['1']['BcApp.publish_end'], null);
        $this->assertEquals($result[1]['or']['2']['BcApp.publish_end'], '0000-00-00 00:00:00');
    }

}
