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

namespace BcThemeFile\Test\TestCase\Form;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFile;
use BcThemeFile\Form\ThemeFileForm;
use Cake\Form\Schema;

/**
 * Class PluginTest
 * @property ThemeFileForm $ThemeFileForm
 */
class ThemeFileFormTest extends BcTestCase
{
    /**
     * @var ThemeFileForm
     */
    public $ThemeFileForm;


    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemeFileForm = new ThemeFileForm();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ThemeFileForm);
        parent::tearDown();
    }

    /**
     * test buildScheme
     */
    public function test_buildSchema()
    {
        $rs = $this->execPrivateMethod($this->ThemeFileForm, '_buildSchema', [new Schema()]);
        $this->assertEquals('string', $rs->fieldType('fullpath'));
        $this->assertEquals('string', $rs->fieldType('name'));
        $this->assertEquals('string', $rs->fieldType('base_name'));
        $this->assertEquals('string', $rs->fieldType('ext'));
        $this->assertEquals('string', $rs->fieldType('type'));
        $this->assertEquals('string', $rs->fieldType('path'));
        $this->assertEquals('string', $rs->fieldType('parent'));
        $this->assertEquals('string', $rs->fieldType('contents'));
    }

    /**
     * test _execute
     */
    public function test_execute()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test validationDefault
     */
    public function test_validationDefault()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test duplicateThemeFile
     */
    public function test_duplicateThemeFile()
    {
        //$value = false
        $rs = $this->ThemeFileForm->duplicateThemeFile(false);
        $this->assertTrue($rs);

        //mode != create
        $context['data']['mode'] = 'update';
        $rs = $this->ThemeFileForm->duplicateThemeFile('test', $context);
        $this->assertTrue($rs);

        //テストの準備
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        $context['data'] = [
            'mode' => 'create',
            'fullpath' => $fullpath,
            'parent' => $fullpath,
            'base_name' => 'test',
            'ext' => 'php',
            'contents' => "<?php echo 'test' ?>"
        ];

        //既にファイルが存在した場合、
        $file = new BcFile($fullpath . 'test.php');
        $file->create();
        $rs = $this->ThemeFileForm->duplicateThemeFile('test', $context);
        $this->assertFalse($rs);
        unlink($fullpath . 'test.php');

        //ファイルが存在しない場合
        $rs = $this->ThemeFileForm->duplicateThemeFile('test', $context);
        $this->assertTrue($rs);
    }
}
