<?php

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFile;
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
     * test __construct
     */
    public function test__construct()
    {
        $this->assertNotFalse($this->BcZip->Zip);
    }

    /**
     * test create
     */
    public function testCreate()
    {
        // ZIPファイルを作成
        $zipSrcPath = TMP . 'zip' . DS;
        mkdir($zipSrcPath);

        file_put_contents($zipSrcPath . '/test.txt', 'Test content');
        $zipFile = sys_get_temp_dir() . '/test.zip';

        $this->BcZip->create($zipSrcPath, $zipFile);
        $this->assertFileExists($zipFile);

        // ZIPファイルを確認
        $za = new \ZipArchive();
        $za->open($zipFile);
        $this->assertTrue($za->locateName('test.txt') !== false);
        $za->close();

        // クリーンアップ
        unlink($zipFile);
        unlink($zipSrcPath . '/test.txt');
        rmdir($zipSrcPath);
    }

    /**
     * test extract
     */
    public function testExtract()
    {
        //準備
        (new BcFolder(TMP_TESTS . 'test'))->create();
        $path1 = TMP_TESTS . 'test' . DS . 'test1.txt';
        $path2 = TMP_TESTS . 'test' . DS . 'test2.txt';
        (new BcFile($path1))->create();
        (new BcFile($path2))->create();
        //テストzipファイルを生成
        $this->BcZip->create(TMP_TESTS . 'test', TMP_TESTS . 'test_extract.zip');
        //テストzipファイルが生成できるか確認
        $this->assertFileExists(TMP_TESTS, 'test_extract.zip');

        //ZIP を展開する
        $this->BcZip->extract(TMP_TESTS . 'test_extract.zip', TMP_TESTS);

        //ZIP が展開できるか確認すること
        $this->assertFileExists(TMP_TESTS . 'test1.txt');
        $this->assertFileExists(TMP_TESTS . 'test2.txt');

        //不要なファイルを削除
        unlink(TMP_TESTS . 'test1.txt');
        unlink(TMP_TESTS . 'test2.txt');
        unlink(TMP_TESTS . 'test_extract.zip');
        (new BcFolder(TMP_TESTS . 'test'))->delete();

        //存在しないファイルを展開する場合、
        $rs = $this->BcZip->extract(TMP_TESTS . 'test_extract.zip', TMP_TESTS);
        //戻り値を確認
        $this->assertFalse($rs);
    }

    /**
     * test _extractByPhpLib
     */
    public function testExtractByPhpLib()
    {
        // ZIPファイルを作成
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

        // 展開されたファイルを確認
        $extractedFile = $targetPath . 'testfolder' . DS . 'testfile.txt';
        $this->assertFileExists($extractedFile);
        $this->assertEquals('This is a test file.', file_get_contents($extractedFile));

        // クリーンアップ
        $folder = new BcFolder($zipSrcPath);
        $folder->delete();
        $folder = new BcFolder($targetPath);
        $folder->delete();
    }

    /**
     * test ExtractByPhpLibReturnsFalse
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
        // ターゲットパスが空であることを確認
        $this->assertEmpty(glob($targetPath . '*'));

        // クリーンアップ
        $folder = new BcFolder($zipSrcPath);
        $folder->delete();
        $folder = new BcFolder($targetPath);
        $folder->delete();
    }

    /**
     * test ExtractByPhpLibRejectsZipSlip
     */
    public function testExtractByPhpLibRejectsZipSlip()
    {
        $zipSrcPath = TMP . 'zip' . DS;
        $sourceZip = $zipSrcPath . 'zipslip.zip';
        $targetPath = TMP . 'extracted' . DS;

        if (!file_exists($zipSrcPath)) {
            mkdir($zipSrcPath, 0777, true);
        }
        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0777, true);
        }

        $zip = new \ZipArchive();
        $zip->open($sourceZip, \ZipArchive::CREATE);
        $zip->addFromString('../evil.txt', 'nope');
        $zip->close();

        $result = $this->execPrivateMethod($this->BcZip, '_extractByPhpLib', [$sourceZip, $targetPath]);

        $this->assertFalse($result);
        $this->assertFileDoesNotExist($targetPath . 'evil.txt');
        $this->assertFileDoesNotExist(TMP . 'evil.txt');

        $folder = new BcFolder($zipSrcPath);
        $folder->delete();
        $folder = new BcFolder($targetPath);
        $folder->delete();
    }

    /**
     * test NormalizeTargetPathAllowsMissingTarget
     */
    public function testNormalizeTargetPathAllowsMissingTarget()
    {
        $parent = TMP . 'zip_target_parent' . DS;
        if (!file_exists($parent)) {
            mkdir($parent, 0777, true);
        }

        $target = $parent . 'child' . DS;
        $result = $this->execPrivateMethod($this->BcZip, '_normalizeTargetPath', [$target]);

        $expectedParent = rtrim(str_replace('\\', '/', realpath($parent)), '/');
        $this->assertEquals($expectedParent . '/child', $result);

        $folder = new BcFolder($parent);
        $folder->delete();
    }

    /**
     * test IsZipEntrySafeRejectsInvalidEntries
     */
    public function testIsZipEntrySafeRejectsInvalidEntries()
    {
        $targetDir = TMP . 'zip_safe_target' . DS;
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $targetPath = rtrim(str_replace('\\', '/', realpath($targetDir)), '/');

        $this->assertFalse($this->execPrivateMethod($this->BcZip, '_isZipEntrySafe', ['/etc/passwd', $targetPath]));
        $this->assertFalse($this->execPrivateMethod($this->BcZip, '_isZipEntrySafe', ['C:/Windows/system.ini', $targetPath]));
        $this->assertFalse($this->execPrivateMethod($this->BcZip, '_isZipEntrySafe', ["evil\0.txt", $targetPath]));
        $this->assertFalse($this->execPrivateMethod($this->BcZip, '_isZipEntrySafe', ['../evil.txt', $targetPath]));
        $this->assertTrue($this->execPrivateMethod($this->BcZip, '_isZipEntrySafe', ['good/file.txt', $targetPath]));
        
        // ディレクトリエントリ
        $this->assertTrue($this->execPrivateMethod($this->BcZip, '_isZipEntrySafe', ['folder/', $targetPath]));
        $this->assertFalse($this->execPrivateMethod($this->BcZip, '_isZipEntrySafe', ['/', $targetPath]));
        $this->assertFalse($this->execPrivateMethod($this->BcZip, '_isZipEntrySafe', ['../folder/', $targetPath]));

        $folder = new BcFolder($targetDir);
        $folder->delete();
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
        // ZIPファイルを作成
        $tempDir = TMP . 'zip_test_' . uniqid();
        $subDir = $tempDir . DS . 'subdir';
        $zipFile = TMP . 'test_archive.zip';

        mkdir($tempDir, 0755, true);
        mkdir($subDir, 0755, true);

        file_put_contents($tempDir . DS . 'file1.txt', 'Content of file 1');
        file_put_contents($subDir . DS . 'file2.txt', 'Content of file 2');

        $this->BcZip->create($tempDir, $zipFile);

        // ZIPファイルを確認
        $this->assertFileExists($zipFile);

        // ZIPファイルの内容を確認
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipFile));

        // 3つのファイル: file1.txt, subdir/file2.txt, subdir
        $this->assertEquals(3, $zip->numFiles);

        $this->assertTrue($zip->locateName('file1.txt') !== false);
        $this->assertTrue($zip->locateName('subdir/file2.txt') !== false);

        $fileContent1 = $zip->getFromName('file1.txt');
        $fileContent2 = $zip->getFromName('subdir/file2.txt');

        $this->assertEquals('Content of file 1', $fileContent1);
        $this->assertEquals('Content of file 2', $fileContent2);

        $zip->close();

        // クリーンアップ
        unlink($zipFile);
        $folder = new BcFolder($tempDir);
        $folder->delete();
    }

    /**
     * test _extractByCommand
     */
    public function test_extractByCommand()
    {
        //準備
        (new BcFolder(TMP_TESTS . 'test'))->create();
        $path1 = TMP_TESTS . 'test' . DS . 'test1.txt';
        $path2 = TMP_TESTS . 'test' . DS . 'test2.txt';
        (new BcFile($path1))->create();
        (new BcFile($path2))->create();
        //テストzipファイルを生成
        $this->BcZip->create(TMP_TESTS . 'test', TMP_TESTS . 'test_extract.zip');
        //テストzipファイルが生成できるか確認
        $this->assertFileExists(TMP_TESTS, 'test_extract.zip');

        //ZIP を展開する
        $rs = $this->execPrivateMethod($this->BcZip, '_extractByCommand', [TMP_TESTS . 'test_extract.zip', TMP_TESTS]);
        $this->assertTrue($rs);
        //ZIP が展開できるか確認すること
        $this->assertFileExists(TMP_TESTS . 'test1.txt');
        $this->assertFileExists(TMP_TESTS . 'test2.txt');

        //不要なファイルを削除
        unlink(TMP_TESTS . 'test1.txt');
        unlink(TMP_TESTS . 'test2.txt');
        unlink(TMP_TESTS . 'test_extract.zip');
        (new BcFolder(TMP_TESTS . 'test'))->delete();

        //存在しないファイルを展開する場合、
        $rs = $this->execPrivateMethod($this->BcZip, '_extractByCommand', [TMP_TESTS . 'test_extract.zip', TMP_TESTS]);
        //戻り値を確認
        $this->assertFalse($rs);
    }

    /**
     * test ExtractByCommandRejectsZipSlip
     */
    public function testExtractByCommandRejectsZipSlip()
    {
        $zipSrcPath = TMP . 'zip' . DS;
        $sourceZip = $zipSrcPath . 'zipslip_cmd.zip';
        $targetPath = TMP . 'extracted' . DS;

        if (!file_exists($zipSrcPath)) {
            mkdir($zipSrcPath, 0777, true);
        }
        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0777, true);
        }

        $zip = new \ZipArchive();
        $zip->open($sourceZip, \ZipArchive::CREATE);
        $zip->addFromString('../evil_cmd.txt', 'nope');
        $zip->close();

        $result = $this->execPrivateMethod($this->BcZip, '_extractByCommand', [$sourceZip, $targetPath]);

        $this->assertFalse($result);
        $this->assertFileDoesNotExist($targetPath . 'evil_cmd.txt');
        $this->assertFileDoesNotExist(TMP . 'evil_cmd.txt');

        $folder = new BcFolder($zipSrcPath);
        $folder->delete();
        $folder = new BcFolder($targetPath);
        $folder->delete();
    }

    /**
     * test _validateZipEntries
     */
    public function testValidateZipEntries()
    {
        $zipFile = TMP . 'validate.zip';

        // 1. 正常なエントリ
        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('file1.txt', 'content');
        $zip->addFromString('dir/file2.txt', 'content');
        $zip->close();

        $targetPath = rtrim(TMP, '/\\');

        $this->BcZip->Zip->open($zipFile);
        $this->assertTrue($this->execPrivateMethod($this->BcZip, '_validateZipEntries', [$targetPath]));
        $this->BcZip->Zip->close();

        // 2. 不正なエントリ (Zip Slip)
        $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('../evil.txt', 'content');
        $zip->close();

        $this->BcZip->Zip->open($zipFile);
        $this->assertFalse($this->execPrivateMethod($this->BcZip, '_validateZipEntries', [$targetPath]));
        $this->BcZip->Zip->close();

        // 3. 空のZIP
        // Note: ZipArchive は空の場合ファイルを作成しない可能性がある
        $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->close();

        if (file_exists($zipFile)) {
            if ($this->BcZip->Zip->open($zipFile) === true) {
                $this->assertTrue($this->execPrivateMethod($this->BcZip, '_validateZipEntries', [$targetPath]));
                $this->BcZip->Zip->close();
            }
        }

        // クリーンアップ
        if (file_exists($zipFile)) {
            unlink($zipFile);
        }
    }

    /**
     * test _normalizeRelativePath
     */
    public function testNormalizeRelativePath()
    {
        $this->assertEquals('folder', $this->execPrivateMethod($this->BcZip, '_normalizeRelativePath', ['folder/']));
        $this->assertEquals('folder/file.txt', $this->execPrivateMethod($this->BcZip, '_normalizeRelativePath', ['folder/file.txt']));
        $this->assertEquals('', $this->execPrivateMethod($this->BcZip, '_normalizeRelativePath', ['/']));
        $this->assertEquals('', $this->execPrivateMethod($this->BcZip, '_normalizeRelativePath', ['./']));
        $this->assertEquals('a', $this->execPrivateMethod($this->BcZip, '_normalizeRelativePath', ['a/b/../']));
        $this->assertNull($this->execPrivateMethod($this->BcZip, '_normalizeRelativePath', ['..']));
        $this->assertNull($this->execPrivateMethod($this->BcZip, '_normalizeRelativePath', ['../']));
    }

    /**
     * test _normalizeAbsolutePath
     */
    public function testNormalizeAbsolutePath()
    {
        $this->assertEquals('/var/www', $this->execPrivateMethod($this->BcZip, '_normalizeAbsolutePath', ['/var/www']));
        $this->assertEquals('/var/www', $this->execPrivateMethod($this->BcZip, '_normalizeAbsolutePath', ['/var/www/']));
        $this->assertEquals('/var', $this->execPrivateMethod($this->BcZip, '_normalizeAbsolutePath', ['/var/www/..']));
        $this->assertNull($this->execPrivateMethod($this->BcZip, '_normalizeAbsolutePath', ['/..']));
        $this->assertNull($this->execPrivateMethod($this->BcZip, '_normalizeAbsolutePath', ['/../var']));

        // Windowsパスのシミュレーション
        $this->assertEquals('C:/Windows', $this->execPrivateMethod($this->BcZip, '_normalizeAbsolutePath', ['C:/Windows']));
        $this->assertEquals('C:/', $this->execPrivateMethod($this->BcZip, '_normalizeAbsolutePath', ['C:/Windows/..']));
        $this->assertNull($this->execPrivateMethod($this->BcZip, '_normalizeAbsolutePath', ['C:/..']));
    }
}
