<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Mcp;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Mcp\BaseMcpTool;

/**
 * BaseMcpToolTest
 */
class BaseMcpToolTest extends BcTestCase
{
    /**
     * Test subject
     *
     * @var TestBaseMcpTool
     */
    protected $BaseMcpTool;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BaseMcpTool = new TestBaseMcpTool();
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->BaseMcpTool);
        parent::tearDown();
    }

    /**
     * test processFileUpload with base64 data
     */
    public function testProcessFileUploadWithBase64()
    {
        // 小さなPNG画像のbase64データ（1x1ピクセルの透明PNG）
        $base64Data = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAI9jAuoqQAAAABJRU5ErkJggg==';

        $result = $this->execPrivateMethod($this->BaseMcpTool, 'processFileUpload', [$base64Data]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertEquals('image/png', $result['type']);
        $this->assertEquals('png', $result['ext']);

        // クリーンアップ
        if (file_exists($result['tmp_name'])) {
            unlink($result['tmp_name']);
        }
    }

    /**
     * test processFileUpload with URL
     */
    public function testProcessFileUploadWithUrl()
    {
        $url = 'https://basercms.net/img/basercms_logo.png';
        $result = $this->execPrivateMethod($this->BaseMcpTool, 'processFileUpload', [$url]);

        // URLの場合はそのまま返される
        $this->assertArrayHasKey('tmp_name', $result);
    }

    /**
     * test getMimeTypeFromExtension
     */
    public function testGetMimeTypeFromExtension()
    {
        $testCases = [
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'mp3' => 'audio/mpeg',
            'unknown' => 'application/octet-stream'
        ];

        foreach($testCases as $extension => $expectedMimeType) {
            $result = $this->execPrivateMethod($this->BaseMcpTool, 'getMimeTypeFromExtension', [$extension]);
            $this->assertEquals($expectedMimeType, $result, "Extension: {$extension}");
        }
    }

    /**
     * test getExtensionFromMimeType
     */
    public function testGetExtensionFromMimeType()
    {
        $testCases = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
            'text/plain' => 'txt',
            'application/unknown' => 'bin'
        ];

        foreach($testCases as $mimeType => $expectedExtension) {
            $result = $this->execPrivateMethod($this->BaseMcpTool, 'getExtensionFromMimeType', [$mimeType]);
            $this->assertEquals($expectedExtension, $result, "MIME Type: {$mimeType}");
        }
    }

    /**
     * test isAllowedExtension
     */
    public function testIsAllowedExtension()
    {
        $allowedExtensions = ['jpg', 'png', 'pdf', 'docx'];
        $disallowedExtensions = ['exe', 'bat', 'sh'];

        foreach($allowedExtensions as $extension) {
            $result = $this->execPrivateMethod($this->BaseMcpTool, 'isAllowedExtension', [$extension]);
            $this->assertTrue($result, "Extension should be allowed: {$extension}");
        }

        foreach($disallowedExtensions as $extension) {
            $result = $this->execPrivateMethod($this->BaseMcpTool, 'isAllowedExtension', [$extension]);
            $this->assertFalse($result, "Extension should not be allowed: {$extension}");
        }
    }

    /**
     * test processImageUpload
     */
    public function testProcessImageUpload()
    {
        // 画像のbase64データ
        $imageBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAI9jAuoqQAAAABJRU5ErkJggg==';

        $result = $this->execPrivateMethod($this->BaseMcpTool, 'processImageUpload', [$imageBase64]);

        $this->assertIsArray($result);
        $this->assertEquals('image/png', $result['type']);

        // クリーンアップ
        if (file_exists($result['tmp_name'])) {
            unlink($result['tmp_name']);
        }
    }

    /**
     * test processImageUpload with non-image file should throw exception
     */
    public function testProcessImageUploadWithNonImageFile()
    {
        // PDFのbase64データ（非画像ファイル）
        $pdfBase64 = 'data:application/pdf;base64,JVBERi0xLjQK';

        try {
            $this->execPrivateMethod($this->BaseMcpTool, 'processImageUpload', [$pdfBase64]);
            $this->fail('例外が投げられるべきです');
        } catch (\Exception $e) {
            $this->assertStringContainsString('画像ファイルではありません', $e->getMessage());
        }
    }

    /**
     * test isFileUploadable method
     */
    public function testIsFileUploadable()
    {
        // Base64データ
        $base64Data = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAI9jAuoqQAAAABJRU5ErkJggg==';
        $this->assertTrue($this->execPrivateMethod($this->BaseMcpTool, 'isFileUploadable', [$base64Data]));

        // URL
        $url = 'https://example.com/image.jpg';
        $this->assertTrue($this->execPrivateMethod($this->BaseMcpTool, 'isFileUploadable', [$url]));

        // 通常の文字列
        $text = 'ただのテキスト';
        $this->assertFalse($this->execPrivateMethod($this->BaseMcpTool, 'isFileUploadable', [$text]));

        // 配列
        $array = ['test' => 'value'];
        $this->assertTrue($this->execPrivateMethod($this->BaseMcpTool, 'isFileUploadable', [$array]));
    }
}

/**
 * テスト用のBaseMcpToolクラス
 */
class TestBaseMcpTool extends BaseMcpTool
{
    // テスト用のため空実装
}
