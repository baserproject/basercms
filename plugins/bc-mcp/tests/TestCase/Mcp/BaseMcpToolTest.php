<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Mcp;

use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Factory\UserGroupFactory;
use BaserCore\Test\Factory\UsersUserGroupFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMcp\Mcp\BaseMcpTool;
use BcMcp\Mcp\McpContext;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BaseMcpToolTest
 */
class BaseMcpToolTest extends BcTestCase
{

    use ScenarioAwareTrait;
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
     * テスト内で mcp_uploads ディレクトリに作成したファイルのパス
     *
     * 設定した場合のみ tearDown() で確実に削除する
     */
    protected ?string $chunkUploadFile = null;

    /**
     * テスト内で作成した mcp_uploads ディレクトリのパス
     */
    protected ?string $chunkUploadDir = null;

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->BaseMcpTool);
        if ($this->chunkUploadFile && file_exists($this->chunkUploadFile)) {
            unlink($this->chunkUploadFile);
        }
        if ($this->chunkUploadDir && is_dir($this->chunkUploadDir) && count(scandir($this->chunkUploadDir)) === 2) {
            rmdir($this->chunkUploadDir);
        }
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
     * test processFileUpload rejects a filename pointing at an existing chunk upload file
     *
     * チャンクアップロードを廃止したため、TMP/mcp_uploads/ に実在するファイルを
     * 指しても、それをアップロードとして扱ってはならない。
     * processChunkFile() が残っている間は配列（アップロード情報）が返り、
     * このテストは FAIL する。processChunkFile() 削除後は false が返り PASS する。
     */
    public function testProcessFileUploadRejectsExistingChunkFile()
    {
        $this->chunkUploadDir = TMP . 'mcp_uploads' . DS;
        if (!is_dir($this->chunkUploadDir)) {
            mkdir($this->chunkUploadDir, 0755, true);
        }
        $filename = 'test_chunk_upload.jpg';
        $this->chunkUploadFile = $this->chunkUploadDir . $filename;
        file_put_contents($this->chunkUploadFile, 'dummy image data');

        $result = $this->execPrivateMethod($this->BaseMcpTool, 'processFileUpload', [$filename]);

        $this->assertFalse($result);
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
     * test isPublicIp - 内部ネットワーク等の非公開アドレスを拒否すること
     * @dataProvider isPublicIpDataProvider
     */
    public function testIsPublicIp(string $ip, bool $expected)
    {
        $this->assertSame($expected, $this->execPrivateMethod($this->BaseMcpTool, 'isPublicIp', [$ip]));
    }

    public static function isPublicIpDataProvider(): array
    {
        return [
            ['93.184.216.34', true],
            ['8.8.8.8', true],
            ['2606:4700::1111', true],
            ['127.0.0.1', false],
            ['127.1.2.3', false],
            ['10.0.0.5', false],
            ['172.16.0.1', false],
            ['192.168.1.1', false],
            ['169.254.169.254', false],
            ['100.64.0.1', false],
            ['0.0.0.0', false],
            ['224.0.0.1', false],
            ['255.255.255.255', false],
            ['::1', false],
            ['fe80::1', false],
            ['fc00::1', false],
            ['fd12::1', false],
            ['ff02::1', false],
            ['::ffff:127.0.0.1', false],
            ['::ffff:10.0.0.1', false],
            ['64:ff9b::7f00:1', false],
        ];
    }

    /**
     * test resolvePublicIp - localhost や IP リテラルの内部アドレスは解決しないこと
     */
    public function testResolvePublicIp()
    {
        $this->assertNull($this->execPrivateMethod($this->BaseMcpTool, 'resolvePublicIp', ['localhost']));
        $this->assertNull($this->execPrivateMethod($this->BaseMcpTool, 'resolvePublicIp', ['foo.localhost']));
        $this->assertNull($this->execPrivateMethod($this->BaseMcpTool, 'resolvePublicIp', ['127.0.0.1']));
        $this->assertNull($this->execPrivateMethod($this->BaseMcpTool, 'resolvePublicIp', ['[::1]']));
        $this->assertNull($this->execPrivateMethod($this->BaseMcpTool, 'resolvePublicIp', ['169.254.169.254']));
        $this->assertSame('93.184.216.34', $this->execPrivateMethod($this->BaseMcpTool, 'resolvePublicIp', ['93.184.216.34']));
    }

    /**
     * test processUrlFile - 内部ネットワークの URL を拒否すること（SSRF 対策）
     * @dataProvider ssrfUrlDataProvider
     */
    public function testProcessUrlFileRejectsInternalUrl(string $url)
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('URLからファイルをダウンロードできませんでした');
        $this->execPrivateMethod($this->BaseMcpTool, 'processUrlFile', [$url]);
    }

    public static function ssrfUrlDataProvider(): array
    {
        return [
            ['http://127.0.0.1/image.png'],
            ['http://localhost:8080/image.png'],
            ['http://169.254.169.254/latest/meta-data/'],
            ['http://10.0.0.1/image.png'],
            ['http://[::1]/image.png'],
            ['http://user:pass@93.184.216.34/image.png'],
        ];
    }

    /**
     * test processUrlFile - ローカルのテストサーバーで取得・リダイレクト・エラー応答を確認する
     */
    public function testProcessUrlFileWithLocalServer()
    {
        $docRoot = TMP . 'mcp_url_test' . DS;
        if (!is_dir($docRoot)) mkdir($docRoot, 0777, true);
        file_put_contents($docRoot . 'image.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAI9jAuoqQAAAABJRU5ErkJggg=='));
        file_put_contents($docRoot . 'redirect.php', '<?php header("Location: /image.png", true, 302);');
        file_put_contents($docRoot . 'redirect_internal.php', '<?php header("Location: http://169.254.169.254/latest/meta-data/", true, 302);');
        file_put_contents($docRoot . 'loop.php', '<?php header("Location: /loop.php", true, 302);');
        file_put_contents($docRoot . 'error.php', '<?php http_response_code(500); echo "error";');
        $port = 18089;
        $process = proc_open(
            ['php', '-S', '127.0.0.1:' . $port, '-t', $docRoot],
            [0 => ['pipe', 'r'], 1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']],
            $pipes
        );
        $this->assertIsResource($process);
        // サーバーの起動を待つ
        for ($i = 0; $i < 50; $i++) {
            $socket = @fsockopen('127.0.0.1', $port, $errno, $errstr, 0.2);
            if ($socket) { fclose($socket); break; }
            usleep(100000);
        }
        TestBaseMcpTool::$allowLoopbackForTest = true;
        try {
            $base = 'http://127.0.0.1:' . $port;
            // 直接取得
            $result = $this->execPrivateMethod($this->BaseMcpTool, 'processUrlFile', [$base . '/image.png']);
            $this->assertEquals('png', $result['ext']);
            $this->assertEquals('image/png', $result['type']);
            $this->assertFileExists($result['tmp_name']);
            unlink($result['tmp_name']);
            // リダイレクト先を検証したうえで追従する
            $result = $this->execPrivateMethod($this->BaseMcpTool, 'processUrlFile', [$base . '/redirect.php']);
            $this->assertEquals('png', $result['ext']);
            unlink($result['tmp_name']);
            // 内部アドレスへのリダイレクトは拒否する
            try {
                $this->execPrivateMethod($this->BaseMcpTool, 'processUrlFile', [$base . '/redirect_internal.php']);
                $this->fail('内部アドレスへのリダイレクトが拒否されていません');
            } catch (\Exception $e) {
                $this->assertEquals('URLからファイルをダウンロードできませんでした', $e->getMessage());
            }
            // リダイレクト回数の上限
            try {
                $this->execPrivateMethod($this->BaseMcpTool, 'processUrlFile', [$base . '/loop.php']);
                $this->fail('リダイレクトの上限が効いていません');
            } catch (\Exception $e) {
                $this->assertEquals('URLからファイルをダウンロードできませんでした', $e->getMessage());
            }
            // エラー応答（404 / 500）は失敗として扱う
            foreach(['/notfound.png', '/error.php'] as $path) {
                try {
                    $this->execPrivateMethod($this->BaseMcpTool, 'processUrlFile', [$base . $path]);
                    $this->fail('エラー応答が失敗として扱われていません: ' . $path);
                } catch (\Exception $e) {
                    $this->assertEquals('URLからファイルをダウンロードできませんでした', $e->getMessage());
                }
            }
        } finally {
            TestBaseMcpTool::$allowLoopbackForTest = false;
            proc_terminate($process);
            proc_close($process);
            foreach(glob($docRoot . '*') as $file) unlink($file);
            rmdir($docRoot);
        }
    }

    /**
     * test resolveLoginUserId - コンテキストが設定されている場合は引数より優先されること
     */
    public function testResolveLoginUserIdPrefersContext()
    {
        McpContext::clear();
        $this->assertSame(7, $this->execPrivateMethod($this->BaseMcpTool, 'resolveLoginUserId', [7]));
        McpContext::setLoginUserId(2);
        try {
            // 引数（クライアントが送信できる値）で操作者を詐称できない
            $this->assertSame(2, $this->execPrivateMethod($this->BaseMcpTool, 'resolveLoginUserId', [1]));
            $this->assertSame(2, $this->execPrivateMethod($this->BaseMcpTool, 'resolveLoginUserId', [null]));
        } finally {
            McpContext::clear();
        }
    }

    /**
     * test resolveActorId - 操作者以外を作成者に指定できるのはシステム管理者のみであること
     */
    public function testResolveActorId()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        UserGroupFactory::make(['id' => 2, 'name' => 'operators', 'title' => 'operators', 'auth_prefix' => 'Admin,Api/Admin'])->persist();
        UserFactory::make(['id' => 2, 'name' => 'operator', 'email' => 'operator@example.com', 'status' => true])->persist();
        UsersUserGroupFactory::make(['user_id' => 2, 'user_group_id' => 2])->persist();

        // 未指定・自分自身は操作者
        $this->assertSame(2, $this->execPrivateMethod($this->BaseMcpTool, 'resolveActorId', [null, 2]));
        $this->assertSame(2, $this->execPrivateMethod($this->BaseMcpTool, 'resolveActorId', [2, 2]));
        // システム管理者は他のユーザーを指定できる
        $this->assertSame(2, $this->execPrivateMethod($this->BaseMcpTool, 'resolveActorId', [2, 1]));
        // コンテキストが無い（操作者不明）場合は指定値
        $this->assertSame(1, $this->execPrivateMethod($this->BaseMcpTool, 'resolveActorId', [1, null]));
        // 一般ユーザーは他のユーザーを指定できない
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('システム管理者のみ');
        $this->execPrivateMethod($this->BaseMcpTool, 'resolveActorId', [1, 2]);
    }

    /**
     * test clampLimit
     */
    public function testClampLimit()
    {
        $this->assertSame(20, $this->execPrivateMethod($this->BaseMcpTool, 'clampLimit', [null, 20]));
        $this->assertSame(20, $this->execPrivateMethod($this->BaseMcpTool, 'clampLimit', [0, 20]));
        $this->assertSame(5, $this->execPrivateMethod($this->BaseMcpTool, 'clampLimit', [5, 20]));
        $this->assertSame(100, $this->execPrivateMethod($this->BaseMcpTool, 'clampLimit', [100000, 20]));
        $this->assertSame(50, $this->execPrivateMethod($this->BaseMcpTool, 'clampLimit', [100000, 20, 50]));
    }

    /**
     * test processBase64File - 許可されていない形式（HTML / SVG 等）を拒否すること
     */
    public function testProcessBase64FileRejectsDisallowedType()
    {
        $html = 'data:text/html;base64,' . base64_encode('<script>alert(1)</script>');
        try {
            $this->execPrivateMethod($this->BaseMcpTool, 'processBase64File', [$html]);
            $this->fail('HTML が拒否されていません');
        } catch (\Exception $e) {
            $this->assertStringContainsString('サポートされていないファイル形式です', $e->getMessage());
        }
        // 申告 MIME が画像でも中身が HTML なら拒否する
        $fake = 'data:image/png;base64,' . base64_encode('<html><script>alert(1)</script></html>');
        try {
            $this->execPrivateMethod($this->BaseMcpTool, 'processBase64File', [$fake]);
            $this->fail('偽装した MIME タイプが拒否されていません');
        } catch (\Exception $e) {
            $this->assertStringContainsString('サポートされていないファイル形式です', $e->getMessage());
        }
        $svg = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
        try {
            $this->execPrivateMethod($this->BaseMcpTool, 'processBase64File', [$svg]);
            $this->fail('SVG が拒否されていません');
        } catch (\Exception $e) {
            $this->assertStringContainsString('サポートされていないファイル形式です', $e->getMessage());
        }
        $this->assertFalse($this->execPrivateMethod($this->BaseMcpTool, 'isAllowedExtension', ['svg']));
        $this->assertFalse($this->execPrivateMethod($this->BaseMcpTool, 'isAllowedExtension', ['html']));
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

        // 拡張子付きの素のファイル名（チャンクアップロードは廃止したため、対応しない）
        //
        // processFileUpload() は data: URI と http(s) URL しか受け付けないため、
        // isFileUploadable() がここで true を返すと、拡張子付きの通常の文字列値
        // （例: サンプルテキストのファイル名相当の値）がファイルと誤判定され、
        // その後 processFileUpload() が false を返して静かに失敗する
        $bareFilename = 'photo.jpg';
        $this->assertFalse($this->execPrivateMethod($this->BaseMcpTool, 'isFileUploadable', [$bareFilename]));
    }

    /**
     * test executeWithErrorHandling
     *
     * \Exception だけではなく \Error も捕捉し、トレースを返す事を確認する
     * （MCPサーバー側で丸められると発生箇所を追跡できなくなるため）
     */
    public function testExecuteWithErrorHandling()
    {
        // \Exception を捕捉できる事を確認
        $result = $this->execPrivateMethod($this->BaseMcpTool, 'executeWithErrorHandling', [
            function() {
                throw new \Exception('例外が発生しました');
            }
        ]);
        $this->assertEquals('例外が発生しました', $result['content']);
        // スタックトレースはクライアントへ返さない（ログにのみ記録する）
        $this->assertArrayNotHasKey('trace', $result);

        // \Error を捕捉できる事を確認
        $result = $this->execPrivateMethod($this->BaseMcpTool, 'executeWithErrorHandling', [
            function() {
                $request = null;
                return $request->getParam('prefix');
            }
        ]);
        // PHP 内部のエラーは内部情報を含むため定型文に丸め、トレースも返さない
        $this->assertStringNotContainsString('getParam() on null', $result['content']);
        $this->assertStringContainsString('ツールの実行中にエラーが発生しました', $result['content']);
        $this->assertArrayNotHasKey('trace', $result);
    }
}

/**
 * テスト用のBaseMcpToolクラス
 */
class TestBaseMcpTool extends BaseMcpTool
{

    /**
     * ツールは登録しない（共通処理のテストが目的のため）
     *
     * @param \Mcp\Server\McpServer $server SDK のサーバー
     * @return \Mcp\Server\McpServer
     */
    public function registerTools(\Mcp\Server\McpServer $server): \Mcp\Server\McpServer
    {
        return $server;
    }

    /**
     * テスト用: ローカルのテストサーバー（127.0.0.1）への接続のみ許可する
     */
    public static bool $allowLoopbackForTest = false;

    protected function isPublicIp(string $ip): bool
    {
        if (self::$allowLoopbackForTest && $ip === '127.0.0.1') return true;
        return parent::isPublicIp($ip);
    }

}
