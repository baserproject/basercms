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


    public function test_validationDefault()
    {
        $validator = $this->ThemeFolderForm->getValidator('default');

        $errors = $validator->validate(['name' => '']);
        $this->assertEquals('フォルダ名を入力してください。', current($errors['name']));

        $errors = $validator->validate([]);
        $this->assertEquals('フォルダ名を入力してください。', current($errors['name']));

        //duplicateThemeFolder
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/';
        $postData = [
            'mode' => 'create',
            'parent' => $fullpath,
            'name' => 'layout',
        ];
        $errors = $validator->validate($postData);
        $this->assertEquals('入力されたフォルダ名は、同一階層に既に存在します。', current($errors['name']));

        //nameAlphaNumericPlus
        $postData = [
            'mode' => 'create',
            'parent' => 'layout',
            'name' => 't e s t',
        ];
        $errors = $validator->validate($postData);
        $this->assertEquals('フォルダ名は半角英数字とハイフン、アンダースコアのみが利用可能です。', current($errors['name']));
    }
}