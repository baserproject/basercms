# bc-mcp スコープ整理 実装計画

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** bc-mcp を「運営者向けの認証付き MCP サーバー」として定義し直し、スコープ外のコード（`search` / `fetch`、チャンクアップロード、stdio 経路）を削除したうえで、全ツールにツール注釈を宣言する。

**Architecture:** 既存の SDK ベース構成（`McpServer` がツールクラスを集めて登録、`McpProxyController` が認証と権限を担い `McpRequestHandler` がプロセス内で実行）は変えない。変更は「登録するツールの取捨選択」「`tool()` へ渡す注釈」「認証を通らない経路の削除」の3点に閉じる。

**Tech Stack:** PHP 8.1+ / CakePHP 5.2 / baserCMS 5.4 / logiscape/mcp-sdk-php v2 / PHPUnit 10.5

**Spec:** [docs/superpowers/specs/2026-08-17-bc-mcp-scope-design.md](../specs/2026-08-17-bc-mcp-scope-design.md)

## Global Constraints

- **実行環境**: ユニットテストは必ず Docker コンテナ `basercms` 上で実行する。baserCMS の配置先は `/var/www/html`。
- **テストコマンドの雛形**: `docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage <対象>"`
- **フルスイートと個別テストを並行実行しない**（テスト DB が競合する）。
- **公開ツール数は51のまま変えない。** 本計画で削除するのは、いずれも `BaserCoreServer::getToolClasses()` に登録されていないコードである。
- **コメント・説明は日本語で書く。** 既存コードのコメント密度と語り口に合わせる。
- **権限チェックは Admin Web API に委ねる**（原則2）。独自の権限判定を追加しない。
- 対象ブランチは `dev-agentic`。

---

## File Structure

| ファイル | 役割 | 変更 |
|---|---|---|
| `plugins/bc-mcp/src/Mcp/BaseMcpTool.php` | 全ツールの基底。注釈定数の置き場 | 定数追加、`processChunkFile()` 削除、コメント修正 |
| `plugins/bc-mcp/src/Mcp/BaserCore/BaserCoreServer.php` | BaserCore のツールクラス一覧 | コメントアウト行の削除 |
| `plugins/bc-mcp/src/Mcp/BaserCore/SearchIndexesTool.php` | `search` / `fetch` | **削除** |
| `plugins/bc-mcp/src/Mcp/BaserCore/FileUploadTool.php` | `sendFileChunk` | **削除** |
| `plugins/bc-mcp/src/Command/McpServerCommand.php` | stdio 起動コマンド | **削除** |
| `plugins/bc-mcp/src/BcMcpPlugin.php` | プラグイン定義 | `console()` の削除 |
| `plugins/bc-mcp/src/Mcp/McpServer.php` | サーバー組み立てと `serverInfo` | `runStdio()` 削除、`available_transports` 修正、`serverInfo` に注釈 |
| `plugins/bc-mcp/src/Mcp/**/*Tool.php`（11ファイル） | 各ツールの登録 | `tool()` に `annotations:` を追加（計50件） |
| `plugins/bc-mcp/src/Mcp/BcCustomContent/CustomEntriesTool.php` | カスタムエントリー | 上記に加え `keyword` を公開 |
| `plugins/bc-mcp/tests/TestCase/Mcp/AnnotationsTest.php` | 注釈の全数検証 | **新規作成** |
| `plugins/bc-mcp/README.md` | 利用者向け文書 | アップロードと Cloudflare の節を更新 |

タスクの並びは「削除 → 注釈 → 補完 → 文書」の順とする。削除を先に済ませることで、注釈を付けて回る対象が確定する。

---

## Task 1: stdio トランスポートの削除

認証・権限・Origin 検証のいずれも通らない経路を塞ぐ。設計書 3.2 に対応。

**Files:**
- Delete: `plugins/bc-mcp/src/Command/McpServerCommand.php`
- Delete: `plugins/bc-mcp/tests/TestCase/Command/McpServerCommandTest.php`
- Modify: `plugins/bc-mcp/src/BcMcpPlugin.php`（`console()` メソッド）
- Modify: `plugins/bc-mcp/src/Mcp/McpServer.php`（`runStdio()` と `available_transports`）
- Modify: `plugins/bc-mcp/src/Mcp/BaseMcpTool.php`（`resolveLoginUserId()` の docblock）
- Test: `plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php`

**Interfaces:**
- Consumes: なし（最初のタスク）
- Produces: `McpServer` から `runStdio()` が消える。以降のタスクは `McpServer::getServer()` と `McpRequestHandler` のみを使う。

- [ ] **Step 1: `available_transports` の期待値を変えるテストを書く**

`plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php` に既存の `serverInfo` 検証があれば修正し、無ければ以下を追加する。

```php
    /**
     * test serverInfo が提供するトランスポートは HTTP のみ
     *
     * 認証と権限を通らない stdio 経路は提供しない
     */
    public function testServerInfoReportsHttpOnly()
    {
        $result = (new McpServer())->serverInfo();

        $this->assertEquals(['http'], $result['available_transports']);
    }
```

- [ ] **Step 2: テストを実行して失敗を確認する**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage --filter testServerInfoReportsHttpOnly plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php"
```

期待: FAIL（`['stdio', 'http']` が返るため差分で落ちる）

- [ ] **Step 3: `McpServer` から stdio を取り除く**

`plugins/bc-mcp/src/Mcp/McpServer.php` の `runStdio()` メソッド全体（docblock を含む）を削除する。

```php
    /**
     * 標準入力からサーバーを起動する
     *
     * HTTP 経由の利用は /bc-mcp エンドポイントが担うため、常駐プロセスとしての
     * 起動は標準入出力のみを提供する。
     *
     * @return void
     */
    public function runStdio(): void
    {
        $this->server->runStdio();
    }
```

続いて `serverInfo()` 内の記述を変更する。

変更前:
```php
            'available_transports' => ['stdio', 'http'],
```

変更後:
```php
            'available_transports' => ['http'],
```

- [ ] **Step 4: テストを実行して成功を確認する**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage --filter testServerInfoReportsHttpOnly plugins/bc-mcp/tests/TestCase/Mcp/McpServerTest.php"
```

期待: PASS

- [ ] **Step 5: コマンドとそのテストを削除する**

```bash
git rm plugins/bc-mcp/src/Command/McpServerCommand.php
git rm plugins/bc-mcp/tests/TestCase/Command/McpServerCommandTest.php
```

- [ ] **Step 6: プラグインからコマンド登録を外す**

`plugins/bc-mcp/src/BcMcpPlugin.php` の `console()` メソッド全体を削除する。`Oauth2CleanupCommand` は CakePHP の自動探索で読み込まれるため、明示登録は不要になる。

削除する部分:
```php
    /**
     * Add commands for the plugin.
     *
     * @param \Cake\Console\CommandCollection $commands The command collection to update.
     * @return \Cake\Console\CommandCollection
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        // MCPサーバーコマンドを追加
        $commands->add('bc_mcp.server', \BcMcp\Command\McpServerCommand::class);
        $commands = parent::console($commands);
        return $commands;
    }
```

あわせて未使用になる `use Cake\Console\CommandCollection;` も削除する。

- [ ] **Step 7: 残った stdio への言及を直す**

`plugins/bc-mcp/src/Mcp/BaseMcpTool.php` の `resolveLoginUserId()` の docblock を修正する。

変更前:
```php
     * MCP のツールは JSON-RPC の引数しか受け取らないため、認証済みの操作者は
     * McpContext から取得する。引数で明示された場合はそれを優先する
     * （stdio 経由の利用など、コンテキストを持たない経路のため）。
```

変更後:
```php
     * MCP のツールは JSON-RPC の引数しか受け取らないため、認証済みの操作者は
     * McpContext から取得する。引数で明示された場合はそれを優先する
     * （テストなど、コンテキストを持たない経路のため）。
```

- [ ] **Step 8: `Oauth2CleanupCommand` が消えていないことを確認する**

```bash
docker exec basercms bash -c "cd /var/www/html && bin/cake" 2>&1 | grep -c "bc_mcp"
```

期待: `1`（`bc_mcp.oauth2_cleanup` のみが残り、`bc_mcp.server` は消えている）

実際のコマンド名が異なる場合は、`bin/cake` の出力を確認して `bc_mcp` で始まる行が `server` を含まないことを目視で確認する。

- [ ] **Step 9: bc-mcp のテストを通す**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests"
```

期待: PASS（削除したコマンドのテスト分だけ件数が減る）

- [ ] **Step 10: コミット**

```bash
git add -A plugins/bc-mcp
git commit -m "認証と権限を通らない stdio 経路を削除

PermissionManager を呼ぶのは McpProxyController のみで、bin/cake
bc_mcp.server は OAuth 認証・権限チェック・Origin 検証・ログイン
ユーザーの設定を全て素通りしていた。

管理画面のツール一覧は McpRequestHandler をプロセス内で呼ぶため
影響を受けない。

Co-Authored-By: Claude Opus 5 (1M context) <noreply@anthropic.com>"
```

---

## Task 2: スコープ外ツールとチャンクアップロードの削除

`search` / `fetch` / `sendFileChunk` の実装と、その受け取り側を削除する。設計書 3.1 と 3.3 に対応。

**Files:**
- Delete: `plugins/bc-mcp/src/Mcp/BaserCore/SearchIndexesTool.php`
- Delete: `plugins/bc-mcp/src/Mcp/BaserCore/FileUploadTool.php`
- Delete: `plugins/bc-mcp/tests/TestCase/Mcp/BaserCore/SearchIndexesToolTest.php`
- Delete: `plugins/bc-mcp/tests/TestCase/Mcp/BaserCore/FileUploadToolTest.php`
- Modify: `plugins/bc-mcp/src/Mcp/BaserCore/BaserCoreServer.php`
- Modify: `plugins/bc-mcp/src/Mcp/BaseMcpTool.php`（`processFileUpload()` と `processChunkFile()`）
- Test: `plugins/bc-mcp/tests/TestCase/Mcp/BcBlog/BlogPostsToolTest.php:716` 付近

**Interfaces:**
- Consumes: Task 1 完了後の状態
- Produces: `BaseMcpTool::processFileUpload(string $fileData, string $fieldName = 'file'): array|false` は `data:` URI と `http(s)` URL のみを受け付け、それ以外は `false` を返す。

- [ ] **Step 1: `processFileUpload` の新しい振る舞いをテストで表現する**

`plugins/bc-mcp/tests/TestCase/Mcp/BaseMcpToolTest.php` が無ければ新規作成する。既にあれば追記する。

```php
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
     * テスト用の具象クラス
     */
    private function createTool(): BaseMcpTool
    {
        return new class extends BaseMcpTool {
            public function registerTools(\Mcp\Server\McpServer $server): \Mcp\Server\McpServer
            {
                return $server;
            }

            /**
             * protected メソッドをテストから呼ぶための入口
             */
            public function callProcessFileUpload(string $fileData): array|false
            {
                return $this->processFileUpload($fileData);
            }
        };
    }

    /**
     * test URL でも data: URI でもない指定は受け付けない
     *
     * チャンクアップロードを廃止したため、ローカルのファイル名を渡す経路は無い
     */
    public function testProcessFileUploadRejectsBareFilename()
    {
        $this->assertFalse($this->createTool()->callProcessFileUpload('example.jpg'));
    }

}
```

- [ ] **Step 2: テストを実行して失敗を確認する**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage --filter testProcessFileUploadRejectsBareFilename plugins/bc-mcp/tests/TestCase/Mcp/BaseMcpToolTest.php"
```

期待: FAIL または例外。現在は `processChunkFile()` に入り「チャンクファイルが存在しません」の例外が `catch` されて `false` が返るため、**PASS してしまう可能性がある**。その場合は Step 3 の削除後に「例外を経由せず素直に `false` を返す」ことを確認する意味のテストとして扱い、Step 2 の期待を PASS に読み替えてよい。

- [ ] **Step 3: チャンク処理を削除する**

`plugins/bc-mcp/src/Mcp/BaseMcpTool.php` の `processFileUpload()` から3つ目の分岐を削除する。

変更前:
```php
            // URLの場合はダウンロードして処理
            if (preg_match('/^https?:\/\//', $fileData)) {
                return $this->processUrlFile($fileData);
            }

            if (!empty($fileData)) {
                return $this->processChunkFile($fileData);
            }

            throw new \Exception('不正なファイルデータ形式です: ' . $fileData);
```

変更後:
```php
            // URLの場合はダウンロードして処理
            if (preg_match('/^https?:\/\//', $fileData)) {
                return $this->processUrlFile($fileData);
            }

            throw new \Exception('不正なファイルデータ形式です: ' . $fileData);
```

続いて `processChunkFile()` メソッド全体（docblock を含む、`public function processChunkFile(string $fileData): array` から対応する閉じ括弧まで）を削除する。

あわせて `processFileUpload()` の docblock を実態に合わせる。

変更前:
```php
     * @param string $fileData ファイルパス、URL、またはbase64エンコードされたデータ
```

変更後:
```php
     * @param string $fileData 画像の URL、または data: URI 形式の base64 データ
```

- [ ] **Step 4: テストを実行して成功を確認する**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/BaseMcpToolTest.php"
```

期待: PASS

- [ ] **Step 5: ツールクラスとテストを削除する**

```bash
git rm plugins/bc-mcp/src/Mcp/BaserCore/SearchIndexesTool.php
git rm plugins/bc-mcp/src/Mcp/BaserCore/FileUploadTool.php
git rm plugins/bc-mcp/tests/TestCase/Mcp/BaserCore/SearchIndexesToolTest.php
git rm plugins/bc-mcp/tests/TestCase/Mcp/BaserCore/FileUploadToolTest.php
```

- [ ] **Step 6: `BaserCoreServer` からコメントアウト行を消す**

`plugins/bc-mcp/src/Mcp/BaserCore/BaserCoreServer.php` の `getToolClasses()` を書き換える。

変更前:
```php
        return [
            PagesTool::class,
            // SearchIndexesTool::class, // ChatGPTで動作しないため一旦、停止
            // FileUploadTool::class // AI側のメッセージ制限によりチャンクによるアップロードを実装したが、それでも、現実的でなかったため、一旦、停止
        ];
```

変更後:
```php
        return [
            PagesTool::class,
        ];
```

- [ ] **Step 7: チャンク前提のテストを URL 方式に置き換える**

`plugins/bc-mcp/tests/TestCase/Mcp/BcBlog/BlogPostsToolTest.php` の716行付近で `TMP . 'mcp_uploads/'` を参照している箇所を確認する。

```bash
grep -n "mcp_uploads" plugins/bc-mcp/tests/TestCase/Mcp/BcBlog/BlogPostsToolTest.php
```

該当するテストメソッドを、`data:` URI を渡す形に書き換える。1x1 の PNG を使う。

```php
        // チャンクアップロードは廃止したため、インラインの data: URI で検証する
        $pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
        $eyeCatch = 'data:image/png;base64,' . $pngBase64;
```

テストの意図が「チャンク経由でアップロードしたファイルがアイキャッチになる」ことであれば、意図ごと「`data:` URI で渡したデータがアイキャッチになる」に読み替える。テストメソッド名に `Chunk` が含まれる場合は `testAddBlogPostWithInlineEyeCatch` のように改名する。

- [ ] **Step 8: bc-mcp のテストを通す**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests"
```

期待: PASS

- [ ] **Step 9: 削除対象への参照が残っていないことを確認する**

```bash
grep -rn "processChunkFile\|SearchIndexesTool\|FileUploadTool\|mcp_uploads" plugins/bc-mcp/src plugins/bc-mcp/tests
```

期待: 出力が空。何か残っていれば、それが参照している側も合わせて直す。

登録ツール数が51のままであることは Task 3 で追加する `AnnotationsTest::testToolCount()` が検証する。

- [ ] **Step 10: コミット**

```bash
git add -A plugins/bc-mcp
git commit -m "スコープ外の search / fetch とチャンクアップロードを削除

search / fetch は一般ユーザー向けの検索インデックスを露出しており、
運営者向けという位置づけと客層が異なる。加えて単一ベンダー固有の
レスポンス形式を要求される。

チャンクアップロードは MCP の File Uploads WG でも仕様から外された
方式で、根本のボトルネックはホストがファイルの生バイトをサーバーへ
渡せない点にある。受け取り側の processChunkFile() も対で削除し、
アイキャッチは URL と data: URI の2方式に絞る。

Co-Authored-By: Claude Opus 5 (1M context) <noreply@anthropic.com>"
```

---

## Task 3: ツール注釈の基盤と全数検証テスト

先に「全ツールが注釈を持つ」ことを検証するテストを用意し、それを赤にしてから注釈を付けていく。設計書 4 に対応。

**Files:**
- Modify: `plugins/bc-mcp/src/Mcp/BaseMcpTool.php`（注釈定数の追加）
- Create: `plugins/bc-mcp/tests/TestCase/Mcp/AnnotationsTest.php`

**Interfaces:**
- Consumes: Task 2 完了後のツール構成（51件）
- Produces: `BaseMcpTool::ANNOTATION_READ` / `ANNOTATION_CREATE` / `ANNOTATION_UPDATE` / `ANNOTATION_DELETE`（いずれも `protected const`、`array` 型）。Task 4 以降の全ツールがこれを参照する。

- [ ] **Step 1: 注釈定数を追加する**

`plugins/bc-mcp/src/Mcp/BaseMcpTool.php` の `OUTPUT_SCHEMA` 定数の直後に追加する。

```php
    /**
     * 読み取り専用ツールの注釈
     *
     * クライアントが読み取りと書き込みを区別できるようにする。Claude の
     * Research はツール呼び出しに都度承認を挟まないため、区別できる情報を
     * 提供する意味がある。readOnlyHint が true のとき、destructiveHint と
     * idempotentHint は意味を持たないため宣言しない。
     */
    protected const ANNOTATION_READ = [
        'readOnlyHint' => true,
        'openWorldHint' => false,
    ];

    /**
     * 追加系ツールの注釈
     *
     * 追加のみで既存データを壊さない。同じ引数で繰り返すと重複が増えるため
     * 冪等ではない。
     */
    protected const ANNOTATION_CREATE = [
        'readOnlyHint' => false,
        'destructiveHint' => false,
        'idempotentHint' => false,
        'openWorldHint' => false,
    ];

    /**
     * 更新系ツールの注釈
     *
     * 既存データを上書きするため破壊的とみなす。同じ引数なら結果は同じ。
     */
    protected const ANNOTATION_UPDATE = [
        'readOnlyHint' => false,
        'destructiveHint' => true,
        'idempotentHint' => true,
        'openWorldHint' => false,
    ];

    /**
     * 削除系ツールの注釈
     *
     * 削除済みのものを再度削除しても結果は変わらない。
     */
    protected const ANNOTATION_DELETE = [
        'readOnlyHint' => false,
        'destructiveHint' => true,
        'idempotentHint' => true,
        'openWorldHint' => false,
    ];
```

- [ ] **Step 2: 全数検証テストを書く**

`plugins/bc-mcp/tests/TestCase/Mcp/AnnotationsTest.php` を新規作成する。

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
use BcMcp\Mcp\McpRequestHandler;
use Mcp\Server\Transport\Http\HttpMessage;

/**
 * AnnotationsTest
 *
 * 全ツールがツール注釈を宣言していることを検証する
 *
 * 注釈は接頭辞ごとに手で指定する方針のため、ツールを追加したときの
 * 付け忘れを検出する仕組みが要る。個別ツールごとではなく tools/list を
 * 走査して全数を確認する。
 */
class AnnotationsTest extends BcTestCase
{

    /**
     * 接頭辞ごとに期待する注釈
     *
     * @var array<string, array>
     */
    private const EXPECTED = [
        'get' => ['readOnlyHint' => true, 'openWorldHint' => false],
        'add' => ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => false, 'openWorldHint' => false],
        'edit' => ['readOnlyHint' => false, 'destructiveHint' => true, 'idempotentHint' => true, 'openWorldHint' => false],
        'delete' => ['readOnlyHint' => false, 'destructiveHint' => true, 'idempotentHint' => true, 'openWorldHint' => false],
    ];

    /**
     * tools/list の結果を取得する
     *
     * @return array
     */
    private function fetchTools(): array
    {
        $request = new HttpMessage(json_encode([
            'jsonrpc' => '2.0',
            'id' => 'annotations-test',
            'method' => 'tools/list',
            'params' => [
                '_meta' => [
                    'io.modelcontextprotocol/protocolVersion' => '2026-07-28',
                    'io.modelcontextprotocol/clientInfo' => ['name' => 'test', 'version' => '1.0.0'],
                    'io.modelcontextprotocol/clientCapabilities' => [],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE));
        $request->setMethod('POST');
        $request->setUri('/bc-mcp');
        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('Accept', 'application/json');
        $request->setHeader('MCP-Protocol-Version', '2026-07-28');
        $request->setHeader('Mcp-Method', 'tools/list');

        $response = (new McpRequestHandler())->handle($request);
        $decoded = json_decode((string)$response->getBody(), true);

        return $decoded['result']['tools'] ?? [];
    }

    /**
     * test 全ツールが接頭辞に応じた注釈を宣言している
     */
    public function testAllToolsDeclareAnnotations()
    {
        $tools = $this->fetchTools();
        $this->assertNotEmpty($tools, 'ツール一覧を取得できませんでした');

        foreach($tools as $tool) {
            $name = $tool['name'];

            // serverInfo は接頭辞を持たないが読み取り専用
            $prefix = ($name === 'serverInfo')? 'get' : null;
            foreach(array_keys(self::EXPECTED) as $candidate) {
                if (str_starts_with($name, $candidate)) {
                    $prefix = $candidate;
                    break;
                }
            }

            $this->assertNotNull($prefix, "ツール {$name} の接頭辞が想定外です。注釈の割り当てを決めてください。");
            $this->assertArrayHasKey('annotations', $tool, "ツール {$name} に注釈がありません");

            foreach(self::EXPECTED[$prefix] as $key => $expected) {
                $this->assertArrayHasKey($key, $tool['annotations'], "ツール {$name} の注釈に {$key} がありません");
                $this->assertSame($expected, $tool['annotations'][$key], "ツール {$name} の {$key} が想定と異なります");
            }
        }
    }

    /**
     * test 公開しているツールの数が変わっていない
     *
     * スコープ整理でツールを増減させていないことの確認
     */
    public function testToolCount()
    {
        $this->assertCount(51, $this->fetchTools());
    }

}
```

- [ ] **Step 3: テストを実行して失敗を確認する**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/AnnotationsTest.php"
```

期待: `testAllToolsDeclareAnnotations` が FAIL（「ツール getPages に注釈がありません」など）。`testToolCount` は PASS。

`testToolCount` が失敗する場合は Task 2 の削除で登録ツールを誤って減らしている。差分を確認して直す。

- [ ] **Step 4: コミット**

この時点では赤いテストが1件残る。Task 4 以降で解消するため、テストを一時的に無効化せずそのままコミットする。

```bash
git add plugins/bc-mcp/src/Mcp/BaseMcpTool.php plugins/bc-mcp/tests/TestCase/Mcp/AnnotationsTest.php
git commit -m "ツール注釈の定数と全数検証テストを追加

接頭辞から自動判定せず明示指定する方針のため、付け忘れを検出する
仕組みとして tools/list を全数走査するテストを用意する。

この時点では各ツールへの注釈付与が未了のためテストは失敗する。

Co-Authored-By: Claude Opus 5 (1M context) <noreply@anthropic.com>"
```

---

## Task 4: BaserCore と BcBlog のツールに注釈を付ける

**Files:**
- Modify: `plugins/bc-mcp/src/Mcp/BaserCore/PagesTool.php`（5件）
- Modify: `plugins/bc-mcp/src/Mcp/BcBlog/BlogPostsTool.php`（5件）
- Modify: `plugins/bc-mcp/src/Mcp/BcBlog/BlogContentsTool.php`（5件）
- Modify: `plugins/bc-mcp/src/Mcp/BcBlog/BlogCategoriesTool.php`（5件）
- Modify: `plugins/bc-mcp/src/Mcp/BcBlog/BlogTagsTool.php`（5件）
- Modify: `plugins/bc-mcp/src/Mcp/McpServer.php`（`serverInfo` 1件）
- Test: `plugins/bc-mcp/tests/TestCase/Mcp/AnnotationsTest.php`

**Interfaces:**
- Consumes: `BaseMcpTool::ANNOTATION_READ` / `ANNOTATION_CREATE` / `ANNOTATION_UPDATE` / `ANNOTATION_DELETE`（Task 3）
- Produces: なし（Task 5 と同じ作業を別ファイルに対して行う）

- [ ] **Step 1: 各 `tool()` 呼び出しに `annotations:` を追加する**

`outputSchema:` の直後に `annotations:` を置く。接頭辞と定数の対応は次のとおり。

| ツール名の接頭辞 | 指定する定数 |
|---|---|
| `get` | `self::ANNOTATION_READ` |
| `add` | `self::ANNOTATION_CREATE` |
| `edit` | `self::ANNOTATION_UPDATE` |
| `delete` | `self::ANNOTATION_DELETE` |

`PagesTool` での例。

変更前:
```php
                name: 'getPages',
                description: '固定ページの一覧を取得します',
                callback: [$this, 'getPages'],
                outputSchema: self::OUTPUT_SCHEMA,
                inputSchema: [
```

変更後:
```php
                name: 'getPages',
                description: '固定ページの一覧を取得します',
                callback: [$this, 'getPages'],
                outputSchema: self::OUTPUT_SCHEMA,
                annotations: self::ANNOTATION_READ,
                inputSchema: [
```

削除系の例。

変更前:
```php
                name: 'deletePage',
                description: '固定ページを削除します',
                callback: [$this, 'deletePage'],
                outputSchema: self::OUTPUT_SCHEMA,
```

変更後:
```php
                name: 'deletePage',
                description: '固定ページを削除します',
                callback: [$this, 'deletePage'],
                outputSchema: self::OUTPUT_SCHEMA,
                annotations: self::ANNOTATION_DELETE,
```

**引数の順序は問わない**（名前付き引数のため）が、既存の並びに合わせて `outputSchema` の直後に置くこと。

対象は次の25件。

- `PagesTool`: `getPages` / `getPage` / `addPage` / `editPage` / `deletePage`
- `BlogPostsTool`: `getBlogPosts` / `getBlogPost` / `addBlogPost` / `editBlogPost` / `deleteBlogPost`
- `BlogContentsTool`: `getBlogContents` / `getBlogContent` / `addBlogContent` / `editBlogContent` / `deleteBlogContent`
- `BlogCategoriesTool`: `getBlogCategories` / `getBlogCategory` / `addBlogCategory` / `editBlogCategory` / `deleteBlogCategory`
- `BlogTagsTool`: `getBlogTags` / `getBlogTag` / `addBlogTag` / `editBlogTag` / `deleteBlogTag`

- [ ] **Step 2: `serverInfo` に注釈を付ける**

`plugins/bc-mcp/src/Mcp/McpServer.php` の `serverInfo` 登録に追加する。`McpServer` は `BaseMcpTool` を継承していないため、定数を参照できない。配列を直接書く。

変更前:
```php
        $this->server->tool(
            name: 'serverInfo',
            description: 'サーバーのバージョンや環境情報を返します',
            callback: [$this, 'serverInfo'],
            outputSchema: [
```

変更後:
```php
        $this->server->tool(
            name: 'serverInfo',
            description: 'サーバーのバージョンや環境情報を返します',
            callback: [$this, 'serverInfo'],
            // BaseMcpTool を継承していないため定数を参照できない。
            // ANNOTATION_READ と同じ内容を直接指定する。
            annotations: ['readOnlyHint' => true, 'openWorldHint' => false],
            outputSchema: [
```

- [ ] **Step 3: テストを実行して進捗を確認する**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/AnnotationsTest.php"
```

期待: まだ FAIL。ただしエラーメッセージが BcCustomContent 系のツール名（`getCustomContents` など）に変わっていること。BaserCore / BcBlog 系の名前が出なくなっていれば本タスクは成功している。

- [ ] **Step 4: 既存テストが壊れていないことを確認する**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/BaserCore plugins/bc-mcp/tests/TestCase/Mcp/BcBlog"
```

期待: PASS

- [ ] **Step 5: コミット**

```bash
git add plugins/bc-mcp/src/Mcp/BaserCore plugins/bc-mcp/src/Mcp/BcBlog plugins/bc-mcp/src/Mcp/McpServer.php
git commit -m "固定ページ・ブログ・serverInfo のツールに注釈を宣言

Co-Authored-By: Claude Opus 5 (1M context) <noreply@anthropic.com>"
```

---

## Task 5: BcCustomContent のツールに注釈を付け、`keyword` を公開する

**Files:**
- Modify: `plugins/bc-mcp/src/Mcp/BcCustomContent/CustomContentsTool.php`（5件）
- Modify: `plugins/bc-mcp/src/Mcp/BcCustomContent/CustomEntriesTool.php`（5件 + `keyword`）
- Modify: `plugins/bc-mcp/src/Mcp/BcCustomContent/CustomFieldsTool.php`（5件）
- Modify: `plugins/bc-mcp/src/Mcp/BcCustomContent/CustomTablesTool.php`（5件）
- Modify: `plugins/bc-mcp/src/Mcp/BcCustomContent/CustomLinksTool.php`（5件）
- Test: `plugins/bc-mcp/tests/TestCase/Mcp/BcCustomContent/CustomEntriesToolTest.php`

**Interfaces:**
- Consumes: `BaseMcpTool::ANNOTATION_*`（Task 3）
- Produces: `CustomEntriesTool::getCustomEntries()` の第2引数が `?string $keyword` になる（旧 `?string $title`）。

- [ ] **Step 1: 注釈を追加する**

Task 4 の Step 1 と同じ要領で、次の25件に `annotations:` を追加する。接頭辞と定数の対応も同じ。

- `CustomContentsTool`: `getCustomContents` / `getCustomContent` / `addCustomContent` / `editCustomContent` / `deleteCustomContent`
- `CustomEntriesTool`: `getCustomEntries` / `getCustomEntry` / `addCustomEntry` / `editCustomEntry` / `deleteCustomEntry`
- `CustomFieldsTool`: `getCustomFields` / `getCustomField` / `addCustomField` / `editCustomField` / `deleteCustomField`
- `CustomTablesTool`: `getCustomTables` / `getCustomTable` / `addCustomTable` / `editCustomTable` / `deleteCustomTable`
- `CustomLinksTool`: `getCustomLinks` / `getCustomLink` / `addCustomLink` / `editCustomLink` / `deleteCustomLink`

`CustomEntriesTool` は `callback:` が先頭に来る書式なので、それに合わせる。

変更前:
```php
            ->tool(
                callback: [$this, 'getCustomEntries'],
                outputSchema: self::OUTPUT_SCHEMA,
                name: 'getCustomEntries',
```

変更後:
```php
            ->tool(
                callback: [$this, 'getCustomEntries'],
                outputSchema: self::OUTPUT_SCHEMA,
                annotations: self::ANNOTATION_READ,
                name: 'getCustomEntries',
```

- [ ] **Step 2: 注釈のテストが全て通ることを確認する**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/AnnotationsTest.php"
```

期待: PASS（Task 3 で赤くしたテストがここで緑になる）

- [ ] **Step 3: `keyword` の失敗するテストを書く**

`plugins/bc-mcp/tests/TestCase/Mcp/BcCustomContent/CustomEntriesToolTest.php` に追加する。このテストクラスは MCP 経由ではなく `$this->CustomEntriesTool` のメソッドを直接呼ぶ書式なので、それに倣う（`testAddCustomEntryBasic` を参照）。

```php
    /**
     * Test getCustomEntries method - キーワード絞り込みテスト
     *
     * 他の一覧ツールと同じく keyword で指定できる。
     * 対象はタイトルとスラッグ（CustomEntriesService の title 条件）。
     *
     * @return void
     */
    public function testGetCustomEntriesWithKeyword()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTablesService = $this->getService(CustomTablesServiceInterface::class);

        $this->loadFixtureScenario(CustomFieldsScenario::class);

        $customTablesService->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);

        $this->CustomEntriesTool->addCustomEntry(
            customTableId: 1,
            title: '検索対象のエントリー',
            name: 'keyword_target',
            status: true,
            creatorId: 1
        );
        $this->CustomEntriesTool->addCustomEntry(
            customTableId: 1,
            title: '関係のないエントリー',
            name: 'keyword_other',
            status: true,
            creatorId: 1
        );

        $result = $this->CustomEntriesTool->getCustomEntries(
            customTableId: 1,
            keyword: '検索対象'
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result['results'], json_encode($result, JSON_UNESCAPED_UNICODE));
        $this->assertEquals('検索対象のエントリー', $result['results'][0]['title']);

        $dataBaseService->dropTable('custom_entry_1_contact');
    }
```

`addCustomEntry` が失敗して結果が0件になる場合は、`CustomFieldsScenario` が用意するフィールド構成と `title` / `name` の必須条件を確認する。既存の `testAddCustomEntryBasic` が緑であれば同じ手順で作成できる。

- [ ] **Step 4: テストを実行して失敗を確認する**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage --filter testGetCustomEntriesWithKeyword plugins/bc-mcp/tests/TestCase/Mcp/BcCustomContent/CustomEntriesToolTest.php"
```

期待: FAIL（`keyword` が inputSchema に無いため無視され、絞り込まれない）

- [ ] **Step 5: `keyword` を公開する**

`CustomEntriesTool::getCustomEntries()` の第2引数を改名する。

変更前:
```php
    public function getCustomEntries(
        int $customTableId,
        ?string $title = null,
        ?int $creatorId = null,
```

変更後:
```php
    public function getCustomEntries(
        int $customTableId,
        ?string $keyword = null,
        ?int $creatorId = null,
```

メソッド本体の `use` と条件生成も合わせる。`CustomEntriesService` 側の条件キーは `title` のままである点に注意する（サービスの API は変えない）。

変更前:
```php
        return $this->executeWithErrorHandling(function() use ($customTableId, $title, $creatorId, $published, $limit, $page, $status) {
```

変更後:
```php
        return $this->executeWithErrorHandling(function() use ($customTableId, $keyword, $creatorId, $published, $limit, $page, $status) {
```

変更前:
```php
            if (!is_null($title)) $conditions['title'] = $title;
```

変更後:
```php
            // CustomEntriesService の title 条件はタイトルとスラッグの LIKE 検索
            if (!is_null($keyword)) $conditions['title'] = $keyword;
```

inputSchema に追加する。

変更前:
```php
                    'properties' => [
                        'customTableId' => ['type' => 'number', 'description' => 'カスタムテーブルID（必須）'],
                        'limit' => ['type' => 'number', 'default' => 20, 'description' => '取得件数（デフォルト: 20）'],
```

変更後:
```php
                    'properties' => [
                        'customTableId' => ['type' => 'number', 'description' => 'カスタムテーブルID（必須）'],
                        'keyword' => ['type' => 'string', 'description' => '検索キーワード（タイトル・スラッグを対象に検索）'],
                        'limit' => ['type' => 'number', 'default' => 20, 'description' => '取得件数（デフォルト: 20）'],
```

- [ ] **Step 6: テストを実行して成功を確認する**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests/TestCase/Mcp/BcCustomContent/CustomEntriesToolTest.php"
```

期待: PASS

- [ ] **Step 7: bc-mcp のテストを通す**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/bc-mcp/tests"
```

期待: PASS

- [ ] **Step 8: コミット**

```bash
git add plugins/bc-mcp/src/Mcp/BcCustomContent plugins/bc-mcp/tests/TestCase/Mcp/BcCustomContent
git commit -m "カスタムコンテンツのツールに注釈を宣言し、keyword を公開

getCustomEntries はタイトル・スラッグの絞り込みを実装として持ちながら
inputSchema に宣言しておらず、クライアントから指定できなかった。
横断検索を持たない方針のため、各一覧ツールの絞り込みは揃っている
必要がある。

Co-Authored-By: Claude Opus 5 (1M context) <noreply@anthropic.com>"
```

---

## Task 6: README の更新

設計書 6.2 に対応。

**Files:**
- Modify: `plugins/bc-mcp/README.md`

**Interfaces:**
- Consumes: Task 1〜5 の完了状態
- Produces: なし（最終タスク）

- [ ] **Step 1: ファイルアップロードの節を実態に合わせる**

「ファイルアップロードについて」の節を次に置き換える。

変更前:
```markdown
### 現状の対応方法
現状としてはSTDIO方式のアップロードツールで、BcMcpが参照可能な領域にアップロードして、そのURLを送信するしかありません。

### 将来的な対応予定
将来的には、MPCの仕様として multipart/form-data に対応する予定との事ですので、その際にBcMcpも対応する予定です。
```

変更後:
```markdown
### 現状の対応方法
アイキャッチなどの画像は、次の2つの方法で指定できます。

- **画像のURL** — ネット上に公開された画像のURLを渡します
- **`data:` URI** — `data:image/png;base64,...` 形式で直接渡します。小さな画像に限ります

### 将来的な対応予定
MCP に [File Uploads Working Group](https://modelcontextprotocol.io/community/working-groups/file-uploads)
が設置され、ホストがファイルピッカーを表示してサーバーへファイルを渡す仕組みが検討されています
（[SEP-2631](https://github.com/modelcontextprotocol/modelcontextprotocol/pull/2631)）。

現状はホスト側にファイルの中身をサーバーへ渡す手段が無いため、ローカルのファイルを
そのままアップロードすることはできません。規格とホストの対応が揃った段階で BcMcp も対応します。
```

「制約事項」の節にある「約30KB以下でチャンク分割送信」の記述は、方式ごと廃止したため削除する。

- [ ] **Step 2: Cloudflare の節に制約と Named Tunnel を追記する**

「2. トンネルの起動」の直後に追記する。

```markdown
Quick Tunnel には次の制約があります。

| 項目 | 内容 |
|---|---|
| 同時リクエスト | 200 in-flight まで |
| SSE（Server-Sent Events） | **非対応** |
| URL | 再起動のたびに変わる |
| 用途 | テストと開発のみ（本番非推奨、SLAなし） |

固定のホスト名が必要な場合は Named Tunnel を使います。Cloudflare アカウントと、
Cloudflare に登録済みのドメインが必要です。

```bash
cloudflared tunnel login
cloudflared tunnel create bc-mcp-verify
cloudflared tunnel route dns bc-mcp-verify mcp-dev.example.com
```

固定ホスト名にすると、OAuth の動的クライアント登録・`SITE_URL`・コネクタ登録を
毎回やり直す必要がなくなります。
```

- [ ] **Step 3: 記述と実装が一致していることを確認する**

```bash
grep -n -i "stdio\|チャンク\|sendFileChunk\|search\|fetch" plugins/bc-mcp/README.md
```

期待: bc-mcp が提供しない機能への言及が残っていないこと。「利用可能なツール」の一覧に `search` / `fetch` / `sendFileChunk` が含まれていないこと。

- [ ] **Step 4: フルスイートを実行する**

他プラグインへの影響が無いことを確認する。**個別テストと同時に実行しないこと。**

```bash
docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage"
```

期待: PASS（失敗0件）

- [ ] **Step 5: コミット**

```bash
git add plugins/bc-mcp/README.md
git commit -m "README をスコープ整理後の実態に合わせる

ファイルアップロードは URL と data: URI の2方式であることを明記し、
廃止したチャンク方式の記述を削除する。Cloudflare Quick Tunnel の
制約と、固定ホスト名が必要な場合の Named Tunnel を追記する。

Co-Authored-By: Claude Opus 5 (1M context) <noreply@anthropic.com>"
```

---

## 完了条件

- `docker exec basercms bash -c "cd /var/www/html && vendor/bin/phpunit --no-coverage"` が失敗0件
- `AnnotationsTest` が緑（全51ツールが接頭辞に応じた注釈を持つ）
- `bin/cake` の一覧に `bc_mcp.server` が存在しない
- `grep -rn "processChunkFile\|SearchIndexesTool\|FileUploadTool" plugins/bc-mcp/src` が空
- README に bc-mcp が提供しない機能の記述が残っていない
