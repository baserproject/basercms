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
     */
    public function test_getType()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test _getName
     */
    public function test_getName()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test _getBaseName
     */
    public function test_getBaseName()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test _getExt
     */
    public function test_getExt()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test _getContents
     */
    public function test_getContents()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test isNew
     */
    public function test_isNew()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
