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

    /**
     * test chmod
     */
    public function test_chmod()
    {
        $path = TMP_TESTS . 'test' . DS . 'chmod';
        $folder = new BcFolder($path);
        $folder->create();
        $folder->chmod(0777);
        $this->assertEquals('0777', substr(sprintf('%o', fileperms($path)), -4));
        $folder->chmod(0775);
        $this->assertEquals('0775', substr(sprintf('%o', fileperms($path)), -4));
        $folder->delete();
    }

    /**
     * test tree
     */
    public function test_tree()
    {
        $path = TMP_TESTS . 'test' . DS . 'tree';
        $folder = new BcFolder($path);
        $folder->create();
        $result = $folder->tree();
        $this->assertEquals($path, $result[0][0]);
        (new BcFile($path. DS . 'test.txt'))->create();
        $result = $folder->tree();
        $this->assertEquals($path, $result[0][0]);
        $this->assertEquals($path. DS . 'test.txt', $result[1][0]);
        $folder->delete();
    }

    /**
     * test copy
     */
    public function test_copy()
    {
        $path = TMP_TESTS . 'test';
        $folder1 = new BcFolder($path);
        $folder1->create();
        (new BcFolder($path .DS. 'test1'))->create();
        (new BcFile($path .DS. 'test1' .DS. 'test1.txt'))->create();
        $file = new BcFile($path. DS. 'test.txt');
        $file->create();
        $des = TMP_TESTS . 'test_des';
        $result = $folder1->copy($des);
        $this->assertTrue($result);
        $this->assertFileExists($des. DS. 'test.txt');
        $this->assertFileExists($des. DS. 'test1' .DS. 'test1.txt');
        $folder1->delete();
        (new BcFolder($des))->delete();
    }

    /**
     * test move
     */
    public function test_move()
    {
        $path = TMP_TESTS . 'test';
        $folder1 = new BcFolder($path);
        $folder1->create();
        $file = new BcFile($path. DS. 'test.txt');
        $file->create();
        $des = TMP_TESTS . 'test_des';
        $folder2 = new BcFolder($des);
        $folder2->create();
        $result = $folder1->move($des);
        $this->assertTrue($result);
        $this->assertFileDoesNotExist($path. DS. 'test.txt');
        $this->assertFileExists($des. DS. 'test.txt');
        $folder2->delete();
    }

    /**
     * test pwd
     * @param string $path
     * @param string $expected
     * @dataProvider pwdDataProvider
     */
    public function test_pwd($path, $expected)
    {
        $bcFolder = new BcFolder($path);
        $result = $bcFolder->pwd();
        $this->assertEquals($expected, $result);
    }

    public static function pwdDataProvider()
    {
        return [
            ['/', '/'],
            ['/var/www/html', '/var/www/html'],
            ['plugins/baser-core', 'plugins/baser-core'],
            ['/path/with spaces/folder', '/path/with spaces/folder'],
            ['', ''],
        ];
    }


    /**
     * test find
     * no pattern all files
     */
    public function testFindReturnsAllFilesWithDefaultPattern()
    {
        //create a test folder
        $testPath = TMP_TESTS . 'test';
        (new BcFolder($testPath))->create();

        //create some test files
        file_put_contents($testPath . '/file1.txt', 'Test file 1');
        file_put_contents($testPath . '/file2.txt', 'Test file 2');
        file_put_contents($testPath . '/file3.txt', 'Test file 3');

        $BcFolder = new BcFolder($testPath);
        $result = $BcFolder->find();

        //check
        $this->assertCount(3, $result);
        $this->assertContains('file1.txt', $result);
        $this->assertContains('file2.txt', $result);
        $this->assertContains('file3.txt', $result);

        // Clean up
        (new BcFolder($testPath))->delete();
    }

    /**
     * test find
     * matching files
     */
    public function testFindReturnsMatchingFiles()
    {
        //create a test folder
        $testPath = TMP_TESTS . 'test';
        (new BcFolder($testPath))->create();

        //create some test files
        file_put_contents($testPath . '/file1.txt', 'Test file 1');
        file_put_contents($testPath . '/file2.txt', 'Test file 2');
        file_put_contents($testPath . '/test_file.txt', 'Test file 3');

        $BcFolder = new BcFolder($testPath);
        $result = $BcFolder->find('test_file.txt');

        //check
        $this->assertCount(1, $result);
        $this->assertContains('test_file.txt', $result);

        //cleanup
        (new BcFolder($testPath))->delete();
    }

    /**
     * test find
     * no matching files
     */
    public function testFindReturnsNoFilesForNonMatchingPattern()
    {
        //create a test folder
        $testPath = TMP_TESTS . 'test';
        (new BcFolder($testPath))->create();

        //create some test files
        file_put_contents($testPath . '/file1.txt', 'Test file 1');
        file_put_contents($testPath . '/file2.txt', 'Test file 2');

        $BcFolder = new BcFolder($testPath);
        $result = $BcFolder->find('non_existent_file.txt');

        //check
        $this->assertCount(0, $result);

        // Clean up
        (new BcFolder($testPath))->delete();
    }
}
