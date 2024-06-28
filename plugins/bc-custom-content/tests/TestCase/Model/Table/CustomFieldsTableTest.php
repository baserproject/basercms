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
use Cake\Routing\Router;

/**
 * CustomFieldsTableTest
 * @property CustomFieldsTable $CustomFieldsTable
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

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertTrue($this->CustomFieldsTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->CustomFieldsTable->hasAssociation('CustomLinks'));
    }

    /**
     * test validationDefault
     */
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
        $request = $this->getRequest('/')->withData('validate', ['EMAIL_CONFIRM', 'FILE_EXT', 'MAX_FILE_SIZE']);
        Router::setRequest($request);
        $errors = $validator->validate([
            'meta' => '{"BcCustomContent":{"email_confirm":"aaaa_bbb","max_file_size":"100","file_ext":"png"}}'
        ]);
        //戻り値を確認
        $this->assertArrayNotHasKey('meta', $errors);

        //全角文字
        $errors = $validator->validate([
            'meta' => '{"BcCustomContent":{"email_confirm":"ああ","max_file_size":"ああ","file_ext":"ああ"}}'

        ]);
        //戻り値を確認
        $this->assertEquals('Eメール比較先フィールド名は半角小文字英数字とアンダースコアのみで入力してください。', $errors['meta']['checkAlphaNumericWithJson']);
        $this->assertEquals('ファイルアップロードサイズ上限は整数値のみで入力してください。', $errors['meta']['checkMaxFileSizeWithJson']);
        $this->assertEquals('拡張子を次の形式のようにカンマ（,）区切りで入力します。', $errors['meta']['checkFileExtWithJson']);
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
        $this->CustomFieldsTable->dispatchEvent('Model.beforeMarshal', ['entity' => $content, 'options' => $options]);
        $this->assertEquals('{"test":"test"}', $content['meta']);
        $this->assertEquals('{"test":"test"}', $content['validate']);
        //case false
        $data = [
            'meta' => '',
            'validate' => ''
        ];
        $options = new \ArrayObject();
        $content = new \ArrayObject($data);
        $this->CustomFieldsTable->dispatchEvent('Model.beforeMarshal', ['entity' => $content, 'options' => $options]);
        $this->assertEquals('', $content['meta']);
        $this->assertEquals('', $content['validate']);
    }

    /**
     * test afterMarshal
     */
    public function test_afterMarshal()
    {
        $request = $this->getRequest('/')->withData('validate', ['EMAIL_CONFIRM', 'FILE_EXT', 'MAX_FILE_SIZE']);
        Router::setRequest($request);

        $customFields = $this->CustomFieldsTable->newEntity(['meta' => ['BcCustomContent' => ['email_confirm' => '全角文字', 'max_file_size' => 'abc', 'file_ext' => 'avc']]]);
        $result = $this->CustomFieldsTable->dispatchEvent('Model.afterMarshal', ['entity' => $customFields, 'data' => new \ArrayObject(), 'options' => new \ArrayObject()]);
        $customFields = $result->getData('entity');
        //エラー情報を正しい状態に戻すことを確認
        $errors = $customFields->getErrors();
        $this->assertEquals('Eメール比較先フィールド名は半角小文字英数字とアンダースコアのみで入力してください。', $errors['meta.BcCustomContent.email_confirm']['checkAlphaNumericWithJson']);
    }

    /**
     * test findAll
     */
    public function test_findAll()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test encodeEntity
     */
    public function test_encodeEntity()
    {
        $entity = new ArrayObject(['meta' => ['key' => 'value'], 'validate' => ['rule' => 'notEmpty']]);
        $result = $this->CustomFieldsTable->encodeEntity($entity);
        $this->assertEquals(json_encode(['key' => 'value'], JSON_UNESCAPED_UNICODE), $result['meta']);
        $this->assertEquals(json_encode(['rule' => 'notEmpty'], JSON_UNESCAPED_UNICODE), $result['validate']);

        //meta empty and validate empty
        $entity = new ArrayObject(['meta' => [], 'validate' => []]);
        $result = $this->CustomFieldsTable->encodeEntity($entity);
        $this->assertEmpty($result['meta']);
        $this->assertEmpty($result['validate']);

        //without meta and validate
        $entity = new ArrayObject(['other' => 'value']);
        $result = $this->CustomFieldsTable->encodeEntity($entity);
        $this->assertEquals('value', $result['other']);
        $this->assertArrayNotHasKey('meta', $result);
        $this->assertArrayNotHasKey('validate', $result);
    }

}
