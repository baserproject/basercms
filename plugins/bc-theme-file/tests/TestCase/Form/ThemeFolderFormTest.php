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
