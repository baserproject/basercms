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

use ArrayObject;
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Model\Table\CustomFieldsTable;
use BcCustomContent\Test\Factory\CustomFieldFactory;

/**
 * CustomFieldsTableTest
 * @property CustomFieldsTable $customFieldsTable
 */
class CustomFieldsTableTest extends BcTestCase
{

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->customFieldsTable = $this->getTableLocator()->get('BcCustomContent.CustomFields');
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->customFieldsTable);
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test validationDefault
     */
    public function test_validationDefault()
    {
        $validator = $this->customFieldsTable->getValidator('default');
        //バリディションを発生しない テスト
        $errors = $validator->validate([
            'name' => 'a',
            'title' => 'a',
            'type' => 'group',
            'size' => '',
            'max_length' => '',
            'source' => '',
        ]);
        $this->assertCount(0, $errors);

        //notEmptyString テスト
        $errors = $validator->validate([
            'name' => '',
            'title' => '',
            'type' => '',
            'size' => '',
            'max_length' => '',
            'source' => '',
        ]);
        $this->assertEquals('フィールド名を入力してください。', current($errors['name']));
        $this->assertEquals('項目見出しを入力してください。', current($errors['title']));
        $this->assertEquals('タイプを入力してください。', current($errors['type']));

        //maxLength テスト
        $errors = $validator->validate([
            'name' => str_repeat('a', 256),
            'title' => str_repeat('a', 256)
        ]);
        $this->assertEquals('フィールド名は255文字以内で入力してください。', current($errors['name']));
        $this->assertEquals('項目見出しは255文字以内で入力してください。', current($errors['title']));

        //フィールド名は半角英数字とアンダースコア & 横幅サイズと最大文字数は整数ではない　テスト
        $errors = $validator->validate([
            'name' => 'あ',
            'size' => 'a',
            'line' => 'a',
            'max_length' => 'b',
        ]);
        $this->assertEquals('フィールド名は半角小文字英数字とアンダースコアのみで入力してください。', current($errors['name']));
        $this->assertEquals('横幅サイズは整数を入力してください。', current($errors['size']));
        $this->assertEquals('行数は整数を入力してください。', current($errors['line']));
        $this->assertEquals('最大文字数は整数を入力してください。', current($errors['max_length']));

        //validateUnique　テスト
        CustomFieldFactory::make(['name' => 'test'])->persist();
        $errors = $validator->validate([
            'name' => 'test',
        ]);
        $this->assertEquals('既に登録のあるフィールド名です。', current($errors['name']));

        //validate checkSelectList　テスト
        CustomFieldFactory::make(['name' => 'test'])->persist();
        $errors = $validator->validate([
            'source' => "あ\rべ\r\nあ\nべ\ntest",
        ]);
        $this->assertEquals('選択リストに同じ項目を複数登録できません。', current($errors['source']));
    }

    /**
     * test beforeMarshal
     */
    public function test_beforeMarshal()
    {
        //case true
        $data = [
            'meta' => [
                'test' => 'test'
            ],
            'validate' => [
                'test' => 'test'
            ]
        ];
        $options = new \ArrayObject();
        $content = new \ArrayObject($data);
        $this->customFieldsTable->dispatchEvent('Model.beforeMarshal', ['entity' => $content, 'options' => $options]);
        $this->assertEquals('{"test":"test"}', $content['meta']);
        $this->assertEquals('{"test":"test"}', $content['validate']);
        //case false
        $data = [
            'meta' => '',
            'validate' => ''
        ];
        $options = new \ArrayObject();
        $content = new \ArrayObject($data);
        $this->customFieldsTable->dispatchEvent('Model.beforeMarshal', ['entity' => $content, 'options' => $options]);
        $this->assertEquals('', $content['meta']);
        $this->assertEquals('', $content['validate']);
    }

    /**
     * test findAll
     */
    public function test_findAll()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
