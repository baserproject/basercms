<?php

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
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
       $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }


    public function test_escapePath()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
