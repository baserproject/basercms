<?php

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcZip;
use ZipArchive;

class BcZipTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcZip = new BcZip();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test create
     */
    public function testCreate()
    {
        // create zip file
        $zipSrcPath = TMP . 'zip' . DS;
        mkdir($zipSrcPath);

        file_put_contents($zipSrcPath . '/test.txt', 'Test content');
        $zipFile = sys_get_temp_dir() . '/test.zip';

        $this->BcZip->create($zipSrcPath, $zipFile);
        $this->assertFileExists($zipFile);

        // check zip file
        $za = new \ZipArchive();
        $za->open($zipFile);
        $this->assertTrue($za->locateName('test.txt') !== false);
        $za->close();

        // clean up
        unlink($zipFile);
        unlink($zipSrcPath . '/test.txt');
        rmdir($zipSrcPath);
    }

    /**
     * test _extractByPhpLib
     */
    public function testExtractByPhpLib()
    {
       $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }


    /**
     * test _escapePath
     * @param $path
     * @param $expected
     * @dataProvider escapePathDataProvider
     */
    public function test_escapePath($path, $expected)
    {
        $result = $this->execPrivateMethod($this->BcZip, '_escapePath', [$path]);
        $this->assertEquals($expected, $result);
    }

    public static function escapePathDataProvider()
    {
        return [
            ['/var/www/html', "''/'var'/'www'/'html'"],
            ['/path/to/some file.txt', "''/'path'/'to'/'some file.txt'"],
            ['/', "''/''"],
            ['', "''"],
            [
                '/path/with/$pecial&chars',
                "''/'path'/'with'/'\$pecial&chars'",
            ],
        ];
    }

    /**
     * test zipSub
     */
    public function testZipSub()
    {
        //create zip file
        $tempDir = TMP . 'zip_test_' . uniqid();
        $subDir = $tempDir . DS . 'subdir';
        $zipFile = TMP . 'test_archive.zip';

        mkdir($tempDir, 0755, true);
        mkdir($subDir, 0755, true);

        file_put_contents($tempDir . DS . 'file1.txt', 'Content of file 1');
        file_put_contents($subDir . DS . 'file2.txt', 'Content of file 2');

        $this->BcZip->create($tempDir, $zipFile);

        //check zip file
        $this->assertFileExists($zipFile);

        //check content of zip file
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipFile));

        // 3 files: file1.txt, subdir/file2.txt, subdir
        $this->assertEquals(3, $zip->numFiles);

        $this->assertTrue($zip->locateName('file1.txt') !== false);
        $this->assertTrue($zip->locateName('subdir/file2.txt') !== false);

        $fileContent1 = $zip->getFromName('file1.txt');
        $fileContent2 = $zip->getFromName('subdir/file2.txt');

        $this->assertEquals('Content of file 1', $fileContent1);
        $this->assertEquals('Content of file 2', $fileContent2);

        $zip->close();

        //clean up
        unlink($zipFile);
        $folder = new BcFolder($tempDir);
        $folder->delete();
    }
}
