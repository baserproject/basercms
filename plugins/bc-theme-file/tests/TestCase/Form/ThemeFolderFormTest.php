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