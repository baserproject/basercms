# bc-mcp MCP 2026-07-28（Dual-era）対応 実装計画

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** bc-mcp の MCP SDK を `logiscape/mcp-sdk-php` v2 へ移植し、Modern（`2026-07-28`）と Legacy（`initialize` 方式）の両世代を同時に提供する Dual-era サーバーにする。あわせて常駐 MCP サーバープロセスを廃止し、CakePHP のリクエスト内で処理を完結させる。移植の完了後、固定ページ（Pages）ツールを新規追加する。

**Architecture:** `McpRequestHandler` が SDK の `HttpServerRunner` をプロセス内で実行する単一の入口になる。`McpProxyController` は認証・認可・`Origin` 検証・ロギングと、CakePHP のリクエスト／レスポンスと SDK の `HttpMessage` の相互変換に責務を絞る。プロトコルの世代判定・`server/discover`・必須ヘッダ検証・`resultType` / `ttlMs` / `cacheScope` の付与はすべて SDK が担う。

**Tech Stack:** PHP 8.1+ / CakePHP 5 / baserCMS 5.4 / `logiscape/mcp-sdk-php` v2.0.0 / `league/oauth2-server` 8.5.5 / PHPUnit 10.5

**設計書:** [2026-08-12-bc-mcp-sdk-migration-design.md](../specs/2026-08-12-bc-mcp-sdk-migration-design.md)
**前提調査:** [2026-08-12-mcp-2026-07-28-bc-mcp-impact.md](../specs/2026-08-12-mcp-2026-07-28-bc-mcp-impact.md)

## Global Constraints

- 作業ブランチは `dev-mcp-2026-07-28`（`dev-agentic` から分岐済み）。
- PHP は `>=8.1` で動作すること。ルート `composer.json` の `config.platform.php` は `8.1` に固定。
- テストはローカル Docker の **`basercms` コンテナ**で実行する。baserCMS の配置先は `/var/www/html`。
- テスト実行コマンドはパイプを含めて**単一引用符の中に収める**（`docker exec basercms sh -c '...'` の形）。
- コメント・コミットメッセージ・ドキュメントは**日本語**で書く。
- `vendor/` 配下はコアハック禁止。
- **`loginUserId` を `inputSchema` に公開してはならない。** AI クライアントに他ユーザーの ID を指定する余地を与えることになる。
- **リクエストボディを改変してはならない。** Modern ではヘッダとボディの一致が検証される。ログインユーザーは `McpContext` 経由で渡す。
- `nyholm/psr7` と `ext-openssl` は OAuth2 側で使用しているため削除しない。
- `league/oauth2-server` の 9系アップデート、Client ID Metadata Documents 対応、CuMcp への反映はスコープ外。

## 使用する SDK の API（実地確認済み）

| API | 用途 |
|---|---|
| `Mcp\Server\McpServer::__construct(string $name, ?LoggerInterface $logger = null, string $version = '1.0.0')` | サーバー生成。ロガーは第2引数 |
| `Mcp\Server\McpServer::tool(name:, description:, callback:, inputSchema:)` | ツール登録。`inputSchema` を明示指定できる |
| `Mcp\Server\McpServer::getServer(): Mcp\Server\Server` | コアサーバーの取得 |
| `Mcp\Server\Server::createInitializationOptions(?NotificationOptions = null): InitializationOptions` | 初期化オプション。引数は省略可 |
| `new Mcp\Server\HttpServerRunner(Server, InitializationOptions, array $httpOptions, ?LoggerInterface, ?SessionStoreInterface, ?HttpIoInterface)` | HTTP リクエストの実行器 |
| `HttpServerRunner::handleRequest(?HttpMessage $request = null): HttpMessage` | 1リクエストを処理してレスポンスを返す |
| `new Mcp\Server\Transport\Http\HttpMessage(?string $body)` + `setMethod()` / `setUri()` / `setHeader()` | リクエストの組み立て |
| `HttpMessage::getStatusCode()` / `getBody()` / `getHeaders()` | レスポンスの取り出し |
| `Mcp\Server\Transport\Http\BufferedIo` | 出力を SAPI へ書き出さずバッファに捕捉する `HttpIoInterface` 実装 |
| `Mcp\Server\Transport\Http\FileSessionStore` | Legacy 世代のセッション永続 |
| `httpOptions` の `allowed_origins` | Origin 検証（null で無効） |

---

### Task 1: SDK の導入（完了済み）

**実施日:** 2026-08-12

- [x] `plugins/bc-mcp/composer.json` の `php-mcp/server: ^3.3` を `logiscape/mcp-sdk-php: ^2.0` に差し替え
- [x] ルート `composer.json` を直接編集（`monorepo-builder merge` は**既存の**バージョン不一致で失敗するため。`nyholm/psr7` の `^1.8` vs `~1.8.2` 等、移植前から存在し本件とは無関係）
- [x] `composer update` により `logiscape/mcp-sdk-php v2.0.0` を導入、`php-mcp/server` と依存17パッケージを削除
- [x] SDK の API を実地確認（上表のとおり）
- [x] SDK に listen 型サーバーが無いことを確認し、in-process 化へ方針変更（設計書 第11章）

---

### Task 2: プロセス内実行の基盤

SDK を CakePHP のリクエスト内で実行する単一の入口を作る。本番とテストがこの経路を共有する。

**Files:**
- Create: `plugins/bc-mcp/src/Mcp/McpContext.php`
- Create: `plugins/bc-mcp/src/Mcp/McpRequestHandler.php`
- Create: `plugins/bc-mcp/tests/TestSuite/McpTestTrait.php`
- Test: `plugins/bc-mcp/tests/TestCase/Mcp/McpContextTest.php`

**Interfaces:**
- Consumes: なし
- Produces:
  - `BcMcp\Mcp\McpContext::setLoginUserId(?int $userId): void` / `getLoginUserId(): ?int` / `clear(): void`
  - `BcMcp\Mcp\McpRequestHandler::handle(\Mcp\Server\Transport\Http\HttpMessage $request): \Mcp\Server\Transport\Http\HttpMessage`
  - `BcMcp\Mcp\McpRequestHandler::getSessionStorePath(): string`
  - `BcMcp\Test\TestSuite\McpTestTrait::callMcp(array $request, array $headers = []): array`
  - `BcMcp\Test\TestSuite\McpTestTrait::callMcpTool(string $name, array $arguments): array`
  - `BcMcp\Test\TestSuite\McpTestTrait::modernMeta(string $protocolVersion = '2026-07-28'): array`

- [ ] **Step 1: `McpContext` の失敗するテストを書く**

Create: `plugins/bc-mcp/tests/TestCase/Mcp/McpContextTest.php`

```php
<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Mcp;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Mcp\McpContext;

/**
 * McpContextTest
 */
class McpContextTest extends BcTestCase
{

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        McpContext::clear();
        parent::tearDown();
    }

    /**
     * test ログインユーザーIDの設定と取得
     */
    public function testSetAndGetLoginUserId()
    {
        $this->assertNull(McpContext::getLoginUserId());

        McpContext::setLoginUserId(5);
        $this->assertEquals(5, McpContext::getLoginUserId());

        McpContext::clear();
        $this->assertNull(McpContext::getLoginUserId());
    }

}
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/McpContextTest.php 2>&1 | tail -15'`

Expected: FAIL（`BcMcp\Mcp\McpContext` が存在しない）。

- [ ] **Step 3: `McpContext` を実装する**

Create: `plugins/bc-mcp/src/Mcp/McpContext.php`

```php
<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Mcp;

/**
 * MCP リクエストのコンテキスト
 *
 * MCP のツールは JSON-RPC の引数だけを受け取るため、認証済みの操作者を
 * 知る手段がない。リクエストボディに引数を注入する方式は、2026-07-28 で
 * ヘッダとボディの一致が検証されるようになったため採らず、同一プロセス内の
 * コンテキストとして保持する。
 *
 * 値は必ず認証後に設定し、リクエストの終わりに clear() する。
 */
class McpContext
{

    /**
     * ログインユーザーID
     * @var int|null
     */
    private static ?int $loginUserId = null;

    /**
     * ログインユーザーIDを設定する
     *
     * @param int|null $userId ユーザーID
     * @return void
     */
    public static function setLoginUserId(?int $userId): void
    {
        self::$loginUserId = $userId;
    }

    /**
     * ログインユーザーIDを取得する
     *
     * @return int|null
     */
    public static function getLoginUserId(): ?int
    {
        return self::$loginUserId;
    }

    /**
     * コンテキストを破棄する
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$loginUserId = null;
    }

}
```

- [ ] **Step 4: テストを実行して通ることを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/McpContextTest.php 2>&1 | tail -15'`

Expected: PASS。

- [ ] **Step 5: `McpRequestHandler` を実装する**

Create: `plugins/bc-mcp/src/Mcp/McpRequestHandler.php`

```php
<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Mcp;

use Cake\Core\Configure;
use Mcp\Server\HttpServerRunner;
use Mcp\Server\Transport\Http\BufferedIo;
use Mcp\Server\Transport\Http\FileSessionStore;
use Mcp\Server\Transport\Http\HttpMessage;

/**
 * MCP リクエストをプロセス内で処理する
 *
 * SDK の HTTP トランスポートは「1リクエストを処理して終わる」モデルであり、
 * 常駐プロセスを必要としない。BufferedIo により出力が SAPI へ直接書き出される
 * のを防ぎ、レスポンスを CakePHP のレスポンスに載せられるようにする。
 *
 * 本番（McpProxyController）とテストがこの経路を共有する。
 */
class McpRequestHandler
{

    /**
     * MCP リクエストを処理する
     *
     * @param \Mcp\Server\Transport\Http\HttpMessage $request リクエスト
     * @return \Mcp\Server\Transport\Http\HttpMessage レスポンス
     */
    public function handle(HttpMessage $request): HttpMessage
    {
        $logger = new McpLogger(LOGS . 'bc_mcp_error.log');
        $sdkServer = (new McpServer())->getServer();
        $coreServer = $sdkServer->getServer();

        $runner = new HttpServerRunner(
            $coreServer,
            $coreServer->createInitializationOptions(),
            $this->getHttpOptions(),
            $logger,
            new FileSessionStore($this->getSessionStorePath()),
            new BufferedIo()
        );

        return $runner->handleRequest($request);
    }

    /**
     * HTTP トランスポートのオプションを取得する
     *
     * allowed_origins は SDK 側の DNS リバインディング対策。
     * プロキシでも検証しているため二重に効かせる。
     *
     * @return array
     */
    public function getHttpOptions(): array
    {
        $options = [];
        $allowedOrigins = (array)Configure::read('BcMcp.allowedOrigins', []);
        if ($allowedOrigins) {
            $options['allowed_origins'] = $allowedOrigins;
        }
        return $options;
    }

    /**
     * Legacy セッションの保存先を取得する
     *
     * Modern（2026-07-28）はセッションを使わないが、Legacy 世代の
     * クライアントはセッションを必要とするためディスクへ永続する。
     *
     * @return string
     */
    public function getSessionStorePath(): string
    {
        $path = TMP . 'bc_mcp_sessions';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }

}
```

- [ ] **Step 6: テスト用ヘルパを作成する**

Create: `plugins/bc-mcp/tests/TestSuite/McpTestTrait.php`

```php
<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestSuite;

use BcMcp\Mcp\McpRequestHandler;
use Mcp\Server\Transport\Http\HttpMessage;

/**
 * MCP サーバーをプロセス内で実行するテスト用ヘルパ
 *
 * 本番と同じ McpRequestHandler を経由するため、テストが実装の実経路を検証する。
 * Modern（2026-07-28）と Legacy（initialize 方式）のどちらのリクエストも実行できる。
 */
trait McpTestTrait
{

    /**
     * Modern リクエストの _meta を取得する
     *
     * @param string $protocolVersion プロトコルバージョン
     * @return array
     */
    protected function modernMeta(string $protocolVersion = '2026-07-28'): array
    {
        return [
            'io.modelcontextprotocol/protocolVersion' => $protocolVersion,
            'io.modelcontextprotocol/clientInfo' => [
                'name' => 'BcMcpTestClient',
                'version' => '1.0.0',
            ],
            'io.modelcontextprotocol/clientCapabilities' => [],
        ];
    }

    /**
     * JSON-RPC リクエストをプロセス内で実行する
     *
     * @param array $request JSON-RPC リクエスト
     * @param array $headers HTTP ヘッダ（Modern の必須ヘッダを渡す）
     * @return array デコード済みのレスポンス
     */
    protected function callMcp(array $request, array $headers = []): array
    {
        $response = $this->callMcpRaw($request, $headers);
        return json_decode($response->getBody() ?? '', true) ?? [];
    }

    /**
     * JSON-RPC リクエストを実行して HttpMessage を得る
     *
     * ステータスコードやヘッダを検証したい場合に使う。
     *
     * @param array $request JSON-RPC リクエスト
     * @param array $headers HTTP ヘッダ
     * @return \Mcp\Server\Transport\Http\HttpMessage
     */
    protected function callMcpRaw(array $request, array $headers = []): HttpMessage
    {
        $message = new HttpMessage(json_encode($request, JSON_UNESCAPED_UNICODE));
        $message->setMethod('POST');
        $message->setUri('/bc-mcp');
        $message->setHeader('Content-Type', 'application/json');
        $message->setHeader('Accept', 'application/json, text/event-stream');
        foreach($headers as $name => $value) {
            $message->setHeader($name, $value);
        }
        return (new McpRequestHandler())->handle($message);
    }

    /**
     * tools/call を実行する
     *
     * @param string $name ツール名
     * @param array $arguments 引数
     * @return array [デコード済みの戻り値, エラーかどうか]
     */
    protected function callMcpTool(string $name, array $arguments): array
    {
        $response = $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => $name,
                'arguments' => $arguments,
                '_meta' => $this->modernMeta(),
            ],
        ], [
            'MCP-Protocol-Version' => '2026-07-28',
            'Mcp-Method' => 'tools/call',
            'Mcp-Name' => $name,
        ]);

        $text = $response['result']['content'][0]['text'] ?? '';
        $isError = $response['result']['isError'] ?? isset($response['error']);
        return [json_decode($text, true) ?? $text, (bool)$isError];
    }

}
```

- [ ] **Step 7: 構文チェック**

Run: `docker exec basercms sh -c 'cd /var/www/html && php -l plugins/bc-mcp/src/Mcp/McpContext.php && php -l plugins/bc-mcp/src/Mcp/McpRequestHandler.php && php -l plugins/bc-mcp/tests/TestSuite/McpTestTrait.php'`

Expected: `No syntax errors detected`。

この時点では `McpServer` が未移植のため `McpRequestHandler::handle()` は動かない。Task 3 で通す。

- [ ] **Step 8: コミット**

```bash
git add plugins/bc-mcp/src/Mcp/McpContext.php plugins/bc-mcp/src/Mcp/McpRequestHandler.php plugins/bc-mcp/tests/TestSuite/McpTestTrait.php plugins/bc-mcp/tests/TestCase/Mcp/McpContextTest.php
git commit -m "MCP リクエストをプロセス内で処理する基盤とテストヘルパを追加"
```

---

### Task 3: McpServer / BaseMcpTool / BlogPostsTool の移植

SDK のサーバー組み立てとツール登録の作法を確立し、プロセス内実行を疎通させる。

**Files:**
- Modify: `plugins/bc-mcp/src/Mcp/McpServer.php`
- Modify: `plugins/bc-mcp/src/Mcp/BaseMcpTool.php`
- Modify: `plugins/bc-mcp/src/Mcp/BcBlog/BlogPostsTool.php`
- Test: `plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php`

**Interfaces:**
- Consumes: `McpTestTrait`（Task 2）、`McpContext`（Task 2）
- Produces:
  - `BcMcp\Mcp\McpServer::getServer(): \Mcp\Server\McpServer`
  - `BcMcp\Mcp\BaseMcpTool::registerTools(\Mcp\Server\McpServer $server): \Mcp\Server\McpServer`（抽象メソッド）
  - `BcMcp\Mcp\BaseMcpTool::resolveLoginUserId(?int $loginUserId = null): ?int`

- [ ] **Step 1: `tools/list` を検証する失敗するテストを書く**

Create: `plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php`

```php
<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Mcp;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Test\TestSuite\McpTestTrait;

/**
 * McpServerTest
 */
class McpServerTest extends BcTestCase
{

    use McpTestTrait;

    /**
     * tools/list を実行する
     *
     * @return array
     */
    private function listTools(): array
    {
        return $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/list',
            'params' => ['_meta' => $this->modernMeta()],
        ], [
            'MCP-Protocol-Version' => '2026-07-28',
            'Mcp-Method' => 'tools/list',
        ]);
    }

    /**
     * test tools/list に全プラグインのツールが並ぶ
     */
    public function testToolsListContainsAllTools()
    {
        $response = $this->listTools();

        $this->assertArrayNotHasKey('error', $response, json_encode($response, JSON_UNESCAPED_UNICODE));
        $names = array_column($response['result']['tools'], 'name');

        // BcBlog
        $this->assertContains('addBlogPost', $names);
        $this->assertContains('getBlogContents', $names);
        $this->assertContains('addBlogCategory', $names);
        $this->assertContains('addBlogTag', $names);
        // BcCustomContent
        $this->assertContains('addCustomTable', $names);
        $this->assertContains('addCustomContent', $names);
        $this->assertContains('addCustomField', $names);
        $this->assertContains('addCustomEntry', $names);
        $this->assertContains('addCustomLink', $names);
        // BaserCore
        $this->assertContains('serverInfo', $names);
    }

    /**
     * test tools/list の結果にキャッシュヒントが付与される
     *
     * 2026-07-28 では ttlMs / cacheScope が必須項目であり、SDK が付与する
     */
    public function testToolsListHasCacheHints()
    {
        $response = $this->listTools();

        $this->assertArrayHasKey('ttlMs', $response['result']);
        $this->assertArrayHasKey('cacheScope', $response['result']);
    }

    /**
     * test 全 result に resultType が付与される
     */
    public function testResultTypeIsComplete()
    {
        $response = $this->listTools();

        $this->assertEquals('complete', $response['result']['resultType']);
    }

    /**
     * test loginUserId が inputSchema に公開されていない
     *
     * 公開すると AI クライアントが他ユーザーの ID を指定できてしまう
     */
    public function testLoginUserIdIsNotExposed()
    {
        $response = $this->listTools();

        foreach($response['result']['tools'] as $tool) {
            $properties = $tool['inputSchema']['properties'] ?? [];
            $this->assertArrayNotHasKey(
                'loginUserId',
                $properties,
                "ツール {$tool['name']} の inputSchema に loginUserId が公開されています"
            );
        }
    }

}
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php 2>&1 | tail -20'`

Expected: FAIL（`PhpMcp\Server\ServerBuilder` が存在しない）。

- [ ] **Step 3: `McpServer` を SDK ベースに書き換える**

Modify: `plugins/bc-mcp/src/Mcp/McpServer.php`

```php
<?php
declare(strict_types=1);

namespace BcMcp\Mcp;

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Mcp\Server\McpServer as SdkMcpServer;

/**
 * baserCMS MCP Server
 *
 * baserCMSのデータを外部から操作するためのMCPサーバー
 * 各エンティティサーバーを統合して提供する
 *
 * プロトコルの世代判定・server/discover・必須ヘッダ検証・resultType や
 * キャッシュヒントの付与は SDK が担うため、本クラスの責務はツールの登録に絞る。
 */
class McpServer
{

    /**
     * SDK のサーバー
     * @var \Mcp\Server\McpServer
     */
    private SdkMcpServer $server;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->buildServer();
    }

    /**
     * サーバーのビルド
     */
    private function buildServer(): void
    {
        $this->server = new SdkMcpServer(
            'baserCMS MCP Server',
            new McpLogger(LOGS . 'bc_mcp_error.log'),
            '1.0.0'
        );

        $availableServers = Configure::read('BcMcp.availableServers', []);
        foreach($availableServers as $serverClass) {
            foreach($serverClass::getToolClasses() as $toolClass) {
                (new $toolClass())->registerTools($this->server);
            }
        }

        // サーバー情報ツールを追加
        $this->server->tool(
            name: 'serverInfo',
            description: 'サーバーのバージョンや環境情報を返します',
            callback: [$this, 'serverInfo'],
            inputSchema: [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'number', 'description' => 'ID'],
                ]
            ]
        );
    }

    /**
     * MCPサーバーの実体を取得する
     *
     * @return \Mcp\Server\McpServer
     */
    public function getServer(): SdkMcpServer
    {
        return $this->server;
    }

    /**
     * 標準入力からサーバーを起動する
     *
     * @return void
     */
    public function runStdio(): void
    {
        $this->server->runStdio();
    }

    /**
     * サーバー情報を取得する
     *
     * @param int|null $id ID
     * @return array
     */
    public function serverInfo(?int $id = null): array
    {
        return [
            'php_version' => PHP_VERSION,
            'basercms_version' => BcUtil::getVersion(),
            'cakephp_version' => Configure::version(),
            'server_time' => date('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get(),
            'mcp_server_version' => '1.0.0',
            'supported_clients' => ['ChatGPT', 'Claude', 'Custom MCP Clients'],
            'available_transports' => ['stdio', 'http'],
        ];
    }
}
```

`runSse()` / `setConfig()` / `registerToolsFromServer()` / `registerResourcesFromServer()` は削除する（常駐 HTTP モードの廃止に伴い不要）。

- [ ] **Step 4: `BaseMcpTool` に抽象メソッドと `resolveLoginUserId()` を追加する**

Modify: `plugins/bc-mcp/src/Mcp/BaseMcpTool.php`

`use BcContainerTrait;` の直後に追加する。

```php
    /**
     * 自身が提供するツールをサーバーに登録する
     *
     * @param \Mcp\Server\McpServer $server SDK のサーバー
     * @return \Mcp\Server\McpServer
     */
    abstract public function registerTools(\Mcp\Server\McpServer $server): \Mcp\Server\McpServer;

    /**
     * 操作者のユーザーIDを解決する
     *
     * MCP のツールは JSON-RPC の引数しか受け取らないため、認証済みの操作者は
     * McpContext から取得する。引数で明示された場合はそれを優先する
     * （stdio 経由の利用など、コンテキストを持たない経路のため）。
     *
     * @param int|null $loginUserId 引数で渡されたユーザーID
     * @return int|null
     */
    protected function resolveLoginUserId(?int $loginUserId = null): ?int
    {
        return $loginUserId ?? McpContext::getLoginUserId();
    }
```

- [ ] **Step 5: `BlogPostsTool` の登録処理を書き換える**

Modify: `plugins/bc-mcp/src/Mcp/BcBlog/BlogPostsTool.php`

1. `use PhpMcp\Server\ServerBuilder;` を削除
2. `addToolsToBuilder(ServerBuilder $builder): ServerBuilder` → `registerTools(\Mcp\Server\McpServer $server): \Mcp\Server\McpServer`
3. `return $builder` → `return $server`
4. `->withTool(handler: [self::class, 'x'], name: 'x', description: '…', inputSchema: […])` → `->tool(name: 'x', description: '…', callback: [$this, 'x'], inputSchema: […])`

**`inputSchema` の中身は1文字も変えない。**

```php
    /**
     * ブログ記事関連のツールをサーバーに登録する
     *
     * @param \Mcp\Server\McpServer $server SDK のサーバー
     * @return \Mcp\Server\McpServer
     */
    public function registerTools(\Mcp\Server\McpServer $server): \Mcp\Server\McpServer
    {
        return $server
            ->tool(
                name: 'getBlogPosts',
                description: 'ブログ記事の一覧を取得します',
                callback: [$this, 'getBlogPosts'],
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'blogContentId' => ['type' => 'number', 'description' => 'ブログコンテンツID（省略時はデフォルト）'],
                        'keyword' => ['type' => 'string', 'description' => '検索キーワード'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（null: 全て, publish: 公開）（省略時は全て）'],
                        'limit' => ['type' => 'number', 'description' => '取得件数（省略時は10件）'],
                        'page' => ['type' => 'number', 'description' => 'ページ番号（省略時は1ページ目）'],
                    ]
                ]
            )
            // 残りの withTool（getBlogPost / addBlogPost / editBlogPost / deleteBlogPost）も同じ要領で置換する
            ;
    }
```

さらに、`getAuthorId()` を呼んでいる箇所の `$loginUserId` を `$this->resolveLoginUserId($loginUserId)` に置き換える。

```php
                'user_id' => $this->getAuthorId($email, $this->resolveLoginUserId($loginUserId)),
```

`deleteBlogPost()` や `editBlogPost()` 内で `$loginUserId` を `saveDblog()` などに渡している箇所も同様に置き換える。

- [ ] **Step 6: 構文チェック**

Run: `docker exec basercms sh -c 'cd /var/www/html && php -l plugins/bc-mcp/src/Mcp/McpServer.php && php -l plugins/bc-mcp/src/Mcp/BaseMcpTool.php && php -l plugins/bc-mcp/src/Mcp/BcBlog/BlogPostsTool.php'`

Expected: `No syntax errors detected`。

- [ ] **Step 7: テストを実行する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php 2>&1 | tail -20'`

Expected: FAIL。ただしエラーは「他のツールクラスが抽象メソッド `registerTools` を実装していない」であり、`McpServer` と `BlogPostsTool` の移植自体は正しいことを示す。Task 4 で全クラスを移植して PASS になる。

- [ ] **Step 8: コミット**

```bash
git add plugins/bc-mcp/src/Mcp/McpServer.php plugins/bc-mcp/src/Mcp/BaseMcpTool.php plugins/bc-mcp/src/Mcp/BcBlog/BlogPostsTool.php plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php
git commit -m "McpServer と BlogPostsTool を SDK のツール登録 API へ移植"
```

---

### Task 4: 残りすべてのツールクラスの移植

**Files:**
- Modify: `plugins/bc-mcp/src/Mcp/BcBlog/BlogContentsTool.php`
- Modify: `plugins/bc-mcp/src/Mcp/BcBlog/BlogCategoriesTool.php`
- Modify: `plugins/bc-mcp/src/Mcp/BcBlog/BlogTagsTool.php`
- Modify: `plugins/bc-mcp/src/Mcp/BaserCore/SearchIndexesTool.php`
- Modify: `plugins/bc-mcp/src/Mcp/BaserCore/FileUploadTool.php`
- Modify: `plugins/bc-mcp/src/Mcp/BcCustomContent/CustomTablesTool.php`
- Modify: `plugins/bc-mcp/src/Mcp/BcCustomContent/CustomContentsTool.php`
- Modify: `plugins/bc-mcp/src/Mcp/BcCustomContent/CustomFieldsTool.php`
- Modify: `plugins/bc-mcp/src/Mcp/BcCustomContent/CustomEntriesTool.php`
- Modify: `plugins/bc-mcp/src/Mcp/BcCustomContent/CustomLinksTool.php`
- Test: `plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php`（Task 3 で作成済み）

**Interfaces:**
- Consumes: `BaseMcpTool::registerTools()` / `resolveLoginUserId()`（Task 3）
- Produces: 全ツールクラスの `registerTools()`

- [ ] **Step 1: 対象ファイルを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && grep -rln "addToolsToBuilder" plugins/bc-mcp/src/'`

- [ ] **Step 2: 各ファイルに Task 3 Step 5 と同じ置換を適用する**

`inputSchema` の中身とビジネスロジックは変更しない。`$loginUserId` を使っている箇所は `$this->resolveLoginUserId($loginUserId)` に置き換える。

- [ ] **Step 3: 旧 API の残存参照がないことを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && grep -rn "ServerBuilder\|withTool\|PhpMcp" plugins/bc-mcp/ 2>&1'`

Expected: 出力なし。

- [ ] **Step 4: 構文チェック**

Run: `docker exec basercms sh -c 'cd /var/www/html && for f in $(grep -rl "registerTools" plugins/bc-mcp/src/Mcp); do php -l $f; done'`

Expected: すべて `No syntax errors detected`。

- [ ] **Step 5: テストを実行して通ることを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php 2>&1 | tail -25'`

Expected: PASS（4テスト）。`ttlMs` / `cacheScope` / `resultType` が SDK により付与され、`loginUserId` が公開されていないことも確認できる。

- [ ] **Step 6: コミット**

```bash
git add plugins/bc-mcp/src/Mcp/
git commit -m "残りの MCP ツールクラスを SDK のツール登録 API へ移植"
```

---

### Task 5: ツール実行テストの移植

既存のツール実行テストを新基盤に載せ替え、`McpContext` 経由でログインユーザーが伝わることを検証する。

**Files:**
- Modify: `plugins/bc-mcp/tests/TestCase/Mcp/McpServerToolCallTest.php`

**Interfaces:**
- Consumes: `McpTestTrait::callMcpTool()`（Task 2）、`McpContext`（Task 2）
- Produces: なし

- [ ] **Step 1: テストを新基盤へ書き換える**

Modify: `plugins/bc-mcp/tests/TestCase/Mcp/McpServerToolCallTest.php`

`Dispatcher` / `CallToolRequest` / `SubscriptionManager` への依存を捨て、`McpTestTrait` を使う。**検証内容（本番で発生した引数でブログ記事が登録できること）は変えない。**

```php
namespace BcMcp\Test\TestCase\Mcp;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Test\Scenario\BlogContentScenario;
use BcMcp\Mcp\McpContext;
use BcMcp\Test\TestSuite\McpTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * McpServerToolCallTest
 *
 * MCPサーバーを別プロセスで起動する事なく、JSON-RPC の tools/call と同じ経路
 * （スキーマ検証 → 引数マッピング → ツール実行）をプロセス内で実行するテスト
 */
class McpServerToolCallTest extends BcTestCase
{

    use ScenarioAwareTrait;
    use McpTestTrait;

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        McpContext::clear();
        parent::tearDown();
    }
```

`setUp()` / 旧 `callTool()` を削除する。`testCallToolAddBlogPost()` は次のように変える。

- `McpContext::setLoginUserId(1);` を先頭（シナリオ読み込み後）に置く
- 引数配列から `'loginUserId' => 1,` を**削除する**（ボディに載せない）
- `$this->callTool(...)` → `$this->callMcpTool(...)`
- assertion はすべて維持する（`user_id` が 1 であることを含む）

```php
    public function testCallToolAddBlogPost()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'news', '/news/');

        // 認証済みの操作者はコンテキストから渡す（リクエストボディは改変しない）
        McpContext::setLoginUserId(1);

        [$result, $isError] = $this->callMcpTool('addBlogPost', [
            'title' => 'BcMcpについて',
            'name' => 'about-bcmcp',
            'status' => 0,
            'content' => '<p>BcMcpは、baserCMSを外部のAIエージェントから直接操作できるようにするMCP（Model Context Protocol）サーバーです。</p>',
            'detail' => $this->getDetail(),
        ]);

        // ツール実行時に例外が発生していない事を確認
        $this->assertFalse($isError, 'ツールの実行に失敗しました。' . (is_string($result)? $result : json_encode($result, JSON_UNESCAPED_UNICODE)));
        // ブログ記事が登録されている事を確認
        $this->assertArrayHasKey('id', $result, 'ブログ記事の登録に失敗しました。' . json_encode($result, JSON_UNESCAPED_UNICODE));
        $this->assertEquals('BcMcpについて', $result['title']);
        $this->assertEquals('about-bcmcp', $result['name']);
        $this->assertEquals(1, $result['blog_content_id']);
        // McpContext 経由でログインユーザーが反映されている事を確認
        $this->assertEquals(1, $result['user_id']);
        $this->assertFalse($result['status']);
    }
```

- [ ] **Step 2: テストを実行して通ることを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/McpServerToolCallTest.php 2>&1 | tail -25'`

Expected: PASS。`user_id` が 1 になっていれば `McpContext` 方式が機能している。

- [ ] **Step 3: コミット**

```bash
git add plugins/bc-mcp/tests/TestCase/Mcp/McpServerToolCallTest.php
git commit -m "ツール実行テストをプロセス内実行の新基盤へ移植"
```

---

### Task 6: プロキシの移植（内部 HTTP 転送の廃止）

**Files:**
- Modify: `plugins/bc-mcp/src/Controller/McpProxyController.php`
- Test: `plugins/bc-mcp/tests/TestCase/Controller/McpProxyControllerTest.php`
- Modify: `plugins/bc-mcp/tests/TestCase/Controller/Admin/OAuth2ControllerTest.php`

**Interfaces:**
- Consumes: `McpRequestHandler::handle()`（Task 2）、`McpContext`（Task 2）
- Produces:
  - `McpProxyController::toMcpMessage(array $mcpRequest): \Mcp\Server\Transport\Http\HttpMessage`

**`OAuth2ControllerTest` の常駐サーバー依存の解消（必須）**

`OAuth2ControllerTest` は MCP プロキシ経由の統合テストのために、`McpServerManger::startMcpServer()` で**実際に常駐 MCP サーバー（SSE / `127.0.0.1:3000`）を起動していた**。in-process 化により起動自体が不要になるため、次を削除する。

- `use BcMcp\Mcp\McpServerManger;`
- サーバーを起動・停止するセットアップ／ティアダウン（`startMcpServer()` / 停止処理 / ポートへの接続待ちループ / `bc_mcp_server.log` の読み出し）

統合テストは常駐サーバーを起動せず `/bc-mcp` を POST するだけでよい（プロキシが同一プロセスで SDK を実行するため）。この修正を行うまで `OAuth2ControllerTest` はエラー1・失敗2の状態になる。

- [ ] **Step 1: リクエスト変換を検証する失敗するテストを書く**

Create: `plugins/bc-mcp/tests/TestCase/Controller/McpProxyControllerTest.php`

```php
<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Controller;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Controller\McpProxyController;
use Cake\Http\ServerRequest;

/**
 * McpProxyControllerTest
 */
class McpProxyControllerTest extends BcTestCase
{

    /**
     * test toMcpMessage が MCP の必須ヘッダを引き継ぐ
     *
     * 2026-07-28 では MCP-Protocol-Version / Mcp-Method / Mcp-Name が必須ヘッダで、
     * SDK がヘッダとボディの一致を検証する
     */
    public function testToMcpMessageCarriesRequiredHeaders()
    {
        $request = new ServerRequest([
            'environment' => [
                'HTTP_MCP_PROTOCOL_VERSION' => '2026-07-28',
                'HTTP_MCP_METHOD' => 'tools/call',
                'HTTP_MCP_NAME' => 'addBlogPost',
                'HTTP_AUTHORIZATION' => 'Bearer secret-token',
            ],
        ]);
        $controller = new McpProxyController($request);

        $message = $controller->toMcpMessage(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/call']);

        $this->assertEquals('2026-07-28', $message->getHeader('MCP-Protocol-Version'));
        $this->assertEquals('tools/call', $message->getHeader('Mcp-Method'));
        $this->assertEquals('addBlogPost', $message->getHeader('Mcp-Name'));
        // 認証はプロキシで完結しているため SDK へ渡さない
        $this->assertNull($message->getHeader('Authorization'));
        $this->assertEquals('POST', $message->getMethod());
    }

}
```

`HttpMessage::getHeader()` の実在とシグネチャを確認し、無ければ `getHeaders()` から取り出す形に合わせる。

Run: `docker exec basercms sh -c 'cd /var/www/html && grep -n "public function getHeader" -A 5 vendor/logiscape/mcp-sdk-php/src/Server/Transport/Http/HttpMessage.php'`

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Controller/McpProxyControllerTest.php 2>&1 | tail -20'`

Expected: FAIL（`toMcpMessage()` が存在しない）。

- [ ] **Step 3: `toMcpMessage()` を実装する**

Modify: `plugins/bc-mcp/src/Controller/McpProxyController.php`

```php
    /**
     * CakePHP のリクエストを SDK の HttpMessage に変換する
     *
     * 2026-07-28 では MCP-Protocol-Version / Mcp-Method / Mcp-Name が必須ヘッダで、
     * SDK がヘッダとボディの一致を検証する。クライアントが送ってきたヘッダを
     * そのまま引き継ぎ、ボディも改変しない事で整合性を保つ。
     * Authorization は認証がプロキシで完結しているため渡さない。
     *
     * @param array $mcpRequest MCP リクエスト
     * @return \Mcp\Server\Transport\Http\HttpMessage
     */
    public function toMcpMessage(array $mcpRequest): HttpMessage
    {
        $message = new HttpMessage(json_encode($mcpRequest, JSON_UNESCAPED_UNICODE));
        $message->setMethod($this->request->getMethod());
        $message->setUri('/bc-mcp');
        $message->setHeader('Content-Type', 'application/json');
        $message->setHeader('Accept', 'application/json, text/event-stream');

        $targets = ['MCP-Protocol-Version', 'Mcp-Method', 'Mcp-Name'];
        foreach($targets as $target) {
            $value = $this->request->getHeaderLine($target);
            if ($value !== '') {
                $message->setHeader($target, $value);
            }
        }
        // x-mcp-header 由来の Mcp-Param-* も引き継ぐ
        foreach($this->request->getHeaders() as $name => $values) {
            if (stripos($name, 'Mcp-Param-') === 0) {
                $message->setHeader($name, implode(', ', $values));
            }
        }
        return $message;
    }
```

`use Mcp\Server\Transport\Http\HttpMessage;` を追加する。

- [ ] **Step 4: `index()` を書き換える**

Modify: `plugins/bc-mcp/src/Controller/McpProxyController.php`

内部 HTTP 転送・サーバー起動チェック・応答の偽装をすべて削除し、`McpRequestHandler` を呼ぶ。

```php
    /**
     * MCP リクエストの受け口
     *
     * 常駐プロセスを持たず、CakePHP のリクエスト内で SDK を実行する。
     * プロトコルの世代判定・必須ヘッダ検証・resultType やキャッシュヒントの
     * 付与はすべて SDK の責務であり、ここでは応答に手を加えない。
     */
    public function index()
    {
        // OPTIONSリクエストの場合はCORSレスポンスを返す
        if ($this->request->getMethod() === 'OPTIONS') {
            return $this->_handleOptionsRequest();
        }

        // Modern（2026-07-28）では GET ストリームが廃止されている
        if (in_array($this->request->getMethod(), ['GET', 'DELETE'], true)) {
            return $this->response
                ->withStatus(405)
                ->withHeader('Content-Type', 'application/json')
                ->withStringBody(json_encode([
                    'jsonrpc' => '2.0',
                    'error' => ['code' => -32601, 'message' => 'Method not allowed. Use POST.']
                ], JSON_UNESCAPED_UNICODE));
        }

        try {
            $requestBody = (string)$this->request->getBody();
            if (empty($requestBody)) {
                return $this->response->withStatus(400);
            }

            $mcpRequest = json_decode($requestBody, true);
            if (!$mcpRequest || !isset($mcpRequest['jsonrpc']) || $mcpRequest['jsonrpc'] !== '2.0') {
                throw new BadRequestException('Invalid MCP request format');
            }

            // クライアントの世代とプロトコルバージョンを記録する
            NegotiationLogger::log($mcpRequest, $this->request->getHeaderLine('MCP-Protocol-Version'));

            // 認証済みの操作者をコンテキストに設定する（ボディは改変しない）
            McpContext::setLoginUserId((int)$this->request->getAttribute('oauth_user_id'));

            if (!$this->checkPermission($mcpRequest)) {
                return $this->response
                    ->withStatus(403)
                    ->withHeader('Content-Type', 'application/json')
                    ->withStringBody(json_encode([
                        'jsonrpc' => '2.0',
                        'error' => [
                            'code' => 403,
                            'message' => 'Forbidden: You do not have permission to perform this action.'
                        ]
                    ], JSON_UNESCAPED_UNICODE));
            }

            $mcpResponse = (new McpRequestHandler())->handle($this->toMcpMessage($mcpRequest));

            $response = $this->response
                ->withStatus($mcpResponse->getStatusCode())
                ->withStringBody((string)$mcpResponse->getBody());
            foreach($mcpResponse->getHeaders() as $name => $value) {
                $response = $response->withHeader($name, $value);
            }
            return $response;
        } catch (BadRequestException $e) {
            throw $e;
        } catch (ForbiddenException $e) {
            return $this->response
                ->withStatus(403)
                ->withHeader('Content-Type', 'application/json')
                ->withStringBody(json_encode([
                    'jsonrpc' => '2.0',
                    'error' => ['code' => 403, 'message' => $e->getMessage()]
                ], JSON_UNESCAPED_UNICODE));
        } catch (\Exception $e) {
            return $this->response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->withStringBody(json_encode([
                    'jsonrpc' => '2.0',
                    'error' => ['code' => 500, 'message' => 'MCPリクエストの処理に失敗しました: ' . $e->getMessage()]
                ], JSON_UNESCAPED_UNICODE));
        } finally {
            McpContext::clear();
        }
    }
```

`checkPermission()` は `$mcpRequest['params']['arguments']['loginUserId']` を参照しているため、`McpContext::getLoginUserId()` を使うように書き換える。

```php
        $user = $usersService->get(McpContext::getLoginUserId());
```

`getProtocolVersion()` / `sendMcpRequest()` / `$this->mcpServerManager` の宣言と初期化を削除する。`use Cake\Http\Client;` / `use Cake\Http\Exception\ServiceUnavailableException;` / `use BcMcp\Mcp\McpServerManger;` も削除する。`use BcMcp\Mcp\McpContext;` / `use BcMcp\Mcp\McpRequestHandler;` / `use BcMcp\Mcp\NegotiationLogger;` を追加する。

**注意:** `NegotiationLogger` は Task 11 で作成する。Task 6 の時点では該当行をコメントアウトしておき、Task 11 で有効化する。

- [ ] **Step 5: テストを実行して通ることを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Controller/McpProxyControllerTest.php 2>&1 | tail -20'`

Expected: PASS。

- [ ] **Step 6: 構文チェック**

Run: `docker exec basercms sh -c 'cd /var/www/html && php -l plugins/bc-mcp/src/Controller/McpProxyController.php'`

Expected: `No syntax errors detected`。

- [ ] **Step 7: コミット**

```bash
git add plugins/bc-mcp/src/Controller/McpProxyController.php plugins/bc-mcp/tests/TestCase/Controller/McpProxyControllerTest.php
git commit -m "プロキシの内部 HTTP 転送を廃止しプロセス内実行へ切り替え"
```

---

### Task 7: 常駐プロセス関連の削除と管理画面の再構成

> **このタスクは Task 11（ネゴシエーションのロギング）の完了後に着手する。** 管理画面の「直近の接続状況」が `NegotiationLogger::readRecent()` を使うため。実行順序は Task 6 → Task 11 → Task 7 → Task 8 とする。

**Files:**
- Delete: `plugins/bc-mcp/src/Mcp/McpServerManger.php`
- Modify: `plugins/bc-mcp/src/Command/McpServerCommand.php`
- Modify: `plugins/bc-mcp/tests/TestCase/Command/McpServerCommandTest.php`
- Modify: `plugins/bc-mcp/src/Controller/Admin/McpServerManagerController.php`
- Modify: `plugins/bc-mcp/src/BcMcpPlugin.php`
- Modify: `plugins/bc-mcp/templates/Admin/McpServerManager/index.php`
- Delete: `plugins/bc-mcp/templates/Admin/McpServerManager/configure.php`（ポート設定が不要になるため）
- Test: `plugins/bc-mcp/tests/TestCase/Controller/Admin/McpServerManagerControllerTest.php`

**Interfaces:**
- Consumes: `BcMcp\Mcp\McpServer`（Task 3）、`BcMcp\Mcp\NegotiationLogger::readRecent()`（Task 11）
- Produces: `McpServerManagerController::getRegisteredTools(): array` — プラグイン単位にグループ化したツール情報（`['BcBlog' => [['name' => 'addBlogPost', 'description' => '…'], …], …]`）

**管理画面の表示内容**

現状の4ブロックを次のように再構成する。

| 現状のブロック | 扱い |
|---|---|
| MCPサーバー状態（稼働中/停止中・PID・内部URL・設定用URL） | 死活表示・PID・内部URL を削除し、接続情報のブロックに再構成 |
| サーバー操作（起動/停止/再起動ボタン） | 削除 |
| AIエージェントでの設定方法（手順1〜3） | 手順1「起動ボタンで起動してください」を削除し2手順にする |
| 利用可能な機能（手書き3行） | 登録済みツールからの自動生成に置き換える。**現状の手書きは実態とずれており、40件以上あるツールが3行しか書かれていない** |

再構成後は次の4ブロックとする。

1. **接続情報** — MCP エンドポイント URL（コピーボタン付き、現状から流用）、`.well-known/oauth-authorization-server` と `.well-known/oauth-protected-resource` の URL、対応プロトコルバージョン（Modern `2026-07-28` と Legacy の両対応であること）
2. **利用可能なツール** — 登録済みツールの名前と説明をプラグイン単位でグループ化して一覧表示
3. **AIエージェントでの設定方法** — URL 登録から始まる2手順
4. **直近の接続状況** — `NegotiationLogger::readRecent()` から世代・プロトコルバージョン・クライアント名・日時を表示。**Claude が Modern に切り替わったことを管理画面から気づけるようにする**（死活表示の代わりになる実用情報）

- [ ] **Step 1: `McpServerManger` の参照箇所を洗い出す**

Run: `docker exec basercms sh -c 'cd /var/www/html && grep -rn "McpServerManger\|isServerRunning\|mcpServerManager" plugins/bc-mcp/ 2>&1'`

- [ ] **Step 2: コマンドを stdio 専用にする**

Modify: `plugins/bc-mcp/src/Command/McpServerCommand.php`

`--transport` / `--host` / `--port` オプションを削除し、stdio 固定にする。`--connection` は残す（stdio 経由でテスト用接続を使う余地があるため）。`--config` は `setConfig()` の削除に伴い削除する。

```php
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription('baserCMS MCP サーバーを標準入出力で起動します')
            ->addOption('connection', [
                'help' => 'サーバーが使用する DB 接続名。default 以外を指定すると default にエイリアスする（主にテストで test 接続を使う用途）。'
                    . 'プラグインのロード自体は bootstrap で環境変数 BC_CONNECTION により切り替わる。',
                'default' => 'default'
            ]);

        return $parser;
    }
```

`execute()` の transport 分岐を削除し、`$server->runStdio();` のみを呼ぶ。HTTP 経由の利用は `/bc-mcp` エンドポイントが担う旨をコメントに残す。

- [ ] **Step 3: コマンドのテストを更新する**

Modify: `plugins/bc-mcp/tests/TestCase/Command/McpServerCommandTest.php`

`testBuildOptionParser()` の `transport` / `host` / `port` に関する assertion を削除し、`connection` オプションの存在を検証する形に変える。

```php
    public function testBuildOptionParser()
    {
        $command = new McpServerCommand();
        $parser = $command->getOptionParser();

        $options = $parser->options();
        $this->assertArrayHasKey('connection', $options);
        $this->assertEquals('default', $options['connection']->defaultValue());
        // HTTP 経由の利用は /bc-mcp エンドポイントが担うため、transport の選択肢は持たない
        $this->assertArrayNotHasKey('transport', $options);
    }
```

`testExecuteHelp()` の期待文字列を新しい説明文に合わせる。

- [ ] **Step 4: `McpServerManger` を削除する**

```bash
git rm plugins/bc-mcp/src/Mcp/McpServerManger.php
```

- [ ] **Step 5: ツール一覧の取得を検証する失敗するテストを書く**

Create: `plugins/bc-mcp/tests/TestCase/Controller/Admin/McpServerManagerControllerTest.php`

```php
<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMcp\Controller\Admin\McpServerManagerController;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\Http\ServerRequest;

/**
 * McpServerManagerControllerTest
 */
class McpServerManagerControllerTest extends BcTestCase
{

    use ScenarioAwareTrait;

    /**
     * test getRegisteredTools が登録済みツールをプラグイン単位で返す
     *
     * 移植前は「利用可能な機能」がテンプレートに手書きされており実態とずれていた
     */
    public function testGetRegisteredTools()
    {
        $controller = new McpServerManagerController(new ServerRequest());

        $tools = $controller->getRegisteredTools();

        $this->assertArrayHasKey('BcBlog', $tools);
        $this->assertArrayHasKey('BcCustomContent', $tools);

        $blogToolNames = array_column($tools['BcBlog'], 'name');
        $this->assertContains('addBlogPost', $blogToolNames);

        // 名前だけでなく説明も表示するため、説明が空でない事を確認する
        foreach($tools['BcBlog'] as $tool) {
            $this->assertNotEmpty($tool['description'], "ツール {$tool['name']} の説明が空です");
        }
    }

    /**
     * test 管理画面が表示される
     */
    public function testIndex()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest('/baser/admin/bc-mcp/mcp-server-manager'));

        $this->get('/baser/admin/bc-mcp/mcp-server-manager');

        $this->assertResponseSuccess();
        // 接続情報と対応プロトコルバージョンが表示される
        $this->assertResponseContains('/bc-mcp');
        $this->assertResponseContains('2026-07-28');
        // 起動・停止の操作は無くなっている
        $this->assertResponseNotContains('mcp_server_manager/start');
        $this->assertResponseNotContains('mcp_server_manager/stop');
    }

}
```

- [ ] **Step 6: テストを実行して失敗を確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Controller/Admin/McpServerManagerControllerTest.php 2>&1 | tail -20'`

Expected: FAIL（`getRegisteredTools()` が存在しない）。

- [ ] **Step 7: コントローラを再構成する**

Modify: `plugins/bc-mcp/src/Controller/Admin/McpServerManagerController.php`

`start()` / `stop()` / `restart()` / `configure()` アクションと `McpServerManger` への依存を削除する。`index()` は情報表示画面として残す。

```php
    /**
     * MCP サーバー情報
     *
     * 常駐プロセスを持たないため、死活監視ではなく接続情報・提供ツール・
     * 直近の接続状況を表示する。
     */
    public function index()
    {
        $baseUrl = rtrim(Router::url('/', true), '/');

        $this->set([
            'endpointUrl' => $baseUrl . '/bc-mcp',
            'authorizationServerMetadataUrl' => $baseUrl . '/.well-known/oauth-authorization-server',
            'protectedResourceMetadataUrl' => $baseUrl . '/.well-known/oauth-protected-resource',
            'protocolVersions' => ['2026-07-28', '2025-11-25', '2025-06-18', '2025-03-26', '2024-11-05'],
            'tools' => $this->getRegisteredTools(),
            'negotiations' => NegotiationLogger::readRecent(10),
        ]);
    }

    /**
     * 登録済みツールをプラグイン単位で取得する
     *
     * テンプレートへの手書きをやめ、実際に登録されているツールを表示する。
     *
     * @return array
     */
    public function getRegisteredTools(): array
    {
        $result = [];
        $availableServers = Configure::read('BcMcp.availableServers', []);
        foreach($availableServers as $pluginName => $serverClass) {
            $server = new SdkMcpServer('info');
            foreach($serverClass::getToolClasses() as $toolClass) {
                (new $toolClass())->registerTools($server);
            }
            $result[$pluginName] = array_map(fn($tool) => [
                'name' => $tool->name,
                'description' => $tool->description,
            ], $server->getServer()->getTools());
        }
        return $result;
    }
```

`getTools()` に相当する取得方法は SDK の実装に合わせる。存在しない場合は `tools/list` を `McpRequestHandler` 経由で1回実行し、その結果を使う（本番と同じ経路になるためこちらの方が確実）。

Run: `docker exec basercms sh -c 'cd /var/www/html && grep -n "public function getTools\|public function listTools" vendor/logiscape/mcp-sdk-php/src/Server/Server.php vendor/logiscape/mcp-sdk-php/src/Server/McpServer.php'`

- [ ] **Step 8: ルートを整理する**

Modify: `plugins/bc-mcp/src/BcMcpPlugin.php`

`mcp-server-manager/configure`（GET / POST）、`start`、`stop`、`restart` のルートを削除する。`mcp-server-manager` の GET のみ残す。

- [ ] **Step 9: テンプレートを再構成する**

Modify: `plugins/bc-mcp/templates/Admin/McpServerManager/index.php`

「MCPサーバー状態」と「サーバー操作」のブロックを削除し、次の4ブロックに再構成する。既存の `bca-panel-box` / `bca-data-list` のマークアップと `copyToClipboard()` は流用する。

1. **接続情報** — `$endpointUrl`（コピーボタン付き）、`$authorizationServerMetadataUrl`、`$protectedResourceMetadataUrl`、`$protocolVersions` を「Modern（2026-07-28）と旧世代の両対応」として表示
2. **利用可能なツール** — `$tools` をプラグイン単位に見出しを付け、ツール名と説明を一覧表示
3. **AIエージェントでの設定方法** — 手順を2つにする（手順1: 上記 URL を AI エージェントの設定に登録する／手順2: 「ブログ記事を追加して」などの指示で操作できる）
4. **直近の接続状況** — `$negotiations` を日時・世代・プロトコルバージョン・クライアント名の表で表示。空の場合は「まだ接続がありません」と表示

Delete: `plugins/bc-mcp/templates/Admin/McpServerManager/configure.php`

- [ ] **Step 10: テストを実行して通ることを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Controller/Admin/McpServerManagerControllerTest.php 2>&1 | tail -25'`

Expected: PASS。

- [ ] **Step 11: 画面の描画に警告が出ていないことを確認する**

`assertResponseSuccess()` は未定義変数の警告を握り潰すため、ログを確認する。

Run: `docker exec basercms sh -c 'cd /var/www/html && grep -c "Undefined variable" logs/debug.log 2>/dev/null; grep "Undefined variable" logs/debug.log 2>/dev/null | tail -5'`

Expected: 新規の `Undefined variable` が出ていないこと。

- [ ] **Step 12: 残存参照がないことを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && grep -rn "McpServerManger\|isServerRunning\|runSse\|--transport" plugins/bc-mcp/ 2>&1'`

Expected: 出力なし。

- [ ] **Step 13: テストを実行する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage --testsuite BcMcp 2>&1 | tail -30'`

Expected: PASS。

- [ ] **Step 14: コミット**

```bash
git add -A plugins/bc-mcp/
git commit -m "常駐 MCP サーバープロセスを廃止し管理画面を情報表示に再構成"
```

---

### Task 8: Dual-era 疎通テスト

**Files:**
- Create: `plugins/bc-mcp/tests/TestCase/Mcp/DualEraTest.php`

**Interfaces:**
- Consumes: `McpTestTrait`（Task 2）
- Produces: なし

- [ ] **Step 1: 両世代の疎通を検証するテストを書く**

Create: `plugins/bc-mcp/tests/TestCase/Mcp/DualEraTest.php`

```php
<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Mcp;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Test\TestSuite\McpTestTrait;

/**
 * DualEraTest
 *
 * Modern（2026-07-28）と Legacy（initialize 方式）の両世代が
 * 同一サーバーで動作することを検証する
 */
class DualEraTest extends BcTestCase
{

    use McpTestTrait;

    /**
     * test server/discover が対応バージョンを返す（Modern の MUST 要件）
     */
    public function testServerDiscover()
    {
        $response = $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'server/discover',
            'params' => ['_meta' => $this->modernMeta()],
        ], [
            'MCP-Protocol-Version' => '2026-07-28',
            'Mcp-Method' => 'server/discover',
        ]);

        $this->assertArrayNotHasKey('error', $response, json_encode($response, JSON_UNESCAPED_UNICODE));
        $this->assertArrayHasKey('capabilities', $response['result']);
        $this->assertArrayHasKey('serverInfo', $response['result']);
    }

    /**
     * test Legacy の initialize が同一サーバーで応答する
     */
    public function testLegacyInitialize()
    {
        $response = $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => '2025-06-18',
                'capabilities' => [],
                'clientInfo' => ['name' => 'LegacyTestClient', 'version' => '1.0.0'],
            ],
        ], [
            'MCP-Protocol-Version' => '2025-06-18',
            'Mcp-Method' => 'initialize',
        ]);

        $this->assertArrayNotHasKey('error', $response, json_encode($response, JSON_UNESCAPED_UNICODE));
        $this->assertArrayHasKey('protocolVersion', $response['result']);
        $this->assertArrayHasKey('capabilities', $response['result']);
    }

    /**
     * test capabilities に未提供機能が申告されない
     *
     * 移植前は resources / prompts を listChanged: true と虚偽申告していた
     */
    public function testCapabilitiesDoNotAdvertiseUnsupportedFeatures()
    {
        $response = $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'server/discover',
            'params' => ['_meta' => $this->modernMeta()],
        ], [
            'MCP-Protocol-Version' => '2026-07-28',
            'Mcp-Method' => 'server/discover',
        ]);

        $capabilities = $response['result']['capabilities'];
        $this->assertArrayHasKey('tools', $capabilities);
        $this->assertArrayNotHasKey('resources', $capabilities);
        $this->assertArrayNotHasKey('prompts', $capabilities);
    }

    /**
     * test 未対応バージョンは UnsupportedProtocolVersionError になる
     */
    public function testUnsupportedProtocolVersion()
    {
        $response = $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/list',
            'params' => ['_meta' => $this->modernMeta('1900-01-01')],
        ], [
            'MCP-Protocol-Version' => '1900-01-01',
            'Mcp-Method' => 'tools/list',
        ]);

        $this->assertEquals(-32022, $response['error']['code']);
        $this->assertArrayHasKey('supported', $response['error']['data']);
    }

    /**
     * test ヘッダとボディの不一致は HeaderMismatch になる
     */
    public function testHeaderMismatch()
    {
        $response = $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => 'serverInfo',
                'arguments' => [],
                '_meta' => $this->modernMeta(),
            ],
        ], [
            'MCP-Protocol-Version' => '2026-07-28',
            'Mcp-Method' => 'tools/call',
            // ボディの params.name と一致しない
            'Mcp-Name' => 'getBlogPosts',
        ]);

        $this->assertEquals(-32020, $response['error']['code']);
    }

}
```

- [ ] **Step 2: テストを実行する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/DualEraTest.php 2>&1 | tail -30'`

Expected: PASS。失敗した場合は SDK の実際のレスポンス構造に合わせて assertion のキー名を修正する。**エラーコード（-32022 / -32020）と「両世代が応答する」という検証内容は緩めない。**

- [ ] **Step 3: コミット**

```bash
git add plugins/bc-mcp/tests/TestCase/Mcp/DualEraTest.php
git commit -m "Modern と Legacy の両世代疎通を検証するテストを追加"
```

---

### Task 9: Origin ヘッダ検証

**Files:**
- Modify: `plugins/bc-mcp/src/Controller/McpProxyController.php`
- Modify: `plugins/bc-mcp/config/setting.php`
- Modify: `plugins/bc-mcp/tests/TestCase/Controller/McpProxyControllerTest.php`

**Interfaces:**
- Consumes: `McpProxyController::toMcpMessage()`（Task 6）
- Produces: `McpProxyController::isAllowedOrigin(string $origin): bool`

- [ ] **Step 1: 失敗するテストを書く**

Modify: `plugins/bc-mcp/tests/TestCase/Controller/McpProxyControllerTest.php`（メソッドを追加）

```php
    /**
     * test 許可オリジンの判定
     */
    public function testIsAllowedOrigin()
    {
        \Cake\Core\Configure::write('BcMcp.allowedOrigins', ['https://claude.ai']);
        $controller = new McpProxyController(new ServerRequest());

        $this->assertTrue($controller->isAllowedOrigin('https://claude.ai'));
        $this->assertFalse($controller->isAllowedOrigin('https://evil.example.com'));
    }

    /**
     * test 許可されないオリジンからのリクエストは 403 になる
     *
     * Origin 検証は DNS リバインディング対策であり、認証より前に効かせる
     */
    public function testDisallowedOriginReturnsForbidden()
    {
        \Cake\Core\Configure::write('BcMcp.allowedOrigins', ['https://claude.ai']);

        $this->configRequest([
            'headers' => ['Origin' => 'https://evil.example.com', 'Content-Type' => 'application/json']
        ]);
        $this->post('/bc-mcp', json_encode(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/list']));

        $this->assertResponseCode(403);
    }
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage --filter Origin plugins/bc-mcp/tests/TestCase/Controller/McpProxyControllerTest.php 2>&1 | tail -20'`

Expected: FAIL（`isAllowedOrigin()` が存在しない）。

- [ ] **Step 3: 設定項目を追加する**

Modify: `plugins/bc-mcp/config/setting.php`

`'BcMcp' => [...]` の中に追加する。

```php
        /**
         * Origin ヘッダの許可リスト
         *
         * DNS リバインディング攻撃対策として、ブラウザから送信された Origin を検証する。
         * 空配列の場合は自サイトのオリジンのみを許可する。
         * Origin ヘッダを持たないリクエスト（サーバー間通信）は検証対象外。
         */
        'allowedOrigins' => [],
```

- [ ] **Step 4: `isAllowedOrigin()` を実装し `beforeFilter()` で検証する**

Modify: `plugins/bc-mcp/src/Controller/McpProxyController.php`

```php
    /**
     * Origin が許可されているかを判定する
     *
     * Streamable HTTP の MUST 要件。DNS リバインディング攻撃により、
     * 悪意あるサイトからローカルの MCP サーバーが操作されるのを防ぐ。
     *
     * @param string $origin Origin ヘッダの値
     * @return bool
     */
    public function isAllowedOrigin(string $origin): bool
    {
        $allowed = (array)Configure::read('BcMcp.allowedOrigins', []);
        if (!$allowed) {
            $siteUrl = rtrim((string)env('SITE_URL', ''), '/');
            if ($siteUrl) {
                $parts = parse_url($siteUrl);
                $allowed = [$parts['scheme'] . '://' . $parts['host'] . (isset($parts['port'])? ':' . $parts['port'] : '')];
            }
        }
        return in_array($origin, $allowed, true);
    }
```

`beforeFilter()` の OPTIONS 判定の直後、OAuth2 検証の**前**に置く。

```php
        // Origin 検証は認証より前に行う（transport レベルの要件）
        $origin = $this->request->getHeaderLine('Origin');
        if ($origin !== '' && !$this->isAllowedOrigin($origin)) {
            $event->setResult($this->response
                ->withStatus(403)
                ->withHeader('Content-Type', 'application/json')
                ->withStringBody(json_encode([
                    'jsonrpc' => '2.0',
                    'error' => ['code' => -32600, 'message' => 'Forbidden: invalid Origin.']
                ], JSON_UNESCAPED_UNICODE)));
            return;
        }
```

`initialize()` の `Access-Control-Allow-Origin: '*'` は、許可された Origin をそのまま返す形に修正する。

- [ ] **Step 5: テストを実行して通ることを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Controller/McpProxyControllerTest.php 2>&1 | tail -25'`

Expected: PASS。

- [ ] **Step 6: コミット**

```bash
git add plugins/bc-mcp/src/Controller/McpProxyController.php plugins/bc-mcp/config/setting.php plugins/bc-mcp/tests/TestCase/Controller/McpProxyControllerTest.php
git commit -m "Origin ヘッダ検証を追加（DNS リバインディング対策）"
```

---

### Task 10: `iss` パラメータ（RFC 9207）の付与

**Files:**
- Modify: `plugins/bc-mcp/src/Lib/OAuth2Util.php`
- Modify: `plugins/bc-mcp/src/Controller/Admin/Oauth2Controller.php`
- Modify: `plugins/bc-mcp/src/Controller/Oauth2Controller.php`
- Create: `plugins/bc-mcp/tests/TestCase/Lib/OAuth2UtilTest.php`

**Interfaces:**
- Consumes: なし
- Produces:
  - `BcMcp\Lib\OAuth2Util::getIssuer(\Cake\Http\ServerRequest $request): string`
  - `BcMcp\Lib\OAuth2Util::addIssuerToUrl(string $url, string $issuer): string`

- [ ] **Step 1: 失敗するテストを書く**

Create: `plugins/bc-mcp/tests/TestCase/Lib/OAuth2UtilTest.php`

```php
<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Lib;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Lib\OAuth2Util;
use Cake\Http\ServerRequest;

/**
 * OAuth2UtilTest
 */
class OAuth2UtilTest extends BcTestCase
{

    /**
     * test getIssuer がメタデータの issuer と同じ値を返す
     */
    public function testGetIssuer()
    {
        $request = new ServerRequest([
            'environment' => ['HTTP_HOST' => 'example.com', 'HTTPS' => 'on'],
        ]);

        $this->assertEquals('https://example.com/bc-mcp', OAuth2Util::getIssuer($request));
    }

    /**
     * test addIssuerToUrl が iss クエリを付与する
     */
    public function testAddIssuerToUrl()
    {
        $result = OAuth2Util::addIssuerToUrl(
            'https://claude.ai/callback?code=abc&state=xyz',
            'https://example.com/bc-mcp'
        );

        parse_str((string)parse_url($result, PHP_URL_QUERY), $query);
        $this->assertEquals('https://example.com/bc-mcp', $query['iss']);
        // 既存のクエリは保持される
        $this->assertEquals('abc', $query['code']);
        $this->assertEquals('xyz', $query['state']);
    }

    /**
     * test addIssuerToUrl はフラグメントを壊さない
     */
    public function testAddIssuerToUrlWithFragment()
    {
        $result = OAuth2Util::addIssuerToUrl(
            'https://claude.ai/callback#code=abc',
            'https://example.com/bc-mcp'
        );

        $this->assertStringContainsString('iss=', $result);
        $this->assertStringContainsString('#code=abc', $result);
    }

}
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Lib/OAuth2UtilTest.php 2>&1 | tail -20'`

Expected: FAIL。

- [ ] **Step 3: `OAuth2Util` にメソッドを追加する**

Modify: `plugins/bc-mcp/src/Lib/OAuth2Util.php`

```php
    /**
     * 認可サーバーの issuer 識別子を取得する
     *
     * RFC 8414 のメタデータで公開する issuer と同一の値でなければならないため、
     * 導出処理をここに集約する。
     *
     * @param \Cake\Http\ServerRequest $request リクエスト
     * @return string
     */
    public static function getIssuer(\Cake\Http\ServerRequest $request): string
    {
        $scheme = $request->is('https')? 'https' : 'http';
        $host = $request->getHeaderLine('Host');
        if (!$host) {
            $host = $request->getEnv('HTTP_HOST')?: 'localhost';
        }
        return $scheme . '://' . $host . '/bc-mcp';
    }

    /**
     * URL に iss クエリを付与する
     *
     * RFC 9207。認可レスポンスに issuer を含める事で mix-up 攻撃を防ぐ。
     *
     * @param string $url 対象の URL
     * @param string $issuer issuer 識別子
     * @return string
     */
    public static function addIssuerToUrl(string $url, string $issuer): string
    {
        $fragment = '';
        $hashPos = strpos($url, '#');
        if ($hashPos !== false) {
            $fragment = substr($url, $hashPos);
            $url = substr($url, 0, $hashPos);
        }
        $separator = str_contains($url, '?')? '&' : '?';
        return $url . $separator . 'iss=' . rawurlencode($issuer) . $fragment;
    }
```

- [ ] **Step 4: テストを実行して通ることを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Lib/OAuth2UtilTest.php 2>&1 | tail -20'`

Expected: PASS。

- [ ] **Step 5: 認可レスポンスに `iss` を付与する**

Modify: `plugins/bc-mcp/src/Controller/Admin/Oauth2Controller.php`

`approve` 分岐（160行目付近）を書き換える。

```php
                    $authResponse = $server->completeAuthorizationRequest($authRequest, $this->response);

                    // RFC 9207: 認可レスポンスに issuer を含める
                    $location = $authResponse->getHeaderLine('Location');
                    if ($location !== '') {
                        $authResponse = $authResponse->withHeader(
                            'Location',
                            OAuth2Util::addIssuerToUrl($location, OAuth2Util::getIssuer($this->request))
                        );
                    }
                    return $authResponse;
```

`deny` 分岐のリダイレクト URL にも付与する（エラー応答も authorization response であるため）。

```php
                    $redirectUrl = OAuth2Util::addIssuerToUrl(
                        $redirectUri . '?' . http_build_query($params),
                        OAuth2Util::getIssuer($this->request)
                    );
                    return $this->redirect($redirectUrl);
```

`use BcMcp\Lib\OAuth2Util;` が未 import なら追加する。

- [ ] **Step 6: メタデータに対応を宣言する**

Modify: `plugins/bc-mcp/src/Controller/Oauth2Controller.php`

`authorizationServerMetadata()` の `$metadata` に追加する。

```php
                'authorization_response_iss_parameter_supported' => true,
```

同メソッド内の `'issuer' => $baseUrl . '/bc-mcp',` を `'issuer' => OAuth2Util::getIssuer($this->request),` に置き換え、authorize 側と同じ導出処理を使う。`use BcMcp\Lib\OAuth2Util;` を追加する。

- [ ] **Step 7: OAuth2 の既存テストに回帰がないことを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Controller/ plugins/bc-mcp/tests/TestCase/Lib/ plugins/bc-mcp/tests/TestCase/Service/ 2>&1 | tail -25'`

Expected: PASS。

- [ ] **Step 8: コミット**

```bash
git add plugins/bc-mcp/src/Lib/OAuth2Util.php plugins/bc-mcp/src/Controller/Admin/Oauth2Controller.php plugins/bc-mcp/src/Controller/Oauth2Controller.php plugins/bc-mcp/tests/TestCase/Lib/OAuth2UtilTest.php
git commit -m "認可レスポンスに iss パラメータを付与（RFC 9207）"
```

---

### Task 11: ネゴシエーションのロギング

**Files:**
- Create: `plugins/bc-mcp/src/Mcp/NegotiationLogger.php`
- Modify: `plugins/bc-mcp/src/Controller/McpProxyController.php`
- Create: `plugins/bc-mcp/tests/TestCase/Mcp/NegotiationLoggerTest.php`

> **このタスクは Task 7（管理画面の再構成）より前に着手する。** 管理画面の「直近の接続状況」が `readRecent()` を使うため。実行順序は Task 6 → Task 11 → Task 7 → Task 8 とする。

**Interfaces:**
- Consumes: なし
- Produces:
  - `BcMcp\Mcp\NegotiationLogger::describe(array $mcpRequest, string $protocolVersionHeader): array`
  - `BcMcp\Mcp\NegotiationLogger::log(array $mcpRequest, string $protocolVersionHeader): void`
  - `BcMcp\Mcp\NegotiationLogger::readRecent(int $limit = 10): array` — ログから直近の接続状況を新しい順に返す（`['loggedAt' => '2026-08-12 16:07:38', 'era' => 'modern', 'protocolVersion' => '2026-07-28', 'clientName' => 'claude-ai', 'clientVersion' => '2.0.0', 'method' => 'tools/call']` の配列）

- [ ] **Step 1: 失敗するテストを書く**

Create: `plugins/bc-mcp/tests/TestCase/Mcp/NegotiationLoggerTest.php`

```php
<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Mcp;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Mcp\NegotiationLogger;

/**
 * NegotiationLoggerTest
 */
class NegotiationLoggerTest extends BcTestCase
{

    /**
     * test Modern リクエストを Modern と判定する
     */
    public function testDescribeModern()
    {
        $result = NegotiationLogger::describe([
            'method' => 'tools/call',
            'params' => [
                'name' => 'addBlogPost',
                '_meta' => [
                    'io.modelcontextprotocol/protocolVersion' => '2026-07-28',
                    'io.modelcontextprotocol/clientInfo' => ['name' => 'claude-ai', 'version' => '2.0.0'],
                ],
            ],
        ], '2026-07-28');

        $this->assertEquals('modern', $result['era']);
        $this->assertEquals('2026-07-28', $result['protocolVersion']);
        $this->assertEquals('claude-ai', $result['clientName']);
        $this->assertEquals('2.0.0', $result['clientVersion']);
        $this->assertEquals('tools/call', $result['method']);
    }

    /**
     * test Legacy の initialize を Legacy と判定する
     */
    public function testDescribeLegacy()
    {
        $result = NegotiationLogger::describe([
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => '2025-06-18',
                'clientInfo' => ['name' => 'legacy-client', 'version' => '1.0.0'],
            ],
        ], '');

        $this->assertEquals('legacy', $result['era']);
        $this->assertEquals('2025-06-18', $result['protocolVersion']);
        $this->assertEquals('legacy-client', $result['clientName']);
    }

    /**
     * test 引数の中身は記録対象に含まれない
     *
     * 機密情報がログに混入するのを防ぐ
     */
    public function testDescribeOmitsArguments()
    {
        $result = NegotiationLogger::describe([
            'method' => 'tools/call',
            'params' => [
                'name' => 'addBlogPost',
                'arguments' => ['title' => '秘密の記事'],
                '_meta' => ['io.modelcontextprotocol/protocolVersion' => '2026-07-28'],
            ],
        ], '2026-07-28');

        $this->assertStringNotContainsString('秘密の記事', json_encode($result, JSON_UNESCAPED_UNICODE));
        $this->assertArrayNotHasKey('arguments', $result);
    }

    /**
     * test readRecent が記録した接続状況を新しい順に読み出す
     *
     * 管理画面の「直近の接続状況」で使う
     */
    public function testReadRecent()
    {
        NegotiationLogger::log([
            'method' => 'tools/list',
            'params' => [
                '_meta' => [
                    'io.modelcontextprotocol/protocolVersion' => '2026-07-28',
                    'io.modelcontextprotocol/clientInfo' => ['name' => 'claude-ai', 'version' => '2.0.0'],
                ],
            ],
        ], '2026-07-28');

        $recent = NegotiationLogger::readRecent(10);

        $this->assertNotEmpty($recent);
        $this->assertEquals('modern', $recent[0]['era']);
        $this->assertEquals('2026-07-28', $recent[0]['protocolVersion']);
        $this->assertEquals('claude-ai', $recent[0]['clientName']);
        $this->assertNotEmpty($recent[0]['loggedAt']);
    }

    /**
     * test readRecent はログが無い場合に空配列を返す
     */
    public function testReadRecentWithoutLog()
    {
        $this->assertSame([], NegotiationLogger::readRecent(10, '/tmp/not_exists_mcp.log'));
    }

}
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/NegotiationLoggerTest.php 2>&1 | tail -20'`

Expected: FAIL。

- [ ] **Step 3: `NegotiationLogger` を実装する**

Create: `plugins/bc-mcp/src/Mcp/NegotiationLogger.php`

```php
<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Mcp;

use Cake\Log\Log;

/**
 * MCP のネゴシエーション内容を記録する
 *
 * クライアントがどの世代（Modern / Legacy）でどのプロトコルバージョンを
 * 要求してきたかを残す事で、クライアント側の移行を検知できるようにする。
 * 引数やトークンの中身は記録しない。
 */
class NegotiationLogger
{

    /**
     * 記録する内容を組み立てる
     *
     * Modern（2026-07-28 以降）はリクエストごとの _meta でバージョンを伝え、
     * Legacy は initialize の params でバージョンを伝える。
     *
     * @param array $mcpRequest MCP リクエスト
     * @param string $protocolVersionHeader MCP-Protocol-Version ヘッダの値
     * @return array
     */
    public static function describe(array $mcpRequest, string $protocolVersionHeader): array
    {
        $meta = $mcpRequest['params']['_meta'] ?? [];
        $isModern = isset($meta['io.modelcontextprotocol/protocolVersion']);

        if ($isModern) {
            $protocolVersion = $meta['io.modelcontextprotocol/protocolVersion'];
            $clientInfo = $meta['io.modelcontextprotocol/clientInfo'] ?? [];
        } else {
            $protocolVersion = $mcpRequest['params']['protocolVersion'] ?? $protocolVersionHeader;
            $clientInfo = $mcpRequest['params']['clientInfo'] ?? [];
        }

        return [
            'era' => $isModern? 'modern' : 'legacy',
            'protocolVersion' => (string)$protocolVersion,
            'clientName' => (string)($clientInfo['name'] ?? ''),
            'clientVersion' => (string)($clientInfo['version'] ?? ''),
            'method' => (string)($mcpRequest['method'] ?? ''),
        ];
    }

    /**
     * ネゴシエーション内容をログに記録する
     *
     * @param array $mcpRequest MCP リクエスト
     * @param string $protocolVersionHeader MCP-Protocol-Version ヘッダの値
     * @return void
     */
    public static function log(array $mcpRequest, string $protocolVersionHeader): void
    {
        $info = self::describe($mcpRequest, $protocolVersionHeader);
        Log::write('info', sprintf(
            'MCP negotiation: era=%s protocolVersion=%s client=%s/%s method=%s',
            $info['era'],
            $info['protocolVersion'],
            $info['clientName'],
            $info['clientVersion'],
            $info['method']
        ), ['mcp']);
    }

    /**
     * 直近の接続状況をログから読み出す
     *
     * 管理画面で「クライアントがどの世代で接続しているか」を確認できるようにする。
     * 常駐プロセスが無くなり死活監視が不要になった代わりに、これが運用時の
     * 主要な確認手段になる。
     *
     * @param int $limit 取得件数
     * @param string|null $logFile ログファイルのパス（テスト用）
     * @return array 新しい順の接続状況
     */
    public static function readRecent(int $limit = 10, ?string $logFile = null): array
    {
        $logFile ??= LOGS . 'mcp.log';
        if (!is_readable($logFile)) {
            return [];
        }

        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return [];
        }

        $result = [];
        foreach(array_reverse($lines) as $line) {
            if (!str_contains($line, 'MCP negotiation:')) {
                continue;
            }
            $pattern = '/^(?<loggedAt>[\d\-]+ [\d:]+).*MCP negotiation: era=(?<era>\S+) '
                . 'protocolVersion=(?<protocolVersion>\S*) client=(?<clientName>[^\/]*)\/(?<clientVersion>\S*) '
                . 'method=(?<method>\S*)$/';
            if (!preg_match($pattern, $line, $matches)) {
                continue;
            }
            $result[] = [
                'loggedAt' => $matches['loggedAt'],
                'era' => $matches['era'],
                'protocolVersion' => $matches['protocolVersion'],
                'clientName' => $matches['clientName'],
                'clientVersion' => $matches['clientVersion'],
                'method' => $matches['method'],
            ];
            if (count($result) >= $limit) {
                break;
            }
        }
        return $result;
    }

}
```

ログの行頭フォーマット（`2026-08-12 14:58:44 info: …`）は既存の `logs/mcp.log` で確認できる。正規表現が合わない場合は実際の出力に合わせて調整する。

- [ ] **Step 4: プロキシで有効化する**

Modify: `plugins/bc-mcp/src/Controller/McpProxyController.php`

Task 6 Step 4 でコメントアウトしていた `NegotiationLogger::log()` の行を有効化する。

- [ ] **Step 5: テストを実行して通ることを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/NegotiationLoggerTest.php plugins/bc-mcp/tests/TestCase/Controller/McpProxyControllerTest.php 2>&1 | tail -25'`

Expected: PASS。

- [ ] **Step 6: コミット**

```bash
git add plugins/bc-mcp/src/Mcp/NegotiationLogger.php plugins/bc-mcp/src/Controller/McpProxyController.php plugins/bc-mcp/tests/TestCase/Mcp/NegotiationLoggerTest.php
git commit -m "MCP ネゴシエーション内容のロギングを追加"
```

---

### Task 12: プラグイン全体テストとフルスイートでの回帰確認

**Files:**
- Modify: 必要に応じて既存テスト
- Modify: `docs/superpowers/specs/2026-08-12-bc-mcp-sdk-migration-design.md`（完了条件の確認結果）

**Interfaces:**
- Consumes: Task 1〜11 のすべて
- Produces: なし

- [ ] **Step 1: bc-mcp のテストをすべて実行する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage --testsuite BcMcp 2>&1 | tail -40'`

Expected: PASS。失敗があれば個別に `--filter` で切り分けて修正する。

- [ ] **Step 2: 旧 SDK と常駐プロセスへの参照が残っていないことを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && grep -rn "PhpMcp\|php-mcp\|McpServerManger\|isServerRunning" plugins/bc-mcp/ composer.json 2>&1'`

Expected: 出力なし。

- [ ] **Step 3: フルスイートを実行して回帰がないことを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage > /tmp/phpunit_full.log 2>&1; tail -45 /tmp/phpunit_full.log'`

Expected: 移植前と同じかそれ以上の結果。新規の失敗があれば根本原因単位で集計して切り分ける。

Run（失敗が多い場合）: `docker exec basercms sh -c 'cd /var/www/html && grep -hoE "[A-Za-z\\\\]+Exception: .{0,80}|[A-Za-z\\\\]+Error: .{0,80}" /tmp/phpunit_full.log | sed -E "s/[0-9]+/N/g" | sort | uniq -c | sort -rn | head -30'`

- [ ] **Step 4: stdio 起動を手動で確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && echo "" | timeout 5 bin/cake bc_mcp.server 2>&1 | head -10'`

Expected: 例外が発生しないこと。`timeout` による終了は正常。

- [ ] **Step 5: 設計書の完了条件を照合する**

設計書 第12章の完了条件を1つずつ確認し、達成状況を追記する。

- Modern と Legacy の両世代で `tools/list` → `tools/call` が通る（`DualEraTest` / `McpServerToolCallTest`）
- 常駐プロセスを起動しなくても `/bc-mcp` が応答する（`McpProxyControllerTest`）
- 既存の bc-mcp テストがすべて通り、フルスイートに回帰がない（Step 1・Step 3）
- `logs/mcp.log` から接続クライアントの世代とプロトコルバージョンが判別できる（`NegotiationLoggerTest`）
- 許可外 `Origin` からのリクエストが 403 で拒否される（`McpProxyControllerTest`）
- 認可レスポンスに `iss` が含まれ、メタデータの `issuer` と一致する（`OAuth2UtilTest`）
- `vendor/php-mcp` への依存が残っていない（Step 2）
- `McpServerManger` と管理画面の起動/停止 UI が削除されている（Step 2）

- [ ] **Step 6: コミット**

```bash
git add docs/superpowers/specs/2026-08-12-bc-mcp-sdk-migration-design.md
git commit -m "MCP 2026-07-28 対応の完了条件を確認して設計書に記録"
```

---

### Task 13: 固定ページ（Pages）ツールの追加

bc-mcp には固定ページを操作するツールが無いため新規に追加する。SDK 移植の完了後に着手することで、新 SDK の作法で最初から書ける。

**Files:**
- Create: `plugins/bc-mcp/src/Mcp/BaserCore/PagesTool.php`
- Modify: `plugins/bc-mcp/src/Mcp/BaserCore/BaserCoreServer.php`
- Test: `plugins/bc-mcp/tests/TestCase/Mcp/BaserCore/PagesToolTest.php`

**Interfaces:**
- Consumes: `BaseMcpTool::registerTools()` / `resolveLoginUserId()` / `executeWithErrorHandling()`（Task 3）、`McpTestTrait::callMcpTool()`（Task 2）
- Produces:
  - `BcMcp\Mcp\BaserCore\PagesTool::registerTools(\Mcp\Server\McpServer $server): \Mcp\Server\McpServer`
  - `PagesTool::getPages()` / `getPage()` / `addPage()` / `editPage()` / `deletePage()`
  - `PagesTool::getPermissionUrl($action, $args = [])`（static）

**データ構造の注意点**

固定ページは `pages` と `contents` の複合構造で、名前が紛らわしい。

| 保存先 | 意味 |
|---|---|
| `pages.contents` | **ページ本文**（HTML） |
| `pages.content`（`Contents` アソシエーション） | **コンテンツ情報**（タイトル・URL・公開状態・親フォルダ） |

`PagesService::create()` に渡す構造（`baser-core` の `PagesControllerTest::testAdd()` で確認済み）。

```php
[
    'contents' => '<p>本文</p>',
    'page_template' => '',
    'content' => [
        'title' => 'ページタイトル',
        'name' => 'about',
        'parent_id' => 1,
        'site_id' => 1,
        'plugin' => 'BaserCore',
        'type' => 'Page',
        'self_status' => true,
    ],
]
```

`plugin` = `'BaserCore'`、`type` = `'Page'` は固定値であり、**ツール側で自動的に補う**（AI クライアントに指定させない）。

- [ ] **Step 1: 失敗するテストを書く**

Create: `plugins/bc-mcp/tests/TestCase/Mcp/BaserCore/PagesToolTest.php`

```php
<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Mcp\BaserCore;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMcp\Mcp\BaserCore\PagesTool;
use BcMcp\Mcp\McpContext;
use BcMcp\Test\TestSuite\McpTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PagesToolTest
 */
class PagesToolTest extends BcTestCase
{

    use ScenarioAwareTrait;
    use McpTestTrait;

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        McpContext::clear();
        parent::tearDown();
    }

    /**
     * test addPage で固定ページが登録できる
     */
    public function testAddPage()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        McpContext::setLoginUserId(1);

        [$result, $isError] = $this->callMcpTool('addPage', [
            'title' => '会社概要',
            'name' => 'about',
            'content' => '<p>会社概要のページです。</p>',
            'status' => 1,
        ]);

        $this->assertFalse($isError, 'ツールの実行に失敗しました。' . (is_string($result)? $result : json_encode($result, JSON_UNESCAPED_UNICODE)));
        $this->assertArrayHasKey('id', $result, json_encode($result, JSON_UNESCAPED_UNICODE));
        // 本文は pages.contents に保存される
        $this->assertEquals('<p>会社概要のページです。</p>', $result['contents']);
        // タイトルと URL はコンテンツ情報に保存される
        $this->assertEquals('会社概要', $result['content']['title']);
        $this->assertEquals('about', $result['content']['name']);
        // plugin と type はツール側で補われる
        $this->assertEquals('BaserCore', $result['content']['plugin']);
        $this->assertEquals('Page', $result['content']['type']);
    }

    /**
     * test editPage で固定ページが編集できる
     */
    public function testEditPage()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        McpContext::setLoginUserId(1);

        [$added] = $this->callMcpTool('addPage', [
            'title' => '編集前',
            'name' => 'before-edit',
            'content' => '<p>編集前の本文</p>',
        ]);

        [$result, $isError] = $this->callMcpTool('editPage', [
            'id' => $added['id'],
            'title' => '編集後',
            'content' => '<p>編集後の本文</p>',
        ]);

        $this->assertFalse($isError, 'ツールの実行に失敗しました。' . (is_string($result)? $result : json_encode($result, JSON_UNESCAPED_UNICODE)));
        $this->assertEquals('編集後', $result['content']['title']);
        $this->assertEquals('<p>編集後の本文</p>', $result['contents']);
        // 指定しなかった項目は変更されない
        $this->assertEquals('before-edit', $result['content']['name']);
    }

    /**
     * test getPages と getPage で固定ページを取得できる
     */
    public function testGetPages()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        McpContext::setLoginUserId(1);

        [$added] = $this->callMcpTool('addPage', [
            'title' => '取得テスト',
            'name' => 'get-test',
            'content' => '<p>取得テストの本文</p>',
        ]);

        [$list, $listError] = $this->callMcpTool('getPages', ['limit' => 10]);
        $this->assertFalse($listError, is_string($list)? $list : json_encode($list, JSON_UNESCAPED_UNICODE));
        $this->assertNotEmpty($list);

        [$single, $singleError] = $this->callMcpTool('getPage', ['id' => $added['id']]);
        $this->assertFalse($singleError, is_string($single)? $single : json_encode($single, JSON_UNESCAPED_UNICODE));
        $this->assertEquals('取得テスト', $single['content']['title']);
    }

    /**
     * test deletePage で固定ページが削除できる
     */
    public function testDeletePage()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        McpContext::setLoginUserId(1);

        [$added] = $this->callMcpTool('addPage', [
            'title' => '削除対象',
            'name' => 'to-be-deleted',
            'content' => '<p>削除対象の本文</p>',
        ]);

        [$result, $isError] = $this->callMcpTool('deletePage', ['id' => $added['id']]);
        $this->assertFalse($isError, is_string($result)? $result : json_encode($result, JSON_UNESCAPED_UNICODE));

        [$notFound, $notFoundError] = $this->callMcpTool('getPage', ['id' => $added['id']]);
        $this->assertTrue($notFoundError, '削除したページが取得できてしまいました。');
    }

    /**
     * test 権限チェック用のURL
     */
    public function testGetPermissionUrl()
    {
        $this->assertEquals(
            ['POST' => '/baser-core/pages/add.json'],
            PagesTool::getPermissionUrl('addPage')
        );
        $this->assertEquals(
            ['POST' => '/baser-core/pages/edit/3.json'],
            PagesTool::getPermissionUrl('editPage', ['id' => 3])
        );
        $this->assertEquals(
            ['GET' => '/baser-core/pages/index.json'],
            PagesTool::getPermissionUrl('getPages')
        );
        // id が無い編集・削除は権限チェックの対象にできない
        $this->assertFalse(PagesTool::getPermissionUrl('editPage'));
    }

}
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/BaserCore/PagesToolTest.php 2>&1 | tail -20'`

Expected: FAIL（`BcMcp\Mcp\BaserCore\PagesTool` が存在しない）。

- [ ] **Step 3: `PagesTool` を実装する**

Create: `plugins/bc-mcp/src/Mcp/BaserCore/PagesTool.php`

既存の `BlogPostsTool` の構成（`registerTools()` → `getPermissionUrl()` → 各アクションメソッド）を踏襲する。`executeWithErrorHandling()` / `createSuccessResponse()` / `resolveLoginUserId()` は `BaseMcpTool` のものを使う。

`inputSchema` は次のとおり（`loginUserId` は公開しない）。

```php
    /**
     * 固定ページ関連のツールをサーバーに登録する
     *
     * @param \Mcp\Server\McpServer $server SDK のサーバー
     * @return \Mcp\Server\McpServer
     */
    public function registerTools(\Mcp\Server\McpServer $server): \Mcp\Server\McpServer
    {
        return $server
            ->tool(
                name: 'getPages',
                description: '固定ページの一覧を取得します',
                callback: [$this, 'getPages'],
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'keyword' => ['type' => 'string', 'description' => '検索キーワード（本文を対象に検索）'],
                        'siteId' => ['type' => 'number', 'description' => 'サイトID（省略時は全て）'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（0: 非公開, 1: 公開）（省略時は全て）'],
                        'limit' => ['type' => 'number', 'description' => '取得件数（省略時は10件）'],
                        'page' => ['type' => 'number', 'description' => 'ページ番号（省略時は1ページ目）'],
                    ]
                ]
            )
            ->tool(
                name: 'getPage',
                description: '指定されたIDの固定ページを取得します',
                callback: [$this, 'getPage'],
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => '固定ページID（必須）'],
                    ],
                    'required' => ['id']
                ]
            )
            ->tool(
                name: 'addPage',
                description: '固定ページを追加します',
                callback: [$this, 'addPage'],
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'title' => ['type' => 'string', 'description' => 'ページタイトル（必須）'],
                        'content' => ['type' => 'string', 'description' => 'ページ本文、マークダウン不可、HTML推奨'],
                        'name' => ['type' => 'string', 'description' => 'URLのスラッグ（省略時は自動採番）'],
                        'parentId' => ['type' => 'number', 'description' => '親フォルダのコンテンツID（省略時はサイトルート）'],
                        'siteId' => ['type' => 'number', 'description' => 'サイトID（省略時は1）'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（0: 非公開, 1: 公開）（省略時は0）'],
                        'description' => ['type' => 'string', 'description' => 'ページの説明'],
                        'publishBegin' => ['type' => 'string', 'format' => 'date-time', 'description' => '公開開始日時（省略時はなし）'],
                        'publishEnd' => ['type' => 'string', 'format' => 'date-time', 'description' => '公開終了日時（省略時はなし）'],
                        'pageTemplate' => ['type' => 'string', 'description' => 'ページテンプレート名（省略時はデフォルト）'],
                        'eyeCatch' => ['type' => 'string', 'description' => 'アイキャッチ画像。外部画像URLを直接指定'],
                    ],
                    'required' => ['title']
                ]
            )
            ->tool(
                name: 'editPage',
                description: '固定ページを編集します',
                callback: [$this, 'editPage'],
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => '固定ページID（必須）'],
                        'title' => ['type' => 'string', 'description' => 'ページタイトル'],
                        'content' => ['type' => 'string', 'description' => 'ページ本文、マークダウン不可、HTML推奨'],
                        'name' => ['type' => 'string', 'description' => 'URLのスラッグ'],
                        'parentId' => ['type' => 'number', 'description' => '親フォルダのコンテンツID'],
                        'status' => ['type' => 'number', 'description' => '公開ステータス（0: 非公開, 1: 公開）'],
                        'description' => ['type' => 'string', 'description' => 'ページの説明'],
                        'publishBegin' => ['type' => 'string', 'format' => 'date-time', 'description' => '公開開始日時'],
                        'publishEnd' => ['type' => 'string', 'format' => 'date-time', 'description' => '公開終了日時'],
                        'pageTemplate' => ['type' => 'string', 'description' => 'ページテンプレート名'],
                        'eyeCatch' => ['type' => 'string', 'description' => 'アイキャッチ画像。外部画像URLを直接指定'],
                    ],
                    'required' => ['id']
                ]
            )
            ->tool(
                name: 'deletePage',
                description: '固定ページを削除します',
                callback: [$this, 'deletePage'],
                inputSchema: [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'number', 'description' => '固定ページID（必須）'],
                    ],
                    'required' => ['id']
                ]
            );
    }
```

`getPermissionUrl()` は次のとおり。

```php
    /**
     * 権限チェック用のURLを取得する
     *
     * @param string $action アクション名
     * @param array $args 引数
     * @return array|false
     */
    public static function getPermissionUrl($action, $args = [])
    {
        switch ($action) {
            case 'addPage':
                return ['POST' => '/baser-core/pages/add.json'];
            case 'editPage':
                if (empty($args['id'])) return false;
                return ['POST' => "/baser-core/pages/edit/{$args['id']}.json"];
            case 'deletePage':
                if (empty($args['id'])) return false;
                return ['POST' => "/baser-core/pages/delete/{$args['id']}.json"];
            case 'getPages':
                return ['GET' => '/baser-core/pages/index.json'];
            case 'getPage':
                if (empty($args['id'])) return false;
                return ['GET' => "/baser-core/pages/view/{$args['id']}.json"];
            default:
                return false;
        }
    }
```

`addPage()` はフラットな引数を `PagesService::create()` の入れ子構造へ組み立てる。**引数の `$content`（本文）と保存先の `content`（コンテンツ情報）の対応関係をコメントで明示する。**

```php
    /**
     * 固定ページを追加する
     *
     * 固定ページは pages テーブルと contents テーブルの複合構造である点に注意する。
     * 引数の $content（ページ本文）は pages.contents へ、タイトルや URL などは
     * content キー（Contents アソシエーション）へ格納する。
     *
     * @param string $title ページタイトル
     * @param string|null $content ページ本文
     * @param string|null $name URLのスラッグ
     * @param int|null $parentId 親フォルダのコンテンツID
     * @param int|null $siteId サイトID
     * @param int|null $status 公開ステータス
     * @param string|null $description 説明
     * @param string|null $publishBegin 公開開始日時
     * @param string|null $publishEnd 公開終了日時
     * @param string|null $pageTemplate ページテンプレート
     * @param string|null $eyeCatch アイキャッチ画像
     * @param int|null $loginUserId ログインユーザーID
     * @return array
     */
    public function addPage(
        string $title,
        ?string $content = null,
        ?string $name = null,
        ?int $parentId = null,
        ?int $siteId = null,
        ?int $status = 0,
        ?string $description = null,
        ?string $publishBegin = null,
        ?string $publishEnd = null,
        ?string $pageTemplate = null,
        ?string $eyeCatch = null,
        ?int $loginUserId = null
    ): array
    {
        return $this->executeWithErrorHandling(function() use (
            $title, $content, $name, $parentId, $siteId, $status,
            $description, $publishBegin, $publishEnd, $pageTemplate, $eyeCatch, $loginUserId
        ) {
            /** @var \BaserCore\Service\PagesService $pagesService */
            $pagesService = $this->getService(PagesServiceInterface::class);

            $contentData = [
                'title' => $title,
                'plugin' => 'BaserCore',
                'type' => 'Page',
                'site_id' => $siteId ?? 1,
                'parent_id' => $parentId ?? $this->getSiteRootContentId($siteId ?? 1),
                'self_status' => (bool)$status,
            ];
            if ($name !== null) $contentData['name'] = $name;
            if ($description !== null) $contentData['description'] = $description;
            if ($publishBegin !== null) $contentData['publish_begin'] = $publishBegin;
            if ($publishEnd !== null) $contentData['publish_end'] = $publishEnd;
            if ($eyeCatch !== null) $contentData['eyecatch'] = $this->processImageUpload($eyeCatch);

            $postData = [
                // ページ本文は pages.contents
                'contents' => $content ?? '',
                'content' => $contentData,
            ];
            if ($pageTemplate !== null) $postData['page_template'] = $pageTemplate;

            $page = $pagesService->create($postData);

            return $this->createSuccessResponse(
                $page->toArray(),
                [],
                '固定ページ「' . $title . '」を追加しました。',
                $this->resolveLoginUserId($loginUserId)
            );
        });
    }
```

`getSiteRootContentId()` は親フォルダ未指定時にサイトルートのコンテンツIDを引くためのヘルパ。`ContentsService` または `Contents` テーブルの `site_root` フラグから取得する。取得方法は `baser-core` の `ContentsTable`／`ContentFoldersService` を確認して決める。

Run: `docker exec basercms sh -c 'cd /var/www/html && grep -rn "site_root" plugins/baser-core/src/Model/Table/ContentsTable.php | head -5'`

`editPage()` は `PagesService::get()` で対象を取得し、指定された項目のみを差分で `update()` に渡す。`deletePage()` は `PagesService::delete()` を呼ぶ。`getPages()` は `getIndex()`、`getPage()` は `get()` を使い、いずれも `content` を含めて返す（`contain` の指定が必要か確認する）。

- [ ] **Step 4: `BaserCoreServer` にツールクラスを登録する**

Modify: `plugins/bc-mcp/src/Mcp/BaserCore/BaserCoreServer.php`

`getToolClasses()` の配列に `PagesTool::class` を追加する。

- [ ] **Step 5: 構文チェック**

Run: `docker exec basercms sh -c 'cd /var/www/html && php -l plugins/bc-mcp/src/Mcp/BaserCore/PagesTool.php && php -l plugins/bc-mcp/src/Mcp/BaserCore/BaserCoreServer.php'`

Expected: `No syntax errors detected`。

- [ ] **Step 6: テストを実行して通ることを確認する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/BaserCore/PagesToolTest.php 2>&1 | tail -30'`

Expected: PASS（5テスト）。

- [ ] **Step 7: tools/list に固定ページツールが並ぶことを確認する**

Modify: `plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php`

`testToolsListContainsAllTools()` に assertion を追加する。

```php
        // BaserCore（固定ページ）
        $this->assertContains('getPages', $names);
        $this->assertContains('getPage', $names);
        $this->assertContains('addPage', $names);
        $this->assertContains('editPage', $names);
        $this->assertContains('deletePage', $names);
```

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php 2>&1 | tail -20'`

Expected: PASS。

- [ ] **Step 8: プラグイン全体のテストを実行する**

Run: `docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage --testsuite BcMcp 2>&1 | tail -30'`

Expected: PASS。

- [ ] **Step 9: コミット**

```bash
git add plugins/bc-mcp/src/Mcp/BaserCore/PagesTool.php plugins/bc-mcp/src/Mcp/BaserCore/BaserCoreServer.php plugins/bc-mcp/tests/TestCase/Mcp/BaserCore/PagesToolTest.php plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php
git commit -m "固定ページの取得・作成・編集・削除ツールを追加"
```
