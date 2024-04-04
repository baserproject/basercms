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

namespace BcCustomContent\Test\TestCase\Model\Table;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Model\Table\CustomFieldsTable;

/**
 * CustomFieldsTableTest
 */
class CustomFieldsTableTest extends BcTestCase
{

    /**
     * @var CustomFieldsTable
     */
    public $CustomFieldsTable;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomFieldsTable = $this->getTableLocator()->get('BcCustomContent.CustomFields');
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomFieldsTable);
        parent::tearDown();
    }

    public function test_validationDefault()
    {
        $validator = $this->CustomFieldsTable->getValidator('default');
        //入力フィールドのデータが超えた場合、
        $errors = $validator->validate([
            'name' => str_repeat('a', 256),
            'title' => str_repeat('a', 256)
        ]);
        //戻り値を確認
        $this->assertEquals('フィールド名は255文字以内で入力してください。', current($errors['name']));
        $this->assertEquals('項目見出しは255文字以内で入力してください。', current($errors['title']));

        //入力フィールドのデータがNULL場合、
        $errors = $validator->validate([
            'name' => '',
            'title' => '',
            'type' => '',
        ]);
        //戻り値を確認
        $this->assertEquals('フィールド名を入力してください。', current($errors['name']));
        $this->assertEquals('項目見出しを入力してください。', current($errors['title']));
        $this->assertEquals('タイプを入力してください。', current($errors['type']));

        //フィールド名は半角小文字英数字とアンダースコアのみ利用可能
        $errors = $validator->validate([
            'name' => 'test sss',
        ]);
        //戻り値を確認
        $this->assertEquals('フィールド名は半角小文字英数字とアンダースコアのみで入力してください。', current($errors['name']));
        $errors = $validator->validate([
            'name' => 'ひらがな',
        ]);
        //戻り値を確認
        $this->assertEquals('フィールド名は半角小文字英数字とアンダースコアのみで入力してください。', current($errors['name']));

        //trueを返す
        $errors = $validator->validate([
            'name' => 'test_test',
        ]);
        //戻り値を確認
        $this->assertArrayNotHasKey('name', $errors);

        //Eメール比較先フィールド名のバリデーション
        //trueを返す
        $errors = $validator->validate([
            'meta' => '{"BcCustomContent":{"email_confirm":"aaaa_bbb","max_file_size":"","file_ext":""}}'
        ]);
        //戻り値を確認
        $this->assertArrayNotHasKey('meta', $errors);

        //全角文字
        $errors = $validator->validate([
            'meta' => '{"BcCustomContent":{"email_confirm":"ああ","max_file_size":"","file_ext":""}}'
        ]);
        //戻り値を確認
        $this->assertEquals('Eメール比較先フィールド名は半角小文字英数字とアンダースコアのみで入力してください。', current($errors['meta']));
    }
}
