<?php
// TODO ucmitz  : コード確認要
return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Test.Case.Model
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('MailMessage', 'BcMail.Model');

/**
 * Class MailMessageTest
 *
 * @property MailMessage $MailMessage
 */
class MailMessageTest extends BaserTestCase
{

    public $fixtures = [
        'baser.Default.SiteConfig',
        'baser.Default.Site',
        'baser.Default.Content',
        'plugin.Mail.Default/MailMessage',
        'plugin.Mail.Default/MailConfig',
        'plugin.Mail.Model/MailMessage/MailContentMailMessage',
        'plugin.Mail.Model/MailMessage/MailFieldMailMessage',
    ];

    public function setUp()
    {
        $this->MailMessage = ClassRegistry::init('BcMail.MailMessage');
        parent::setUp();
    }

    public function tearDown()
    {
        unset($this->MailMessage);
        parent::tearDown();
    }

    /**
     * モデルのセットアップを行う
     *
     * MailMessageモデルは利用前にこのメソッドを呼び出しておく必要あり
     *
     * @param type $mailContentId
     * @return boolean
     */
    public function testSetup()
    {
        $this->MailMessage->setup(1);
        $this->assertEquals('mail_message_1', $this->MailMessage->createTableName(1), 'テーブルを正しく設定できません');

        // setupUpload
        $saveDir = $this->MailMessage->getBehavior('BcUpload')->BcUpload['MailMessage']->settings['saveDir'];
        $expected = "mail" . DS . "limited" . DS . '1' . DS . "messages";
        $this->assertEquals($expected, $saveDir, 'アップロード設定を正しく設定できません');
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
        $this->MailMessage->setup($id);
        $this->MailMessage->data = ['MailMessage' => $data];

        $this->MailMessage->validates();
        $this->assertEquals($expected, $this->MailMessage->validationErrors, $message);
    }

    public function validateDataProvider()
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
                    'name_1' => [__('必須項目です。')],
                    'name_2' => [__('必須項目です。')],
                    'email_1' => [__('形式が無効です。')],
                    'email_2' => [__('形式が無効です。')],
                    'root' => [__('必須項目です。')],
                    'email_not_same' => [__('入力データが一致していません。')],
                    'tel_not_complate' => [__('入力データが不完全です。')],
                    'tel_1' => [true],
                    'tel_2' => [true],
                    'tel_3' => [true],
                    'category' => [__('必須項目です。')],
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
     * データベース用のデータに変換する
     *
     * @param array $type
     * @param mixed $value データベース用のデータの値
     * @param mixed $expected 期待値
     * @dataProvider convertToDbDataProvider
     */
    public function testConvertToDb($type, $value, $expected)
    {
        // 初期化
        $this->MailMessage->mailFields = [
            [
                'MailField' => [
                    'field_name' => 'value',
                    'use_field' => true,
                    'type' => $type,
                ]
            ]
        ];
        $dbData = ['MailMessage' => [
            'value' => $value,
        ]];

        // 実行
        $result = $this->MailMessage->convertToDb($dbData);

        $this->assertEquals($expected, $result['MailMessage']['value']);
    }

    public function convertToDbDataProvider()
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
     * 機種依存文字の変換処理
     * 内部文字コードがUTF-8である必要がある。
     * 多次元配列には対応していない。
     */
    public function testReplaceText()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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

    public function convertDatasToMailDataProvider()
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
}
