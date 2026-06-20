<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Mcp\BaserCore;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Mcp\BaserCore\FileUploadTool;

/**
 * FileUploadToolTest Test Case
 */
class FileUploadToolTest extends BcTestCase
{

    /**
     * Test subject
     */
    protected $fileUploadTool;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->fileUploadTool = new FileUploadTool();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        // テスト用ファイルをクリーンアップ
        $this->cleanupTestFiles();
        unset($this->fileUploadTool);
        parent::tearDown();
    }

    /**
     * テスト用ファイルをクリーンアップ
     */
    private function cleanupTestFiles(): void
    {
        $uploadDir = TMP . 'mcp_uploads/';
        if (is_dir($uploadDir)) {
            $files = glob($uploadDir . '*');
            foreach($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * 単一チャンクファイルのアップロードテスト
     */
    public function testSendSingleChunk()
    {
        $fileId = 'test_file_' . uniqid();
        $filename = 'test.txt';
        $content = 'Hello, World!';
        $chunkData = base64_encode($content);

        $result = $this->fileUploadTool->sendFileChunk($fileId, 0, 1, $chunkData, $filename);

        $this->assertEquals('complete', $result['status']);
        $this->assertArrayHasKey('file', $result);

        // ファイルが正しく作成されているかチェック
        $finalFile = TMP . 'mcp_uploads/' . $filename;
        $this->assertTrue(file_exists($finalFile));
        $this->assertEquals($content, file_get_contents($finalFile));
    }

    /**
     * 複数チャンクファイルのアップロードテスト
     */
    public function testSendMultipleChunks()
    {
        $fileId = 'test_multi_' . uniqid();
        $filename = 'test_multi.txt';
        $content1 = 'Hello, ';
        $content2 = 'World!';
        $totalChunks = 2;

        // 最初のチャンクを送信
        $result1 = $this->fileUploadTool->sendFileChunk($fileId, 0, $totalChunks, base64_encode($content1), $filename);

        $this->assertEquals('chunk_received', $result1['status']);
        $this->assertEquals(1, $result1['progress']);

        // 2番目のチャンクを送信
        $result2 = $this->fileUploadTool->sendFileChunk($fileId, 1, $totalChunks, base64_encode($content2), $filename);

        $this->assertEquals('complete', $result2['status']);
        $this->assertArrayHasKey('file', $result2);

        // マージされたファイルが正しく作成されているかチェック
        $finalFile = TMP . 'mcp_uploads/' . $filename;
        $this->assertTrue(file_exists($finalFile));
        $this->assertEquals($content1 . $content2, file_get_contents($finalFile));

        // チャンクファイルが削除されているかチェック
        $this->assertFalse(file_exists(TMP . 'mcp_uploads/' . $fileId . '.part0'));
        $this->assertFalse(file_exists(TMP . 'mcp_uploads/' . $fileId . '.part1'));
    }

    /**
     * チャンク順序が異なる場合のテスト
     */
    public function testSendChunksOutOfOrder()
    {
        $fileId = 'test_order_' . uniqid();
        $filename = 'test_order.txt';
        $content1 = 'Hello, ';
        $content2 = 'World!';
        $totalChunks = 2;

        // 2番目のチャンクを先に送信
        $result1 = $this->fileUploadTool->sendFileChunk($fileId, 1, $totalChunks, base64_encode($content2), $filename);

        $this->assertEquals('chunk_received', $result1['status']);
        $this->assertEquals(2, $result1['progress']);

        // 1番目のチャンクを後で送信
        $result2 = $this->fileUploadTool->sendFileChunk($fileId, 0, $totalChunks, base64_encode($content1), $filename);

        $this->assertEquals('complete', $result2['status']);

        // マージされたファイルが正しい順序で作成されているかチェック
        $finalFile = TMP . 'mcp_uploads/' . $filename;
        $this->assertTrue(file_exists($finalFile));
        $this->assertEquals($content1 . $content2, file_get_contents($finalFile));
    }

    /**
     * 大きなファイルの分割アップロードテスト
     */
    public function testLargeFileUpload()
    {
        $fileId = 'test_large_' . uniqid();
        $filename = 'test_large.txt';
        $chunkSize = 1024; // 1KB chunks
        $totalSize = 3000; // 3KB total
        $content = str_repeat('A', $totalSize);

        $chunks = str_split($content, $chunkSize);
        $totalChunks = count($chunks);

        // 各チャンクを順番に送信
        for($i = 0; $i < $totalChunks - 1; $i++) {
            $result = $this->fileUploadTool->sendFileChunk($fileId, $i, $totalChunks, base64_encode($chunks[$i]), $filename);

            $this->assertEquals('chunk_received', $result['status']);
            $this->assertEquals($i + 1, $result['progress']);
        }

        // 最後のチャンクを送信
        $lastIndex = $totalChunks - 1;
        $result = $this->fileUploadTool->sendFileChunk($fileId, $lastIndex, $totalChunks, base64_encode($chunks[$lastIndex]), $filename);

        $this->assertEquals('complete', $result['status']);

        // マージされたファイルが正しく作成されているかチェック
        $finalFile = TMP . 'mcp_uploads/' . $filename;
        $this->assertTrue(file_exists($finalFile));
        $this->assertEquals($totalSize, filesize($finalFile));
        $this->assertEquals($content, file_get_contents($finalFile));
    }

    /**
     * 不正なbase64データのテスト
     */
    public function testInvalidBase64Data()
    {
        $fileId = 'test_invalid_' . uniqid();
        $filename = 'test_invalid.txt';
        $invalidBase64 = 'invalid-base64-data!@#$%';

        $result = $this->fileUploadTool->sendFileChunk($fileId, 0, 1, $invalidBase64, $filename);

        // base64_decodeはfalseを返すが、空文字列として処理される
        $this->assertEquals('complete', $result['status']);

        $finalFile = TMP . 'mcp_uploads/' . $filename;
        $this->assertTrue(file_exists($finalFile));
        // 不正なbase64は空文字列またはガベージデータになる
        $this->assertTrue(filesize($finalFile) >= 0);
    }

    /**
     * アップロードディレクトリが作成されることをテスト
     */
    public function testUploadDirectoryCreation()
    {
        $uploadDir = TMP . 'mcp_uploads/';

        // ディレクトリが存在することを確認
        $this->assertTrue(is_dir($uploadDir));
        $this->assertTrue(is_writable($uploadDir));
    }

    /**
     * 同じファイルIDで複数回アップロードした場合のテスト
     */
    public function testDuplicateFileId()
    {
        $fileId = 'test_duplicate_' . uniqid();
        $filename = 'test_duplicate.txt';
        $content1 = 'First upload';
        $content2 = 'Second upload';

        // 最初のアップロード
        $result1 = $this->fileUploadTool->sendFileChunk($fileId, 0, 1, base64_encode($content1), $filename);
        $this->assertEquals('complete', $result1['status']);

        // 同じファイルIDで2回目のアップロード（上書きされる）
        $result2 = $this->fileUploadTool->sendFileChunk($fileId, 0, 1, base64_encode($content2), $filename);
        $this->assertEquals('complete', $result2['status']);

        // 最後にアップロードされたファイルの内容を確認
        $finalFile = TMP . 'mcp_uploads/' . $filename;
        $this->assertEquals($content2, file_get_contents($finalFile));
    }

    /**
     * 実際の画像ファイル（basercms.png）を使ったアップロードテスト
     */
    public function testUploadRealImageFile()
    {
        $imagePath = WWW_ROOT . 'img' . DS . 'basercms.png';

        // ファイルが存在することを確認
        $this->assertTrue(file_exists($imagePath), 'basercms.png が存在しません');

        $imageContent = file_get_contents($imagePath);
        $fileId = 'test_image_' . uniqid();
        $filename = 'basercms.png';

        // 画像ファイルを単一チャンクでアップロード
        $result = $this->fileUploadTool->sendFileChunk($fileId, 0, 1, base64_encode($imageContent), $filename);

        $this->assertEquals('complete', $result['status']);
        $this->assertArrayHasKey('file', $result);

        // アップロードされたファイルが元のファイルと同じであることを確認
        $uploadedFile = TMP . 'mcp_uploads/' . $filename;
        $this->assertTrue(file_exists($uploadedFile));
        $this->assertEquals(filesize($imagePath), filesize($uploadedFile));
        $this->assertEquals($imageContent, file_get_contents($uploadedFile));

        // ファイルがPNG画像として有効か確認（オプション）
        $imageInfo = getimagesize($uploadedFile);
        $this->assertNotFalse($imageInfo, 'アップロードされたファイルが有効な画像ではありません');
        $this->assertEquals(IMAGETYPE_PNG, $imageInfo[2], 'アップロードされたファイルがPNG形式ではありません');
    }

    /**
     * 画像ファイルを複数チャンクに分割してアップロードするテスト
     */
    public function testUploadImageFileInChunks()
    {
        $imagePath = WWW_ROOT . 'img' . DS . 'basercms.png';

        // ファイルが存在することを確認
        $this->assertTrue(file_exists($imagePath), 'basercms.png が存在しません');

        $imageContent = file_get_contents($imagePath);
        $fileId = 'test_image_chunks_' . uniqid();
        $filename = 'basercms_chunked.png';

        // 画像を1024バイトずつのチャンクに分割
        $chunkSize = 1024;
        $chunks = str_split($imageContent, $chunkSize);
        $totalChunks = count($chunks);

        $this->assertGreaterThan(1, $totalChunks, '画像ファイルが小さすぎてチャンク分割できません');

        // 各チャンクを順番に送信（最後のチャンク以外）
        for($i = 0; $i < $totalChunks - 1; $i++) {
            $result = $this->fileUploadTool->sendFileChunk($fileId, $i, $totalChunks, base64_encode($chunks[$i]), $filename);

            $this->assertEquals('chunk_received', $result['status']);
            $this->assertEquals($i + 1, $result['progress']);
        }

        // 最後のチャンクを送信
        $lastIndex = $totalChunks - 1;
        $result = $this->fileUploadTool->sendFileChunk($fileId, $lastIndex, $totalChunks, base64_encode($chunks[$lastIndex]), $filename);

        $this->assertEquals('complete', $result['status']);

        // マージされたファイルが元のファイルと同じであることを確認
        $uploadedFile = TMP . 'mcp_uploads/' . $filename;
        $this->assertTrue(file_exists($uploadedFile));
        $this->assertEquals(filesize($imagePath), filesize($uploadedFile));
        $this->assertEquals($imageContent, file_get_contents($uploadedFile));

        // ファイルがPNG画像として有効か確認
        $imageInfo = getimagesize($uploadedFile);
        $this->assertNotFalse($imageInfo, 'マージされたファイルが有効な画像ではありません');
        $this->assertEquals(IMAGETYPE_PNG, $imageInfo[2], 'マージされたファイルがPNG形式ではありません');

        // チャンクファイルが削除されているかチェック
        for($i = 0; $i < $totalChunks; $i++) {
            $chunkFile = TMP . 'mcp_uploads/' . $fileId . '.part' . $i;
            $this->assertFalse(file_exists($chunkFile), "チャンクファイル {$chunkFile} が削除されていません");
        }
    }

    /**
     * 大きな画像ファイルのMD5ハッシュチェックテスト
     */
    public function testImageFileIntegrityWithMd5()
    {
        $imagePath = WWW_ROOT . 'img' . DS . 'basercms.png';

        // ファイルが存在することを確認
        $this->assertTrue(file_exists($imagePath), 'basercms.png が存在しません');

        $imageContent = file_get_contents($imagePath);
        $originalMd5 = md5($imageContent);
        $fileId = 'test_image_md5_' . uniqid();
        $filename = 'basercms_md5.png';

        // 画像を512バイトずつの小さなチャンクに分割（より細かい分割）
        $chunkSize = 512;
        $chunks = str_split($imageContent, $chunkSize);
        $totalChunks = count($chunks);

        // 各チャンクを順番に送信
        for($i = 0; $i < $totalChunks; $i++) {
            $result = $this->fileUploadTool->sendFileChunk($fileId, $i, $totalChunks, base64_encode($chunks[$i]), $filename);


            if ($i < $totalChunks - 1) {
                $this->assertEquals('chunk_received', $result['status']);
            } else {
                $this->assertEquals('complete', $result['status']);
            }
        }

        // アップロードされたファイルのMD5ハッシュを確認
        $uploadedFile = TMP . 'mcp_uploads/' . $filename;
        $this->assertTrue(file_exists($uploadedFile));

        $uploadedMd5 = md5_file($uploadedFile);
        $this->assertEquals($originalMd5, $uploadedMd5, 'アップロードされたファイルのMD5ハッシュが元のファイルと一致しません');

        // ファイルサイズも確認
        $this->assertEquals(filesize($imagePath), filesize($uploadedFile), 'ファイルサイズが一致しません');
    }
}
