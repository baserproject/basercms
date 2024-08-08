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
use BcThemeFile\Model\Entity\ThemeFolder;

/**
 * Class ThemeFolderTest
 */
class ThemeFolderTest extends BcTestCase
{
    /**
     * @var ThemeFolder
     */
    public $ThemeFolder;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemeFolder = new ThemeFolder(['fullpath' => TMP_TESTS]);
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        //$options['new'] == empty
        $this->assertEquals('/var/www/html/tmp/', $this->ThemeFolder->parent);

        //$options['new'] != empty
        $this->ThemeFolder = new ThemeFolder(['fullpath' => TMP_TESTS], ['new' => true]);
        $this->assertEquals('/var/www/html/tmp/tests/', $this->ThemeFolder->parent);
    }

    /**
     * test _getName
     */
    public function test_getName()
    {
        $this->assertEquals('tests', $this->execPrivateMethod($this->ThemeFolder, '_getName', []));
    }

}
