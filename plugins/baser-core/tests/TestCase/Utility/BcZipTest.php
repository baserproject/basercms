<?php

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcZip;

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
        // create zip file
        $zipSrcPath = TMP . 'zip' . DS;
        $sourceZip = $zipSrcPath . 'test.zip';
        $targetPath = TMP . 'extracted' . DS;

        if (!file_exists($zipSrcPath)) {
            mkdir($zipSrcPath, 0777, true);
        }
        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0777, true);
        }

        $zip = new \ZipArchive();
        $zip->open($sourceZip, \ZipArchive::CREATE);
        $zip->addFromString('testfolder' . DS . 'testfile.txt', 'This is a test file.');
        $zip->close();

        $result = $this->execPrivateMethod($this->BcZip, '_extractByPhpLib', [$sourceZip, $targetPath]);

        $this->assertTrue($result);
        $this->assertEquals('testfolder', $this->BcZip->topArchiveName);

        // check extracted file
        $extractedFile = $targetPath . 'testfolder' . DS . 'testfile.txt';
        $this->assertFileExists($extractedFile);
        $this->assertEquals('This is a test file.', file_get_contents($extractedFile));

        // clean up
        $folder = new BcFolder($zipSrcPath);
        $folder->delete();
        $folder = new BcFolder($targetPath);
        $folder->delete();
    }

    /**
     * test testExtractByPhpLibReturnsFalse
     */
    public function testExtractByPhpLibReturnsFalse()
    {
        $zipSrcPath = TMP . 'zip' . DS;
        $sourceZip = $zipSrcPath . 'invalid.zip';
        $targetPath = TMP . 'extracted' . DS;

        if (!file_exists($zipSrcPath)) {
            mkdir($zipSrcPath, 0777, true);
        }
        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0777, true);
        }

        $result = $this->execPrivateMethod($this->BcZip, '_extractByPhpLib', [$sourceZip, $targetPath]);

        $this->assertFalse($result);
        //check target path is empty
        $this->assertEmpty(glob($targetPath . '*'));

        // clean up
        $folder = new BcFolder($zipSrcPath);
        $folder->delete();
        $folder = new BcFolder($targetPath);
        $folder->delete();
    }

    public function test_escapePath()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
