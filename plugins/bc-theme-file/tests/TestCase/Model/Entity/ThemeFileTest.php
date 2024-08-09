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

namespace BcThemeFile\Test\TestCase\Model\Entity;
use BaserCore\TestSuite\BcTestCase;
use BcThemeFile\Model\Entity\ThemeFile;

/**
 * Class ThemeFileTest
 */
class ThemeFileTest extends BcTestCase
{
    /**
     * @var ThemeFile
     */
    public $ThemeFile;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemeFile = new ThemeFile(['fullpath' => TMP_TESTS]);
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        //$options['new'] == empty
        $this->assertEquals('/var/www/html/tmp/', $this->ThemeFile->parent);

        //$options['new'] != empty & type = css
        $this->ThemeFile = new ThemeFile(['fullpath' => TMP_TESTS], ['new' => true, 'type' => 'css']);
        $this->assertEquals('/var/www/html/tmp/tests/', $this->ThemeFile->parent);
        $this->assertEquals('css', $this->execPrivateMethod($this->ThemeFile, '_getExt', []));

        //$options['new'] != empty & type はCssとJsではない
        $this->ThemeFile = new ThemeFile(['fullpath' => TMP_TESTS], ['new' => true, 'type' => 'php']);
        $this->assertEquals('/var/www/html/tmp/tests/', $this->ThemeFile->parent);
        $this->assertEquals('php', $this->execPrivateMethod($this->ThemeFile, '_getExt', []));
    }

    /**
     * test _getType
     * @dataProvider getTypeDataProvider
     * @param $fullpath
     * @param $expect
     */
    public function test_getType($fullpath, $expect)
    {
        $this->ThemeFile->fullpath = $fullpath;
        $rs = $this->execPrivateMethod($this->ThemeFile, '_getType', []);
        $this->assertEquals($expect, $rs);
    }

    public static function getTypeDataProvider()
    {
        return [
            ['/var/www/html/plugins/BcThemeSample/templates/layout/default.ctp', 'text'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/default.php', 'text'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/css/default.css', 'text'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/js/default.js', 'text'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/image/default.png', 'image'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/image/default.gif', 'image'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/image/default.jpg', 'image'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/image/default.jpeg', 'image'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/other/default.html', 'file'],
        ];
    }

    /**
     * test _getName
     * @dataProvider getNameDataProvider
     * @param $fullpath
     * @param $expect
     */
    public function test_getName($fullpath, $expect)
    {
        $this->ThemeFile->fullpath = $fullpath;
        $rs = $this->execPrivateMethod($this->ThemeFile, '_getName', []);
        $this->assertEquals($expect, $rs);
    }

    public static function getNameDataProvider()
    {
        return [
            ['/var/www/html/plugins/BcThemeSample/templates/layout/%E3%81%B2%E3%82%89%E3%81%8C%E3%81%AA.ctp', 'ひらがな.ctp'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/カタカナ.php', 'カタカナ.php'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/css/漢字.css', '漢字.css'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/js/default.js', 'default.js'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/image/default.png', 'default.png'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/image/default.gif', 'default.gif'],
            ['/var/www/html/plugins/BcThemeSample/templates/layout/other/default.html', 'default.html'],
        ];
    }

    /**
     * test _getBaseName
     * @dataProvider getBaseNameDataProvider
     * @param $fullpath
     * @param $expect
     *
     */
    public function test_getBaseName($fullpath, $expect)
    {
        $this->ThemeFile->fullpath = $fullpath;
        $rs = $this->execPrivateMethod($this->ThemeFile, '_getBaseName', []);
        $this->assertEquals($expect, $rs);
    }

    public static function getBaseNameDataProvider()
    {
        return [
            ['/var/www/html/tmp/', ''], //isNew = true
            ['/var/www/html/tmp/default.php', 'default'] //isNew = false
        ];
    }

    /**
     * test _getExt
     * @dataProvider getExtDataProvider
     * @param $fullpath
     * @param $expect
     */
    public function test_getExt($fullpath, $expect)
    {
        $this->ThemeFile->fullpath = $fullpath;
        $rs = $this->execPrivateMethod($this->ThemeFile, '_getExt', []);
        $this->assertEquals($expect, $rs);
    }

    public static function getExtDataProvider()
    {
        return [
            ['/var/www/html/tmp/', ''], //isNew = true
            ['/var/www/html/tmp/default.php', 'php'] //isNew = false
        ];
    }

    /**
     * test _getContents
     *
     * @dataProvider getContentsDataProvider
     * @param $fileType
     * @param $fullpath
     * @param $expect
     */
    public function test_getContents($fileType, $fullpath, $expect)
    {
        $this->ThemeFile->type = $fullpath;
        $this->ThemeFile->fullpath = $fullpath;
        $rs = $this->execPrivateMethod($this->ThemeFile, '_getContents', []);
        $this->assertStringContainsString($expect, $rs);
    }

    public static function getContentsDataProvider()
    {
        return [
            ['text', '/var/www/html/plugins/bc-front/webroot/css/colorbox/colorbox-1.6.1.css', '#colorbox, #cboxOverlay, #cboxWrapper{position:absolute; top:0; left:0; z-index:9999; overflow:hidden;}'], //type = text
            ['image', '/var/www/html/tmp/default.image', ''] //type != text
        ];
    }

    /**
     * test isNew
     */
    public function test_isNew()
    {
        $this->ThemeFile->parent = '/var/www/html/tmp/tests/';
        $this->assertTrue($this->ThemeFile->isNew());

        $this->ThemeFile->parent = '/var/www/html/tmp/tests/test.php';
        $this->assertFalse($this->ThemeFile->isNew());
    }
}
