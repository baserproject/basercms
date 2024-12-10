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
use Cake\Core\Exception\CakeException;
use Cake\ORM\Entity;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\Validation\Validator;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use TypeError;

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
        $this->MailMessage->setupUpload(1);
        $bcUpload = $this->MailMessage->getBehavior('BcUpload');
        $this->assertEquals('/var/www/html/webroot/files/mail/limited/1/messages/', $bcUpload->BcFileUploader["MailMessages"]->savePath);
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
     * test convertMessageToCsv
     */
    public function testConvertMessageToCsv()
    {
        // Set up the mail fields
        $this->MailMessage->mailFields = [
            (object)['field_name' => 'image', 'name' => 'Image', 'type' => 'file'],
            (object)['field_name' => 'description', 'name' => 'Description', 'type' => 'text'],
        ];

        // Set up the messages
        $messages = [
            new Entity(['id' => 1, 'image' => 'image1.jpg', 'description' => 'Test description 1', 'created' => '2023-01-01', 'modified' => '2023-01-02']),
            new Entity(['id' => 2, 'image' => 'image2.jpg', 'description' => 'Test description 2', 'created' => '2023-01-03', 'modified' => '2023-01-04']),
        ];

        $result = $this->MailMessage->convertMessageToCsv($messages);

        // Check the result of the conversion to CSV 1
        $this->assertEquals('image1.jpg', $result[0]['MailMessage']['image (Image)']);
        $this->assertEquals(' Test description 1', $result[0]['MailMessage']['description (Description)']);
        $this->assertEquals('2023-01-01', $result[0]['MailMessage']['作成日']);
        $this->assertEquals('2023-01-02', $result[0]['MailMessage']['更新日']);

        // Check the result of the conversion to CSV 2
        $this->assertEquals('image2.jpg', $result[1]['MailMessage']['image (Image)']);
        $this->assertEquals(' Test description 2', $result[1]['MailMessage']['description (Description)']);
        $this->assertEquals('2023-01-03', $result[1]['MailMessage']['作成日']);
        $this->assertEquals('2023-01-04', $result[1]['MailMessage']['更新日']);
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

    /**
     * test createTableName
     * @param $mailContentId
     * @param $expected
     * @param bool $expectException
     * @dataProvider createTableNameDataProvider
     */
    public function test_createTableName($mailContentId, $expected, $expectException = false)
    {
        if ($expectException) {
            $this->expectException(TypeError::class);
        }

        $result = $this->MailMessage->createTableName($mailContentId);

        if (!$expectException) {
            $this->assertEquals($expected, $result);
        }
    }

    public static function createTableNameDataProvider()
    {
        return [
            [1, 'mail_message_1'],
            [0, 'mail_message_0'],
            [-1, 'mail_message_-1'],
            ['2', 'mail_message_2'],
            ['abc', null, true],
            ['', null, true],
            [null, null, true],
            [true, 'mail_message_1'],
            [false, 'mail_message_0'],
        ];
    }

    /**
     * test setUseTable
     */
    public function test_setUseTable()
    {
        $mailContentId = 1;
        $this->MailMessage->setUseTable($mailContentId);

        $actualTableName = $this->MailMessage->getTable();
        $this->assertEquals('mail_message_1', $actualTableName);
    }

    /**
     * test convertDatasToMail
     */
    public function test_convertDatasToMail()
    {
        //mailFields
        $mailFields = [
            (object)[
                'field_name' => 'name',
                'before_attachment' => '<b>Before</b> Attachment',
                'after_attachment' => 'After<br />Attachment',
                'head' => '<br />Head',
                'no_send' => false,
                'type' => 'text'
            ],
            (object)[
                'field_name' => 'password',
                'before_attachment' => '',
                'after_attachment' => '',
                'head' => '',
                'no_send' => false,
                'type' => 'password'
            ],
            (object)[
                'field_name' => 'hobbies',
                'before_attachment' => '',
                'after_attachment' => '',
                'head' => '',
                'no_send' => false,
                'type' => 'multi_check'
            ]
        ];

        // message
        $message = new \stdClass();
        $message->name = 'John Doe';
        $message->password = 'secret123';
        $message->hobbies = 'Reading|Writing';

        // mailContent
        $mailContent = new \stdClass();
        $mailContent->subject_user = 'Hello, {$name}';
        $mailContent->subject_admin = 'New User: {$name}';

        $options = [
            'maskedPasswords' => [
                'password' => '********'
            ]
        ];

        $data = [
            'mailFields' => $mailFields,
            'message' => $message,
            'mailContent' => $mailContent,
        ];

        $result = $this->MailMessage->convertDatasToMail($data, $options);

        // Expected
        $expectedData = [
            'mailFields' => [
                (object)[
                    'field_name' => 'name',
                    'before_attachment' => 'Before Attachment',
                    'after_attachment' => "After\nAttachment",
                    'head' => 'Head',
                    'no_send' => false,
                    'type' => 'text'
                ],
                (object)[
                    'field_name' => 'password',
                    'before_attachment' => '',
                    'after_attachment' => '',
                    'head' => '',
                    'no_send' => false,
                    'type' => 'password'
                ],
                (object)[
                    'field_name' => 'hobbies',
                    'before_attachment' => '',
                    'after_attachment' => '',
                    'head' => '',
                    'no_send' => false,
                    'type' => 'multi_check'
                ]
            ],
            'message' => (object)[
                'name' => 'John Doe',
                'password' => '********',
                'hobbies' => ['Reading', 'Writing']
            ],
            'mailContent' => (object)[
                'subject_user' => 'Hello, John Doe',
                'subject_admin' => 'New User: John Doe'
            ]
        ];

        $this->assertEquals($expectedData, $result);
    }

    /**
     * test validationDefault
     * @param $existingValidator
     * @param $expectedField
     * @dataProvider validationDefaultDataProvider
     */
    public function testValidationDefault($existingValidator, $expectedField)
    {
        if ($existingValidator) {
            $validator = new Validator();
            $validator->requirePresence($expectedField);
            $this->MailMessage->setValidator('MailMessages', $validator);
        }

        $inputValidator = new Validator();
        $inputValidator->requirePresence($expectedField);

        $result = $this->MailMessage->validationDefault($inputValidator);

        $this->assertTrue($result->hasField($expectedField));

        $fieldData = $result->field($expectedField);
        $this->assertTrue($fieldData->isPresenceRequired());
        $this->assertFalse($fieldData->isEmptyAllowed());
    }

    public static function validationDefaultDataProvider()
    {
        return [
            //case existing validator
            [true, 'test_field'],
            //case not existing validator
            [false, 'input_field']
        ];
    }

}
