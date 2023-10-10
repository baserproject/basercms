<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.5
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use PHPUnit\Framework\TestCase;

/**
 * Class BcFolderTest
 */
class BcFolderTest extends TestCase
{

    /**
     * test __construct
     * @return void
     */
	public function test__construct()
	{
        $path = TMP_TESTS . 'test';
        $folder = new BcFolder($path);
        $this->assertEquals($path, $folder->getPath());
	}

    /**
     * test Create
     * @return void
     */
    public function testCreate()
    {
        $path = TMP_TESTS . 'test' . DS . 'test';
        $this->assertTrue((new BcFolder($path))->create());
        $this->assertTrue(is_dir($path));
        (new BcFolder(dirname($path)))->delete();
    }

    /**
     * test GetFiles
     * @return void
     */
	public function testGetFiles()
	{
        (new BcFolder(TMP_TESTS . 'test'))->create();
        $path = TMP_TESTS . 'test';
        $path1 = TMP_TESTS . 'test' . DS . 'test1.txt';
        $path2 = TMP_TESTS . 'test' . DS . 'test2.txt';
        $path3 = TMP_TESTS . 'test' . DS . 'test';
        (new BcFile($path1))->create();
        (new BcFile($path2))->create();
        (new BcFolder($path3))->create();
        $this->assertEquals([basename($path1), basename($path2)], (new BcFolder($path))->getFiles());
        $this->assertEquals([$path1, $path2], (new BcFolder($path))->getFiles(['full' => true]));
	}

    /**
     * test GetFolders
     * @return void
     */
	public function testGetFolders()
	{
        (new BcFolder(TMP_TESTS . 'test'))->create();
        $path = TMP_TESTS . 'test';
        $path1 = TMP_TESTS . 'test' . DS . 'test1.txt';
        $path2 = TMP_TESTS . 'test' . DS . 'test2.txt';
        $path3 = TMP_TESTS . 'test' . DS . 'test';
        (new BcFile($path1))->create();
        (new BcFile($path2))->create();
        (new BcFolder($path3))->create();
        $this->assertEquals([basename($path3)], (new BcFolder($path))->getFolders());
        $this->assertEquals([$path3], (new BcFolder($path))->getFolders(['full' => true]));
	}

    /**
     * test Delete
     * @return void
     */
	public function testDelete()
	{
        $child = TMP_TESTS . 'test' . DS . 'test';
        (new BcFolder($child))->create();
        $parent = TMP_TESTS . 'test';
        $this->assertTrue((new BcFolder($parent))->delete());
        $this->assertFalse(is_dir($child));
        $this->assertFalse(is_dir($parent));
	}

}
