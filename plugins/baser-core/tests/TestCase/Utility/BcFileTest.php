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

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;

/**
 * Class BcFileTest
 */
class BcFileTest extends BcTestCase
{

    /**
     * test __construct
     * @return void
     */
	public function test__construct()
	{
        $path = TMP_TESTS . 'test';
        $folder = new BcFile($path);
        $this->assertEquals($path, $folder->getPath());
	}

    /**
     * test Create
     * @return void
     */
	public function testCreate()
	{
        $path = TMP_TESTS . 'test' . DS . 'test.txt';
        $this->assertTrue((new BcFile($path))->create());
        $this->assertTrue(is_file($path));
        (new BcFolder(dirname($path)))->delete();
	}

    /**
     * test CheckParentFolder
     * @return void
     */
    public function testCheckParentFolder()
    {
        $path = TMP_TESTS . 'test' . DS . 'test.txt';
        $this->assertTrue($this->execPrivateMethod(new BcFile($path), 'checkParentFolder'));
        $this->assertTrue(is_dir(dirname($path)));
        (new BcFolder(dirname($path)))->delete();
    }

    /**
     * test WriteAndRead
     * @return void
     */
	public function testWriteAndRead()
    {
        $path = TMP_TESTS . 'test' . DS . 'test.txt';
        $file = new BcFile($path);
        $this->assertTrue($file->write('test'));
        $this->assertEquals('test', $file->read());
        (new BcFolder(dirname($path)))->delete();
    }

    /**
     * test Delete
     * @return void
     */
    public function testDelete()
    {
        $path = TMP_TESTS . 'test' . DS . 'test.txt';
        $file = new BcFile($path);
        $file->create();
        $this->assertTrue($file->delete());
        (new BcFolder(dirname($path)))->delete();
        $this->assertFalse($file->delete());
    }


    /**
     * test size
     * @return void
     */
    public function testSize()
    {
        $path = TMP_TESTS . 'test' . DS . 'test.txt';
        $file = new BcFile($path);
        $file->create();
        $this->assertEquals(0, $file->size());
        $file->write('hello');
        $this->assertEquals(5, $file->size());
        (new BcFolder(dirname($path)))->delete();
        $file->delete();
    }
}
