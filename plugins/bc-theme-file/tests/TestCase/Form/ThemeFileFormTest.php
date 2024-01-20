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
        $fullpath = '/var/www/html/plugins/BcColumn/templates/layout/';
        //新規追加テスト
        $data = [
            'mode' => 'create',
            'parent' => $fullpath,
            'base_name' => 'test_add',
            'ext' => 'php',
            'contents' => 'test',
            'fullpath' => $fullpath
        ];
        $rs = $this->execPrivateMethod($this->ThemeFileForm, '_execute', [$data]);
        //戻る値を確認
        $this->assertTrue($rs);
        //実際にファイルが作成されいてるか確認すること
        $this->assertTrue(file_exists($fullpath . 'test_add.php'));
        //ファイルの中身を確認
        $this->assertEquals('test', file_get_contents($fullpath . 'test_add.php'));

        //編集テスト
        $data = [
            'mode' => 'update',
            'parent' => $fullpath,
            'base_name' => 'test_update',
            'ext' => 'php',
            'contents' => 'test update',
            'fullpath' => $fullpath . 'test_add.php',
        ];
        $rs = $this->execPrivateMethod($this->ThemeFileForm, '_execute', [$data]);
        //戻る値を確認
        $this->assertTrue($rs);
        //実際にファイルが変更されいてるか確認すること
        $this->assertFalse(file_exists($fullpath . 'test_add.php'));
        $this->assertTrue(file_exists($fullpath . 'test_update.php'));
        //ファイルの中身を確認
        $this->assertEquals('test update', file_get_contents($fullpath . 'test_update.php'));

        //作成されたファイルを削除
        unlink($fullpath . 'test_update.php');

        //modeは['create', 'update']ではない場合、
        $data = [
            'mode' => 'new',
            'parent' => $fullpath,
            'base_name' => 'test_update',
            'ext' => 'php',
            'contents' => 'test update',
            'fullpath' => $fullpath . 'test_add.php',
        ];
        $rs = $this->execPrivateMethod($this->ThemeFileForm, '_execute', [$data]);
        //戻る値を確認
        $this->assertFalse($rs);
    }

    /**
     * test validationDefault
     */
    public function test_validationDefault()
    {
        $validator = $this->ThemeFileForm->getValidator('default');
        //base_nameを入力しない場合、
        $errors = $validator->validate([
            'base_name' => ''
        ]);
        $this->assertEquals('テーマファイル名を入力してください。', current($errors['base_name']));
        $errors = $validator->validate([
        ]);
        $this->assertEquals('テーマファイル名を入力してください。', current($errors['base_name']));

        //無効な文字を入力した場合、
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        $postData = [
            'mode' => 'create',
            'fullpath' => $fullpath,
            'parent' => $fullpath,
            'base_name' => 't e s t',
            'ext' => 'php',
            'contents' => "<?php echo 'test' ?>"
        ];
        $errors = $validator->validate($postData);
        $this->assertEquals('テーマファイル名は半角英数字とハイフン、アンダースコアのみが利用可能です。', current($errors['base_name']));

        //既にファイルが存在した場合、
        $file = new BcFile($fullpath . 'test.php');
        $file->create();
        $postData['base_name'] = 'test';
        $errors = $validator->validate($postData);
        $this->assertEquals('入力されたテーマファイル名は、同一階層に既に存在します。', current($errors['base_name']));
        unlink($fullpath . 'test.php');
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
