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
}