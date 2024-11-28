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
use BcMail\Model\Table\MailFieldsTable;
use BcMail\Test\Factory\MailFieldsFactory;
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\Test\Scenario\MailFieldsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * @property MailFieldsTable $MailFieldsTable
 */
class MailFieldsTableTest extends BcTestCase
{

    use ScenarioAwareTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->MailFieldsTable = $this->getTableLocator()->get('BcMail.MailFields');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->MailFieldsTable);
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertEquals('mail_fields', $this->MailFieldsTable->getTable());
        $this->assertEquals('id', $this->MailFieldsTable->getPrimaryKey());
        $this->assertTrue($this->MailFieldsTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->MailFieldsTable->hasAssociation('MailContents'));
    }

    /**
     * validate
     */
    public function test_validationDefaultNotError()
    {
        $validator = $this->MailFieldsTable->getValidator('default');
        $errors = $validator->validate([
            'name' => str_repeat('a', 255),
            'field_name' => str_repeat('a', 50),
            'mail_content_id' => 999,
            'type' => 'type',
            'head' => str_repeat('a', 255),
            'attention' => str_repeat('a', 255),
            'before_attachment' => str_repeat('a', 255),
            'after_attachment' => str_repeat('a', 255),
            'options' => str_repeat('a', 255),
            'class' => str_repeat('a', 255),
            'default_value' => str_repeat('a', 255),
            'description' => str_repeat('a', 255),
            'group_field' => str_repeat('a', 255),
            'group_valid' => str_repeat('a', 255)
        ]);
        $this->assertCount(0, $errors);
    }

    public function test_validationDefaultEmpty()
    {
        $validator = $this->MailFieldsTable->getValidator('default');
        $errors = $validator->validate([
            'name' => '',
            'type' => ''
        ]);
        $this->assertEquals('項目名を入力してください。', current($errors['name']));
        $this->assertEquals('タイプを入力してください。', current($errors['type']));

    }


    public function test_validationDefaultOverText()
    {
        $validator = $this->MailFieldsTable->getValidator('default');
        $errors = $validator->validate([
            'name' => str_repeat('a', 256),
            'field_name' => str_repeat('a', 51),
            'mail_content_id' => 999,
            'type' => 'type',
            'head' => str_repeat('a', 256),
            'attention' => str_repeat('a', 256),
            'before_attachment' => str_repeat('a', 256),
            'after_attachment' => str_repeat('a', 256),
            'options' => str_repeat('a', 256),
            'class' => str_repeat('a', 256),
            'default_value' => str_repeat('a', 256),
            'description' => str_repeat('a', 256),
            'group_field' => str_repeat('a', 256),
            'group_valid' => str_repeat('a', 256)
        ]);

        $this->assertEquals('項目名は255文字以内で入力してください。', current($errors['name']));
        $this->assertEquals('フィールド名は50文字以内で入力してください。', current($errors['field_name']));
        $this->assertEquals('項目見出しは255文字以内で入力してください。', current($errors['head']));
        $this->assertEquals('注意書きは255文字以内で入力してください。', current($errors['attention']));
        $this->assertEquals('前見出しは255文字以内で入力してください。', current($errors['before_attachment']));
        $this->assertEquals('後見出しは255文字以内で入力してください。', current($errors['after_attachment']));
        $this->assertEquals('オプションは255文字以内で入力してください。', current($errors['options']));
        $this->assertEquals('クラス名は255文字以内で入力してください。', current($errors['class']));
        $this->assertEquals('初期値は255文字以内で入力してください。', current($errors['default_value']));
        $this->assertEquals('説明文は255文字以内で入力してください。', current($errors['description']));
        $this->assertEquals('グループ名は255文字以内で入力してください。', current($errors['group_field']));
        $this->assertEquals('グループ入力チェックは255文字以内で入力してください。', current($errors['group_valid']));
    }

    public function test_validationDefaultHankakuCheck()
    {
        $validator = $this->MailFieldsTable->getValidator('default');
        $errors = $validator->validate([
            'mail_content_id' => 999,
            'field_name' => '１２３ａｂｃ',
            'group_field' => '１２３ａｂｃ',
            'group_valid' => '１２３ａｂｃ'
        ]);

        $this->assertEquals('フィールド名は小文字の半角英数字、アンダースコアのみで入力してください。', current($errors['field_name']));
        $this->assertEquals('グループ名は半角英数字、ハイフン、アンダースコアで入力してください。', current($errors['group_field']));
        $this->assertEquals('グループ入力チェックは半角英数字、ハイフン、アンダースコアで入力してください。', current($errors['group_valid']));
    }

    public function test_validationDefaultDuplicate()
    {
        MailFieldsFactory::make(['mail_content_id' => 1, 'field_name' => 'field_1'])->persist();
        $validator = $this->MailFieldsTable->getValidator('default');
        $errors = $validator->validate([
            'mail_content_id' => 1,
            'field_name' => 'field_1'
        ]);

        $this->assertEquals('既に登録のあるフィールド名です。', current($errors['field_name']));
    }

    /**
     * コントロールソースを取得する
     */
    public function testGetControlSource()
    {
        //$field = null
        $rs = $this->MailFieldsTable->getControlSource();
        $this->assertEquals('テキスト', $rs['type']['text']);
        $this->assertEquals('Eメール形式チェック', $rs['valid_ex']['VALID_EMAIL']);
        $this->assertEquals('半角変換', $rs['auto_convert']['CONVERT_HANKAKU']);

        //$field != null
        $rs = $this->MailFieldsTable->getControlSource('type');
        $this->assertEquals('テキストエリア', $rs['textarea']);
        $this->assertEquals('隠しフィールド', $rs['hidden']);
    }

    /**
     * 同じ名称のフィールド名がないかチェックする
     * 同じメールコンテンツが条件
     */
    public function testDuplicateMailField()
    {
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $this->loadFixtureScenario(MailContentsScenario::class);

        $context = [
            'field' => 'field_name',
            'data' => ['mail_content_id' => 1, 'id' => 2],
            'newRecord' => true
        ];
        //case false
        $result = $this->MailFieldsTable->duplicateMailField('name_1', $context);
        $this->assertFalse($result);

        //case true
        $result = $this->MailFieldsTable->duplicateMailField('name_test', $context);
        $this->assertTrue($result);

    }

    /**
     * メールフィールドの値として正しい文字列か検証する
     * 半角英数-_
     */
    public function testHalfTextMailField()
    {
        //case true
        $string = 'abc123_';
        $result = $this->MailFieldsTable->halfTextMailField($string);
        $this->assertTrue($result);

        //case false
        $string = 'abcABC123_';
        $result = $this->MailFieldsTable->halfTextMailField($string);
        $this->assertFalse($result);
    }

    /**
     * 選択リストの入力チェック
     * @dataProvider sourceMailFieldDataProvider
     */
    public function test_sourceMailField($value, $context, $expected)
    {
        $result = $this->MailFieldsTable->sourceMailField($value, $context);
        $this->assertEquals($expected, $result);
    }

    public static function sourceMailFieldDataProvider()
    {
        return [
            ['radio', ['data' => ['type' => 'radio']], true],
            ['', ['data' => ['type' => 'radio']], false],
            ['select', ['data' => ['type' => 'select']], true],
            ['', ['data' => ['type' => 'select']], false],
            ['multi_check', ['data' => ['type' => 'multi_check']], true],
            ['', ['data' => ['type' => 'multi_check']], false],
            ['autozip', ['data' => ['type' => 'autozip']], true],
            ['', ['data' => ['type' => 'autozip']], false],
            ['text', ['data' => ['type' => 'text']], true]
        ];
    }


    /**
     * フィールドデータをコピーする
     *
     * @param int $id
     * @param array $data
     * @param array $sortUpdateOff
     * @param array $expected 期待値
     * @dataProvider copyDataProvider
     */
    public function testCopy($id, $data, $sortUpdateOff)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $options = ['sortUpdateOff' => $sortUpdateOff];
        $result = $this->MailField->copy($id, $data, $options);

        if ($id) {
            $this->assertEquals('姓漢字_copy', $result['MailField']['name'], '$idからコピーができません');
            if (!$sortUpdateOff) {
                $this->assertEquals(19, $result['MailField']['sort'], 'sortを正しく設定できません');
            } else {
                $this->assertEquals(1, $result['MailField']['sort'], 'sortを正しく設定できません');
            }
        } else {
            $this->assertEquals('hogeName_copy', $result['MailField']['name'], '$dataからコピーができません');
            if (!$sortUpdateOff) {
                $this->assertEquals(19, $result['MailField']['sort'], 'sortを正しく設定できません');
            } else {
                $this->assertEquals(999, $result['MailField']['sort'], 'sortを正しく設定できません');
            }
        }
    }

    public static function copyDataProvider()
    {
        return [
            [1, [], false],
            [false, ['MailField' => [
                'mail_content_id' => 1,
                'field_name' => 'name_1',
                'name' => 'hogeName',
                'sort' => 999,
            ]], false],
            [1, [], true],
            [false, ['MailField' => [
                'mail_content_id' => 1,
                'field_name' => 'name_1',
                'name' => 'hogeName',
                'sort' => 999,
            ]], true],
        ];
    }

    /**
     * After Delete
     */
    public function testAfterDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * After Save
     */
    public function testAfterSave()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * testFormatSource
     * @dataProvider formatSourceDataProvider
     */
    public function testFormatSource($source, $expected)
    {
        $result = $this->MailFieldsTable->formatSource($source);
        $this->assertEquals($expected, $result);
    }

    public static function formatSourceDataProvider()
    {
        return [
            ["  １|２|３|４|５", "１\n２\n３\n４\n５"],
            ["１|２ ３|４|５", "１\n２ ３\n４\n５"],
            ["\r１|\r２|３|４|５", "１\n２\n３\n４\n５"],
            ["１\n２\n３\n４\n５", "１\n２\n３\n４\n５"],
            ["１|\n２|３|４|５", "１\n\n２\n３\n４\n５"]
        ];
    }
}
