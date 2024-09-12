<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcMail\Test\TestCase\Model\Table;

use BaserCore\TestSuite\BcTestCase;
use BcMail\Model\Entity\MailMessage;
use BcMail\Model\Table\MailFieldsTable;
use BcMail\Model\Table\MailMessagesTable;
use BcMail\Test\Factory\MailFieldsFactory;
use BcMail\Test\Scenario\MailFieldsScenario;
use BcMail\Test\TestCase\Model\Array;
use BcMail\Test\TestCase\Model\ClassRegistry;
use Cake\ORM\Entity;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class MailMessageTest
 *
 * @property MailMessagesTable $MailMessage
 * @property MailFieldsTable $MailField
 */
class MailMessagesTableTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    public function setUp(): void
    {
        $this->MailMessage = $this->getTableLocator()->get('BcMail.MailMessages');
        $this->MailField = $this->getTableLocator()->get('BcMail.MailFields');
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->MailMessage);
        unset($this->MailField);
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertEquals('mail_messages', $this->MailMessage->getTable());
        $this->assertEquals('id', $this->MailMessage->getPrimaryKey());
        $this->assertTrue($this->MailMessage->hasBehavior('Timestamp'));
        $this->assertTrue($this->MailMessage->hasBehavior('BcUpload'));
    }

    /**
     *
     * setup test
     */
    public function test_setup(): void
    {
        $result = $this->MailMessage->setup(1);
        $this->assertTrue($result);
    }

    /**
     * テーブル名を設定する
     */
    public function testSetUseTable()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * アップロード設定を行う
     */
    public function testSetupUpload()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * beforeSave
     *
     * @return boolean
     */
    public function testBeforeSave()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // 初期化
        $this->MailMessage->createTable(1);
        // ======================================================
        // createTable の際、CakeSchema::read(); が実行され、
        // ClassRegistry内のモデルが全てAppModelに変換され MailMessage::setup() が失敗する
        // そのため、ClassRegistry::flush() を行うが、次は、setup() 内の setupUpload() で、Behavior のロードに失敗する
        // といったわけで、ClassRegistry::addObject で強制的に更新
        // ======================================================
        ClassRegistry::flush();
        ClassRegistry::addObject('MailMessage', $this->MailMessage);
        $this->MailMessage->setup(1);
        $this->MailMessage->data = ['MailMessage' => [
            'name_1' => "\xE2\x85\xA0\xE2\x85\xA1\xE3\x8D\x8D\xE3\x88\xB9",
            'name_2' => 'hoge',
            'root' => '2',
            'category' => '2',
            'email_1' => 'hoge@hoge.com',
            'email_2' => 'hoge@hoge.com'
        ]];
        $result = $this->MailMessage->save();
        $this->MailMessage->dropTable(1);
        $this->assertEquals('IIIメートル(代)', $result['MailMessage']['name_1'], 'beforeSaveでデータベース用のデータに変換されていません');
    }

    /**
     * Called after data has been checked for errors
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate($id, $data, $expected, $message)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->MailMessage->setup($id);
        $this->MailMessage->data = ['MailMessage' => $data];

        $this->MailMessage->validates();
        $this->assertEquals($expected, $this->MailMessage->validationErrors, $message);
    }

    public static function validateDataProvider()
    {
        return [
            // 正常系
            [
                1, [
                'email_1' => 'a@example.co.jp', 'email_2' => 'a@example.co.jp',
                'tel_1' => '000', 'tel_2' => '0000', 'tel_3' => '0000',
                'category' => 1, 'message' => ['year' => 9999, 'month' => 99, 'day' => 99],
                'name_1' => 'baser', 'name_2' => 'cms',
                'root' => '検索エンジン',
            ],
                [], 'バリデーションチェックが正しく行われていません'
            ],
            // 異常系
            [
                1, [
                'email_1' => 'email', 'email_2' => 'email_hoge', // Eメール確認チェック
                'tel_1' => 'num1', 'tel_2' => false, 'tel_3' => false, // 不完全データチェック
                'category' => false, 'message' => false, // 拡張バリデートチェック, FixtureでmessageにVALID_DATETIME付与済み
                'name_1' => '', 'name_2' => '', // バリデートグループエラーチェック
            ],
                [
                    'name_1' => [__d('baser_core', '必須項目です。')],
                    'name_2' => [__d('baser_core', '必須項目です。')],
                    'email_1' => [__d('baser_core', '形式が無効です。')],
                    'email_2' => [__d('baser_core', '形式が無効です。')],
                    'root' => [__d('baser_core', '必須項目です。')],
                    'email_not_same' => [__d('baser_core', '入力データが一致していません。')],
                    'tel_not_complate' => [__d('baser_core', '入力データが不完全です。')],
                    'tel_1' => [true],
                    'tel_2' => [true],
                    'tel_3' => [true],
                    'category' => [__d('baser_core', '必須項目です。')],
                    'name' => [true, true],
                    'email' => [true, true]
                ], 'バリデーションチェックが正しく行われていません'
            ],
        ];
    }

    /**
     * バリデート処理
     */
    public function testBeforeValidate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Called after data has been checked for errors
     */
    public function testAfterValidate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     *
     * convertToDb test
     */
    public function testConvertToDb()
    {
        $this->loadFixtureScenario(MailFieldsScenario::class);
        MailFieldsFactory::make([
            'mail_content_id' => 1,
            'field_name' => 'multi_check',
            'type' => 'multi_check',
            'use_field' => 1,
        ])->persist();
        MailFieldsFactory::make([
            'mail_content_id' => 1,
            'field_name' => 'pw',
            'type' => 'password',
            'use_field' => 1,
        ])->persist();
        $mailFields = $this->MailField->find('all')->all();
        $mailMessage = new Entity(
            [
                'id' => 1,
                'name_1' => "\xE3\x8C\x98",
                'multi_check' => ['a', 'b', 'c'],
            ]
        );
        $result = $this->MailMessage->convertToDb($mailFields, $mailMessage);
        $this->assertEquals('グラム', $result->name_1);
        $this->assertEquals('a|b|c', $result->multi_check);
    }

    public static function convertToDbDataProvider()
    {
        return [
            [null, 'hoge', 'hoge'],
            ['multi_check', 'hoge', 'hoge'],
            ['multi_check', ['hoge1', 'hoge2', 'hoge3'], 'hoge1|hoge2|hoge3'],
            [null, "\xE2\x85\xA0\xE2\x85\xA1\xE3\x8D\x8D\xE3\x88\xB9", 'IIIメートル(代)'],
            ['multi_check', ["\xE2\x85\xA0", "\xE2\x85\xA1", "\xE3\x8D\x8D", "\xE3\x88\xB9"], 'I|II|メートル|(代)'],
        ];
    }

    /**
     * メール用に変換する
     *
     * @param int $no_send no_sendの値
     * @param string $type 指定するタイプ
     * @dataProvider convertDatasToMailDataProvider
     */
    public function testConvertDatasToMail($no_send, $type)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // 初期化
        $this->MailMessage->mailFields = [
            [
                'MailField' => [
                    'field_name' => 'value',
                    'use_field' => true,
                    'no_send' => $no_send,
                    'type' => $type,
                ]
            ]
        ];
        $dbData = [
            'mailFields' => [
                'key1' => [
                    'MailField' => [
                        'before_attachment' => '<before>before_attachment',
                        'after_attachment' => '<after><br>after_attachment',
                        'head' => '<head><br>head',
                    ]
                ]
            ],
            'message' => [
                'value' => '<br><br />hoge',
            ]
        ];
        if ($type == 'file') {
            $dbData['message']['value_tmp'] = 'hoge_tmp';
        }


        // 実行
        $result = $this->MailMessage->convertDatasToMail($dbData);

        if (is_null($type)) {
            if (!$no_send) {
                $expectedMailField = [
                    'before_attachment' => 'before_attachment',
                    'after_attachment' => "\nafter_attachment",
                    'head' => 'head',
                ];
                $this->assertEquals($expectedMailField, $result['mailFields']['key1']['MailField'], 'mailFieldsに正しい値を格納できていません');

                $expectedMessage = "<br><br />hoge";
                $this->assertEquals($expectedMessage, $result['message']['value']);
            } else {
                $this->assertEmpty($result['message']);
            }
        } else if ($type == 'multi_check') {
            $expectedMessage = "<br><br />hoge";
            $this->assertEquals($expectedMessage, $result['message']['value'][0]);
        } else if ($type == 'file') {
            $expectedMessage = 'hoge_tmp';
            $this->assertEquals($expectedMessage, $result['message']['value']);
        }
    }

    public static function convertDatasToMailDataProvider()
    {
        return [
            [0, null],
            [1, null],
            [0, 'multi_check'],
            [0, 'file'],
        ];
    }

    /**
     * フルテーブル名を生成する
     */
    public function testCreateFullTableName()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 受信メッセージの内容を表示状態に変換する
     *
     * @param int $id
     * @param array $messages
     * @return array
     */
    public function testConvertMessageToCsv()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $messages = [
            ['MailMessage' => [
                'id' => 1, 'name_1' => 'v1', 'name_2' => 'v2',
                'name_kana_1' => 'v3', 'name_kana_2' => 'v4', 'sex' => 'v5',
                'email_1' => 'v6', 'email_2' => 'v7', 'tel_1' => 'v8',
                'tel_2' => 'v9', 'tel_3' => 'v10', 'zip' => 'v11',
                'address_1' => 'v12', 'address_2' => 'v13', 'address_3' => 'v14',
                'category' => 'v15', 'message' => 'v16', 'root' => 'v17',
                'root_etc' => 'v18', 'created' => 'v19', 'modified' => 'v20',
                'modified' => 'v21',
            ]],
            ['MailMessage' => [
                'id' => 2, 'name_1' => 'v1', 'name_2' => 'v2',
                'name_kana_1' => 'v3', 'name_kana_2' => 'v4', 'sex' => 'v5',
                'email_1' => 'v6', 'email_2' => 'v7', 'tel_1' => 'v8',
                'tel_2' => 'v9', 'tel_3' => 'v10', 'zip' => 'v11',
                'address_1' => 'v12', 'address_2' => 'v13', 'address_3' => 'v14',
                'category' => 'v15', 'message' => 'v16', 'root' => 'v17',
                'root_etc' => 'v18', 'created' => 'v19', 'modified' => 'v20',
                'modified' => 'v21',
            ]]
        ];

        $expected = [
            0 => [
                'MailMessage' => [
                    'NO' => 1, 'name_1 (姓漢字)' => 'v1', 'name_2 (名漢字)' => 'v2',
                    'name_kana_1 (姓カナ)' => 'v3', 'name_kana_2 (名カナ)' => 'v4', 'sex (性別)' => '',
                    'email_1 (メールアドレス)' => 'v6', 'email_2 (メールアドレス確認)' => 'v7',
                    'tel_1 (電話番号１)' => 'v8', 'tel_2 (電話番号２)' => 'v9', 'tel_3 (電話番号３)' => 'v10',
                    'zip (郵便番号)' => 'v11', 'address_1 (都道府県)' => '', 'address_2 (市区町村・番地)' => 'v13',
                    'address_3 (建物名)' => 'v14', 'category (お問い合わせ項目)' => '', 'message (お問い合わせ内容)' => 'v16',
                    'root (ルート)' => '', 'root_etc (ルートその他)' => 'v18', '作成日' => 'v19', '更新日' => 'v21'
                ]
            ],
            1 => [
                'MailMessage' => [
                    'NO' => 2, 'name_1 (姓漢字)' => 'v1', 'name_2 (名漢字)' => 'v2',
                    'name_kana_1 (姓カナ)' => 'v3', 'name_kana_2 (名カナ)' => 'v4', 'sex (性別)' => '',
                    'email_1 (メールアドレス)' => 'v6', 'email_2 (メールアドレス確認)' => 'v7',
                    'tel_1 (電話番号１)' => 'v8', 'tel_2 (電話番号２)' => 'v9', 'tel_3 (電話番号３)' => 'v10',
                    'zip (郵便番号)' => 'v11', 'address_1 (都道府県)' => '', 'address_2 (市区町村・番地)' => 'v13',
                    'address_3 (建物名)' => 'v14', 'category (お問い合わせ項目)' => '', 'message (お問い合わせ内容)' => 'v16',
                    'root (ルート)' => '', 'root_etc (ルートその他)' => 'v18', '作成日' => 'v19', '更新日' => 'v21'
                ]
            ]
        ];

        $result = $this->MailMessage->convertMessageToCsv(1, $messages);
        $this->assertEquals($expected, $result, '受信メッセージの内容を表示状態に正しく変換できません');
    }

    /**
     * find
     *
     * @param String $type
     * @param mixed $query
     * @return Array
     */
    public function testFind()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     *
     * setMailFields test
     *
     */
    public function testSetMailFields()
    {
        $mailFields = $this->MailMessage->mailFields;
        $this->assertCount(0, $mailFields);
        $this->loadFixtureScenario(MailFieldsScenario::class);

        $this->MailMessage->setMailFields(1);
        $mailFields = $this->MailMessage->mailFields;
        $this->assertCount(3, $mailFields);

        $this->MailMessage->setMailFields(99);
        $mailFields = $this->MailMessage->mailFields;
        $this->assertCount(0, $mailFields);
    }

    /**
     *
     * _validGroupErrorCheck test
     *
     */
    public function testValidGroupErrorCheck()
    {
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $mailMessage = new MailMessage(
            [
                'id' => 1,
                'name_1' => "hehe",
            ]
        );
        $this->MailMessage->setMailFields(1);
        $this->execPrivateMethod($this->MailMessage, '_validGroupErrorCheck', [$mailMessage]);
        $this->assertCount(0, $mailMessage->getErrors());

        MailFieldsFactory::make([
            'id' => 4,
            'mail_content_id' => 1,
            'field_name' => 'ok',
            'group_valid' => 'name',
            'use_field' => 1,
        ])->persist();
        $mailMessage = new MailMessage(
            [
                'id' => 11,
            ]
        );
        $this->MailMessage->setMailFields(1);
        $this->execPrivateMethod($this->MailMessage, '_validGroupErrorCheck', [$mailMessage]);
        $this->assertCount(0, $mailMessage->getErrors());
    }

    /**
     *
     * _validGroupComplete test
     *
     */
    public function testValidGroupComplete()
    {
        MailFieldsFactory::make([
            'id' => 99,
            'mail_content_id' => 1,
            'field_name' => 'ok99',
            'valid_ex' => 'VALID_GROUP_COMPLATE, keke',
            'use_field' => 1,
        ])->persist();
        MailFieldsFactory::make([
            'id' => 98,
            'mail_content_id' => 1,
            'field_name' => 'ok98',
            'type' => 'number',
            'valid_ex' => 'VALID_GROUP_COMPLATE, 98',
            'use_field' => 1,
        ])->persist();
        $this->MailMessage->setMailFields(1);
        $mailMessage = new MailMessage(
            [
                'id' => 1,
                'name_1' => "hehe",
                'ok98' => "hic98",
                'ok99' => "hic99",
            ]
        );
        $this->execPrivateMethod($this->MailMessage, '_validGroupComplete', [$mailMessage]);
        $this->assertCount(0, $mailMessage->getErrors());

        $mailMessage = new MailMessage(
            [
                'id' => 1,
                'name_1' => "hehe",
                'ok98' => "hic98",
                'ok99' => "",
            ]
        );
        $this->execPrivateMethod($this->MailMessage, '_validGroupComplete', [$mailMessage]);
        $this->assertCount(1, $mailMessage->getErrors()['_not_complate']);
    }


    /**
     * test createFullTableName
     */
    public function test_createFullTableName()
    {
        $this->MailMessage->tablePrefix = 'prefix_';
        $mailContentId = 5;

        $result = $this->MailMessage->createFullTableName($mailContentId);
        $expected = 'prefix_mail_message_5';
        $this->assertEquals($expected, $result);
    }
}
