<?php

namespace BcThemeFile\Test\TestCase\Form;

use BaserCore\TestSuite\BcTestCase;
use BcThemeFile\Form\ThemeFolderForm;
use Cake\Form\Schema;

class ThemeFolderFormTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemeFolderForm = new ThemeFolderForm();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test validationDefault
     */
    public function test_validationDefault()
    {
        $validator = $this->ThemeFolderForm->getValidator('default');
        //フォルダ名を入力しない
        $errors = $validator->validate(['name' => '', 'mode' => 'create']);
        //戻り値を確認
        $this->assertEquals('フォルダ名を入力してください。', current($errors['name']));

        //同一階層に既に存在
        $errors = $validator->validate(['name' => 'plugins', 'mode' => 'create', 'parent' => '']);
        //戻り値を確認
        $this->assertEquals('入力されたフォルダ名は、同一階層に既に存在します。', current($errors['name']));

        //同一階層に既に存在
        $errors = $validator->validate(['name' => 'あいうえお', 'mode' => 'create', 'parent' => '']);
        //戻り値を確認
        $this->assertEquals('フォルダ名は半角英数字とハイフン、アンダースコアのみが利用可能です。', current($errors['name']));

        //エラーがない
        $errors = $validator->validate(['name' => 'test', 'mode' => 'create', 'parent' => '']);
        //戻り値を確認
        $this->assertCount(0, $errors);
    }

    public function test_buildSchema()
    {
        $schema = $this->execPrivateMethod($this->ThemeFolderForm, '_buildSchema', [new Schema()]);
        $this->assertEquals('string', $schema->fieldType('fullpath'));
        $this->assertEquals('string', $schema->fieldType('name'));
        $this->assertEquals('string', $schema->fieldType('parent'));
    }

    /**
     * test _execute
     */
    public function test_execute()
    {
        $data['fullpath'] = TMP;
        //フォルダの作成　テスト
        $data['name'] = 'test_create';
        $data['mode'] = 'create';
        $rs = $this->execPrivateMethod($this->ThemeFolderForm, '_execute', [$data]);
        //フォルダが作成できるか確認すること
        $this->assertTrue($rs);
        $this->assertTrue(is_dir(TMP . DS . 'test_create'));

        //フォルダのリネーム　テスト
        $data['fullpath'] = TMP . DS . 'test_create';
        $data['name'] = 'test_update';
        $data['mode'] = 'update';
        $rs = $this->execPrivateMethod($this->ThemeFolderForm, '_execute', [$data]);
        //戻り値確認
        $this->assertTrue($rs);
        //フォルダがリネームできるか確認すること
        $this->assertTrue(is_dir(TMP . DS . 'test_update'));
        $this->assertFalse(is_dir(TMP . DS . 'test_create'));

        //作成したフォルダを削除
        rmdir(TMP . DS . 'test_update');
        $this->assertFalse(is_dir(TMP . DS . 'test_update'));
    }

    public function test_duplicateThemeFolderEmptyValue(){
        $rs = $this->ThemeFolderForm->duplicateThemeFolder('');
        $this->assertTrue($rs);
    }

    public function test_duplicateThemeFolderWithoutModeCreate(){
        //mode != create
        $context['data']['mode'] = 'update';
        $rs = $this->ThemeFolderForm->duplicateThemeFolder('test', $context);
        $this->assertTrue($rs);
    }

    public function test_duplicateThemeFolderAlreadyExists()
    {
        $fullPath = BASER_PLUGINS . 'BcThemeSample' . '/templates/';
        $context = ['data' => ['mode' => 'create', 'parent' => $fullPath]];
        $result = $this->ThemeFolderForm->duplicateThemeFolder('layout', $context);
        $this->assertFalse($result);
    }

    public function test_duplicateThemeFolderDoesNotExist()
    {
        $fullPath = BASER_PLUGINS . 'BcThemeSample' . '/templates/';
        $context = ['data' => ['mode' => 'create', 'parent' => $fullPath]];
        $result = $this->ThemeFolderForm->duplicateThemeFolder('test', $context);
        $this->assertTrue($result);
    }
}
