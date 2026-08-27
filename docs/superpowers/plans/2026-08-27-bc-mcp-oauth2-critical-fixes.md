# bc-mcp OAuth2 重大脆弱性の修正 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** bc-mcp の OAuth2 認可フローから「第三者が正規の認可を横取りできる経路」「第三者が自力でトークンを取得できる経路」「既知のハードコード鍵で動作する経路」を排除する。

**Architecture:** 同意エンドポイントを CSRF 保護下に戻し、認可リクエストの検証を GET 時に済ませてセッションに保持、POST は保持済みリクエストのみで完了させる。暗号化キーは必須化し、未設定時は OAuth2/MCP エンドポイントだけを 503 で停止する。動的クライアント登録は開けたまま維持し、authorization_code + PKCE(S256) のパブリッククライアントに限定する。

**Tech Stack:** PHP 8.5 / CakePHP 5 / baserCMS 5.4.x / league/oauth2-server 8.x / logiscape/mcp-sdk-php 2.x / PHPUnit 10.5

**Spec:** `docs/superpowers/specs/2026-08-27-bc-mcp-oauth2-critical-fixes-design.md`

## Global Constraints

- 対象プラグインは `plugins/bc-mcp` のみ。`plugins/baser-core` を含む他プラグインとコア（`vendor/`）は変更しない。
- bc-mcp は未リリースのため後方互換は考慮しない。既存の登録済みクライアント・発行済みトークンの無効化は許容する。
- テストは Docker コンテナで実行する。コマンドは `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --no-coverage`。個別実行は `--filter` を併用する。
- 着手前のベースラインは **215 tests / 1093 assertions / OK**。各タスクの完了時点でスイート全体がグリーンであること。
- コメント・メッセージ・コミットログは日本語で書く。既存コードのコメント密度と口調（「〜する」「〜のため」調の説明コメント）に合わせる。
- シェルコマンドはパイプや複合コマンドを避け、単一コマンドで実行する。
- `redirect_uri` の許容範囲は「`https` 全般」＋「`http` はホストが `127.0.0.1` / `[::1]` / `localhost` の場合のみ」。
- レート制限の閾値は「同一 IP から1時間あたり10件」、未使用クライアントの保持期間は「30日」。いずれも `config/setting.php` の `BcMcp.registration` 配下で変更可能にする。
- CSRF 対策は `CsrfProtectionMiddleware` に一本化する。`FormProtection` の `validate` は無効のままとする（POST がボディを読まない設計のため、フィールド改竄検証は意味を持たない）。
- パブリッククライアント一本化により、認可リクエストには `code_challenge` と `code_challenge_method=S256` が必須になる。テストは全てこれを伴う形で書く。
- スコープ外（本計画では触らない）: SSRF 対策、スコープ強制、`loginUserId` 偽装、`client_secret`/`registration_access_token` の平文保存、例外メッセージの露出、JWKS の鍵不一致、デバッグログ、ファイルパーミッション、Host ヘッダ由来の issuer。

---

## File Structure

**変更するファイル**

| ファイル | 責務 | 変更内容 |
| --- | --- | --- |
| `config/setting.php` | プラグイン設定 | CSRF スキップ URL の個別列挙、レート制限と保持期間の設定値、Cache 設定 |
| `src/Controller/Admin/Oauth2Controller.php` | 同意画面と認可の完了 | GET 検証 → セッション保持 → POST 完了へ再構成 |
| `templates/Admin/Oauth2/authorize.php` | 同意画面 | セッション由来のスコープ表示、hidden 入力の削除 |
| `src/OAuth2/Service/OAuth2Service.php` | OAuth2 サーバーの組み立て | 暗号化キー必須化、client_credentials 削除 |
| `src/OAuth2/Grant/AuthCodeGrant.php` | 認可コードグラント | `code_challenge_method` を S256 に限定 |
| `src/OAuth2/Service/OAuth2ClientRegistrationService.php` | 動的クライアント登録 | メタデータ検証の厳格化、パブリッククライアント限定 |
| `src/Controller/Oauth2Controller.php` | 認証不要な OAuth2 エンドポイント | 設定不備時の 503、登録のレート制限 |
| `src/Controller/McpProxyController.php` | MCP リクエストの受け口 | 設定不備時の 503（JSON-RPC 形式） |
| `src/Lib/OAuth2Util.php` | PSR-7 変換 | client_credentials 専用分岐の削除 |
| `src/Controller/Admin/McpServerManagerController.php` | 管理画面 | 暗号化キー未設定の検知 |
| `templates/Admin/McpServerManager/index.php` | 管理画面 | 警告パネルの表示 |
| `src/BcMcpPlugin.php` | プラグイン定義 | putEnv 失敗の検知、Cache 設定の登録 |
| `src/Command/Oauth2CleanupCommand.php` | 掃除コマンド | 未使用クライアントの削除 |

**新規作成するファイル**

| ファイル | 責務 |
| --- | --- |
| `src/OAuth2/Exception/OAuth2ConfigurationException.php` | OAuth2 の設定不備を表す例外 |
| `src/OAuth2/Validator/RedirectUriRegistrationValidator.php` | 登録時の `redirect_uri` 検証（認可時の照合とは別責務） |
| `src/Service/RegistrationRateLimiter.php` | クライアント登録のレート制限 |
| `tests/TestCase/Controller/Admin/Oauth2ConsentFlowTest.php` | 同意フロー（CSRF・セッション・PKCE）のテスト |
| `tests/TestCase/OAuth2/Validator/RedirectUriRegistrationValidatorTest.php` | redirect_uri 検証の単体テスト |
| `tests/TestCase/Service/RegistrationRateLimiterTest.php` | レート制限の単体テスト |
| `tests/TestCase/Command/Oauth2CleanupCommandTest.php` | 掃除コマンドのテスト |

---

### Task 1: CSRF スキップ対象の個別列挙と同意フローのテスト基盤

**Files:**
- Modify: `plugins/bc-mcp/config/setting.php:26-38`
- Test: `plugins/bc-mcp/tests/TestCase/Controller/Admin/Oauth2ConsentFlowTest.php`（新規）

**Interfaces:**
- Consumes: なし（最初のタスク）
- Produces: `/bc-mcp/oauth2/authorize` が `CsrfProtectionMiddleware` の検証対象になる。テストクラス `Oauth2ConsentFlowTest` が以降のタスクの土台になり、`registerClient(array $override = []): array` / `codeChallenge(): string` / `authorizeQuery(string $clientId, array $override = []): string` の3つの private ヘルパを提供する。

- [ ] **Step 1: 失敗するテストを書く**

`plugins/bc-mcp/tests/TestCase/Controller/Admin/Oauth2ConsentFlowTest.php` を新規作成する。

```php
<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * 同意（authorize）フローのテスト
 *
 * CSRF 保護、セッション経由の認可リクエスト受け渡し、PKCE の必須化を検証する。
 */
class Oauth2ConsentFlowTest extends BcTestCase
{
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * PKCE の code_verifier
     *
     * RFC 7636 が要求する 43〜128 文字の範囲に収める。
     */
    private const CODE_VERIFIER = 'bc-mcp-test-code-verifier-0123456789012345678901234567';

    /**
     * 既定のリダイレクト先
     */
    private const REDIRECT_URI = 'https://example.com/callback';

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $_ENV['UNIT_TEST'] = true;
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->ensureOAuth2Keys();
    }

    /**
     * テスト用の RSA キーペアを用意する
     *
     * @return void
     */
    private function ensureOAuth2Keys(): void
    {
        $privateKeyPath = CONFIG . 'oauth2_private.key';
        $publicKeyPath = CONFIG . 'oauth2_public.key';
        if (file_exists($privateKeyPath) && file_exists($publicKeyPath)) {
            return;
        }
        $resource = openssl_pkey_new([
            'digest_alg' => 'sha256',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($resource, $privateKey);
        file_put_contents($privateKeyPath, $privateKey);
        file_put_contents($publicKeyPath, openssl_pkey_get_details($resource)['key']);
    }

    /**
     * クライアントを登録して登録レスポンスを返す
     *
     * @param array $override 上書きする登録メタデータ
     * @return array
     */
    private function registerClient(array $override = []): array
    {
        $this->post('/bc-mcp/oauth2/register', $override + [
            'client_name' => 'Consent Flow Test Client',
            'redirect_uris' => [self::REDIRECT_URI],
            'grant_types' => ['authorization_code', 'refresh_token'],
            'response_types' => ['code'],
            'token_endpoint_auth_method' => 'none',
            'scope' => 'mcp:read mcp:write',
        ]);
        $this->assertResponseCode(201);
        return json_decode((string)$this->_response->getBody(), true);
    }

    /**
     * code_verifier に対応する code_challenge を生成する
     *
     * @return string
     */
    private function codeChallenge(): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', self::CODE_VERIFIER, true)), '+/', '-_'), '=');
    }

    /**
     * 認可リクエストのクエリ文字列を組み立てる
     *
     * @param string $clientId クライアントID
     * @param array $override 上書きするパラメータ
     * @return string
     */
    private function authorizeQuery(string $clientId, array $override = []): string
    {
        return http_build_query($override + [
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => self::REDIRECT_URI,
            'scope' => 'mcp:read mcp:write',
            'code_challenge' => $this->codeChallenge(),
            'code_challenge_method' => 'S256',
        ]);
    }

    /**
     * CSRF トークンの無い同意 POST は拒否される
     *
     * @return void
     */
    public function testApproveWithoutCsrfTokenIsRejected(): void
    {
        $client = $this->registerClient();
        $this->loginAdmin($this->getRequest());

        $this->post(
            '/bc-mcp/oauth2/authorize?' . $this->authorizeQuery($client['client_id']),
            ['action' => 'approve']
        );

        $this->assertResponseCode(403);
    }

    /**
     * CSRF スキップ対象のエンドポイントはトークン無しでも通る
     *
     * @return void
     */
    public function testTokenEndpointRemainsCsrfExempt(): void
    {
        // grant_type が無いため OAuth2 のエラーになるが、CSRF で 403 にはならない
        $this->post('/bc-mcp/oauth2/token', []);
        $this->assertResponseCode(400);
    }
}
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter Oauth2ConsentFlowTest --no-coverage`

Expected: `testApproveWithoutCsrfTokenIsRejected` が FAIL（403 ではなく 302 が返る。現状 CSRF がスキップされ認可が成立してしまうため）。`testTokenEndpointRemainsCsrfExempt` は PASS。

- [ ] **Step 3: skipCsrfUrl を個別列挙に変更する**

`plugins/bc-mcp/config/setting.php` の `skipCsrfUrl` を差し替える。

```php
        /**
         * CSRFチェックをスキップするURL
         *
         * Cookie を使わないエンドポイントのみを列挙する。ワイルドカードで
         * /bc-mcp/oauth2/* を一括指定すると、管理画面セッションで動作する
         * 同意エンドポイント（authorize）まで無防備になるため、authorize は
         * 意図的に含めない。
         */
        'skipCsrfUrl' => [
            'Mcp' => '/bc-mcp',
            'OAuth2Token' => '/bc-mcp/oauth2/token',
            'OAuth2Register' => '/bc-mcp/oauth2/register',
            'OAuth2RegisterClient' => '/bc-mcp/oauth2/register/*',
            'OAuth2Verify' => '/bc-mcp/oauth2/verify',
            'OAuth2ClientInfo' => '/bc-mcp/oauth2/client-info',
        ]
```

- [ ] **Step 4: テストを実行して通ることを確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter Oauth2ConsentFlowTest --no-coverage`

Expected: 2 tests PASS。

- [ ] **Step 5: スイート全体を実行する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --no-coverage`

Expected: `Admin/OAuth2ControllerTest::testIntegration` と `OAuth2ControllerTest` の同意 POST を含む2件が FAIL する（CSRF トークンが無いため）。これらの修正は Task 3 と Task 6 で行うため、ここでは失敗するテスト名を控えておく。他は PASS していること。

- [ ] **Step 6: コミット**

```bash
git add plugins/bc-mcp/config/setting.php plugins/bc-mcp/tests/TestCase/Controller/Admin/Oauth2ConsentFlowTest.php
git commit -m "bc-mcp: 同意エンドポイントを CSRF 保護の対象に戻す"
```

---

### Task 2: 認可リクエストの検証を GET 時に行いセッションへ保持する

**Files:**
- Modify: `plugins/bc-mcp/src/Controller/Admin/Oauth2Controller.php:56-214`
- Modify: `plugins/bc-mcp/templates/Admin/Oauth2/authorize.php:1-45`
- Test: `plugins/bc-mcp/tests/TestCase/Controller/Admin/Oauth2ConsentFlowTest.php`

**Interfaces:**
- Consumes: Task 1 のテストヘルパと CSRF 保護下の `/bc-mcp/oauth2/authorize`
- Produces: 定数 `Oauth2Controller::SESSION_AUTH_REQUEST = 'BcMcp.authRequest'`。GET 成功時にこのキーへ `League\OAuth2\Server\RequestTypes\AuthorizationRequest` が書かれる。ビュー変数は `client`（`ClientEntityInterface`）、`scopes`（`ScopeEntityInterface[]`）、`user`。

- [ ] **Step 1: 失敗するテストを書く**

`Oauth2ConsentFlowTest` に3件追加する。

```php
    /**
     * GET で認可リクエストが検証されセッションに保持される
     *
     * @return void
     */
    public function testGetStoresValidatedAuthorizationRequest(): void
    {
        $client = $this->registerClient();
        $this->loginAdmin($this->getRequest());

        $this->get('/bc-mcp/oauth2/authorize?' . $this->authorizeQuery($client['client_id']));

        $this->assertResponseOk();
        $this->assertResponseContains('Consent Flow Test Client');
        // スコープは識別子ではなく説明文で見せる
        $this->assertResponseContains('データの読み取り');

        $authRequest = $this->_requestSession->read('BcMcp.authRequest');
        $this->assertInstanceOf(
            \League\OAuth2\Server\RequestTypes\AuthorizationRequest::class,
            $authRequest
        );
        $this->assertEquals($client['client_id'], $authRequest->getClient()->getIdentifier());
    }

    /**
     * 不正な redirect_uri の GET は同意画面を出さない
     *
     * @return void
     */
    public function testGetWithInvalidRedirectUriDoesNotRenderConsent(): void
    {
        $client = $this->registerClient();
        $this->loginAdmin($this->getRequest());

        $this->get('/bc-mcp/oauth2/authorize?' . $this->authorizeQuery($client['client_id'], [
            'redirect_uri' => 'https://attacker.example.com/callback',
        ]));

        $this->assertResponseCode(400);
        $this->assertNull($this->_requestSession->read('BcMcp.authRequest'));
    }

    /**
     * code_challenge の無い認可リクエストは拒否される
     *
     * @return void
     */
    public function testGetWithoutCodeChallengeIsRejected(): void
    {
        $client = $this->registerClient();
        $this->loginAdmin($this->getRequest());

        $this->get('/bc-mcp/oauth2/authorize?' . http_build_query([
            'client_id' => $client['client_id'],
            'response_type' => 'code',
            'redirect_uri' => self::REDIRECT_URI,
        ]));

        $this->assertResponseCode(400);
    }
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter Oauth2ConsentFlowTest --no-coverage`

Expected: `testGetStoresValidatedAuthorizationRequest` が FAIL（セッションに何も書かれず `assertInstanceOf` で落ちる。スコープも識別子のまま表示される）。`testGetWithoutCodeChallengeIsRejected` も FAIL（現行は GET 時に league の検証を通していないため 200 が返る）。

- [ ] **Step 3: コントローラの GET 側を実装する**

`plugins/bc-mcp/src/Controller/Admin/Oauth2Controller.php` の `authorize()` を次の形に置き換える。POST 側の実装は Task 3 で行うため、この時点では `completeAuthorization()` への委譲だけ書き、既存の POST 処理ブロックはそのまま残しておく（Task 3 で削除する）。

```php
    /**
     * 認可リクエストを保持するセッションキー
     */
    public const SESSION_AUTH_REQUEST = 'BcMcp.authRequest';

    /**
     * 認可エンドポイント
     *
     * Authorization Code Grant の開始点。GET で認可リクエストを検証して
     * セッションへ保持し、同意画面にはセッションの内容だけを表示する。
     * baserCMS の管理画面認証が必要。
     *
     * @return \Cake\Http\Response|\Psr\Http\Message\ResponseInterface
     */
    public function authorize()
    {
        $user = $this->Authentication->getIdentity();
        if (!$user) {
            // baserCMS標準のログインページにリダイレクト
            $this->Flash->set('認証が必要です。ログインしてください。');
            return $this->redirect([
                'plugin' => 'BaserCore',
                'prefix' => 'Admin',
                'controller' => 'Users',
                'action' => 'login',
                '?' => [
                    'redirect' => $this->request->getRequestTarget()
                ]
            ]);
        }

        if ($this->request->is('post')) {
            return $this->completeAuthorization();
        }

        try {
            $psrRequest = OAuth2Util::createPsr7Request($this->request);
            $authRequest = $this->oauth2Service->getAuthorizationServer()
                ->validateAuthorizationRequest($psrRequest);
        } catch (OAuthServerException $e) {
            // client_id / redirect_uri / response_type / PKCE の不備は league が
            // 判定する。リダイレクト先が信頼できない段階のため、クライアントへ
            // リダイレクトせずその場でエラーを返す。
            return $this->response
                ->withStatus(400)
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'error' => $e->getErrorType(),
                    'error_description' => $e->getMessage(),
                ]));
        }

        // 同意画面に見せた内容と、実際に発行される権限を一致させるため、
        // 検証済みの認可リクエストをセッションへ保持する。
        $this->request->getSession()->write(self::SESSION_AUTH_REQUEST, $authRequest);

        $this->set([
            'client' => $authRequest->getClient(),
            'scopes' => $authRequest->getScopes(),
            'user' => $user,
        ]);

        return $this->render('authorize');
    }
```

併せて次を行う。

- `use League\OAuth2\Server\Exception\OAuthServerException;` を追記する
- 手書きの必須パラメータチェック、`response_type !== 'code'` チェック、クライアント存在チェック、`in_array($redirectUri, $client->getRedirectUri())` チェックを削除する
- 使われなくなる `use BcMcp\OAuth2\Repository\OAuth2ClientRepository;` を削除する
- 末尾の `catch (\Exception $exception)` による 500 ブロックを削除する（league の例外は上で捕捉する）

- [ ] **Step 4: 同意画面テンプレートを修正する**

`plugins/bc-mcp/templates/Admin/Oauth2/authorize.php` の先頭 docblock を差し替える。

```php
<?php
/**
 * OAuth2 認可画面テンプレート
 *
 * 表示内容は GET 時に検証してセッションへ保持した認可リクエスト由来。
 * POST ではフォームの値を読まないため、hidden 入力は持たない。
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \League\OAuth2\Server\Entities\ClientEntityInterface $client
 * @var \League\OAuth2\Server\Entities\ScopeEntityInterface[] $scopes
 * @var \BaserCore\Model\Entity\User $user
 */
$this->BcBaser->setTitle('BcMcp アプリケーション認可');
?>
```

権限一覧の箇所を差し替える。スコープの説明文は `OAuth2ScopeRepository` が持っているため、
`OAuth2Helper` は使わない。

```php
      <div class="permissions mb-3">
        <h4>要求されている権限</h4>
        <ul>
          <?php if (!$scopes): ?>
            <li>基本的なアクセス権限</li>
          <?php else: ?>
            <?php foreach($scopes as $scope): ?>
              <li><?= h($scope->getDescription()?: $scope->getIdentifier()) ?></li>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
      </div>
```

`$this->BcAdminForm->hidden('client_id', ...)` から `hidden('state', ...)` までの4行を削除する。
`$this->BcAdminForm->create(null, ['type' => 'post'])` は残す（CSRF トークンがここで出力される）。

- [ ] **Step 5: テストを実行して通ることを確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter Oauth2ConsentFlowTest --no-coverage`

Expected: 5 tests PASS。

- [ ] **Step 6: コミット**

```bash
git add plugins/bc-mcp/src/Controller/Admin/Oauth2Controller.php plugins/bc-mcp/templates/Admin/Oauth2/authorize.php plugins/bc-mcp/tests/TestCase/Controller/Admin/Oauth2ConsentFlowTest.php
git commit -m "bc-mcp: 認可リクエストの検証を GET 時に行いセッションへ保持する"
```

---

### Task 3: 同意 POST をセッションの内容だけで完了させる

**Files:**
- Modify: `plugins/bc-mcp/src/Controller/Admin/Oauth2Controller.php`
- Modify: `plugins/bc-mcp/tests/TestCase/Controller/Admin/OAuth2ControllerTest.php:196-215`
- Test: `plugins/bc-mcp/tests/TestCase/Controller/Admin/Oauth2ConsentFlowTest.php`

**Interfaces:**
- Consumes: `Oauth2Controller::SESSION_AUTH_REQUEST`（Task 2）
- Produces: `completeAuthorization()` が許可・拒否の両方を league の `completeAuthorizationRequest()` 経由で処理し、`iss` 付きの 302 を返す。セッションのキーは処理後に必ず削除される。

- [ ] **Step 1: 失敗するテストを書く**

`Oauth2ConsentFlowTest` に3件追加する。

```php
    /**
     * セッションに認可リクエストが無い POST は 400 になる
     *
     * @return void
     */
    public function testApproveWithoutSessionRequestReturnsBadRequest(): void
    {
        $this->registerClient();
        $this->loginAdmin($this->getRequest());
        $this->enableCsrfToken();

        $this->post('/bc-mcp/oauth2/authorize', ['action' => 'approve']);

        $this->assertResponseCode(400);
        $this->assertResponseNotContains('code=');
    }

    /**
     * POST に混ぜた別のクライアント・リダイレクト先は無視される
     *
     * @return void
     */
    public function testApproveIgnoresParametersInPostRequest(): void
    {
        $legitimate = $this->registerClient();
        $this->loginAdmin($this->getRequest());

        // 正規のクライアントで同意画面を開く
        $this->get('/bc-mcp/oauth2/authorize?' . $this->authorizeQuery($legitimate['client_id']));
        $this->assertResponseOk();

        $this->enableCsrfToken();

        // 攻撃者のリダイレクト先をクエリとボディの両方に混ぜる
        $this->post('/bc-mcp/oauth2/authorize?' . http_build_query([
            'client_id' => 'client_attacker',
            'redirect_uri' => 'https://attacker.example.com/callback',
        ]), [
            'action' => 'approve',
            'client_id' => 'client_attacker',
            'redirect_uri' => 'https://attacker.example.com/callback',
        ]);

        $this->assertResponseCode(302);
        $location = $this->_response->getHeaderLine('Location');
        $this->assertStringStartsWith(self::REDIRECT_URI, $location);
        $this->assertStringNotContainsString('attacker.example.com', $location);
    }

    /**
     * 拒否した場合は access_denied で戻る
     *
     * @return void
     */
    public function testDenyRedirectsWithAccessDenied(): void
    {
        $client = $this->registerClient();
        $this->loginAdmin($this->getRequest());

        $this->get('/bc-mcp/oauth2/authorize?' . $this->authorizeQuery($client['client_id'], [
            'state' => 'test-state',
        ]));
        $this->assertResponseOk();

        $this->enableCsrfToken();
        $this->post('/bc-mcp/oauth2/authorize', ['action' => 'deny']);

        $this->assertResponseCode(302);
        $location = $this->_response->getHeaderLine('Location');
        $this->assertStringContainsString('error=access_denied', $location);
        $this->assertStringContainsString('state=test-state', $location);
        $this->assertStringContainsString('iss=', $location);
    }
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter Oauth2ConsentFlowTest --no-coverage`

Expected: 追加した3件が FAIL（`completeAuthorization()` が未実装のため `Error: Call to undefined method`）。

- [ ] **Step 3: POST 側を実装する**

`plugins/bc-mcp/src/Controller/Admin/Oauth2Controller.php` に残っている POST 処理ブロック（`if ($action === 'approve')` と `elseif ($action === 'deny')` の一式）を削除し、次のメソッドを追加する。

```php
    /**
     * 認可を完了させる
     *
     * クエリもボディも読まず、GET 時に検証してセッションへ保持した認可
     * リクエストのみを使う。これにより同意画面で見せた権限と、実際に
     * 発行される権限がすり替わる余地を無くす。
     *
     * @return \Cake\Http\Response|\Psr\Http\Message\ResponseInterface
     */
    private function completeAuthorization()
    {
        $session = $this->request->getSession();
        $authRequest = $session->read(self::SESSION_AUTH_REQUEST);
        // 認可コードの二重発行を防ぐため、読み出したら必ず破棄する
        $session->delete(self::SESSION_AUTH_REQUEST);

        if (!$authRequest instanceof AuthorizationRequest) {
            return $this->response
                ->withStatus(400)
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'error' => 'invalid_request',
                    'error_description' => 'Authorization request not found. Please start over.',
                ]));
        }

        $userEntity = new User();
        $userEntity->setIdentifier($this->Authentication->getIdentity()->getIdentifier());
        $authRequest->setUser($userEntity);
        $authRequest->setAuthorizationApproved($this->request->getData('action') === 'approve');

        try {
            $authResponse = $this->oauth2Service->getAuthorizationServer()
                ->completeAuthorizationRequest($authRequest, $this->response);
        } catch (OAuthServerException $e) {
            // 拒否時は league が access_denied のリダイレクトを生成する
            $authResponse = $e->generateHttpResponse($this->response);
        }

        // RFC 9207: 認可レスポンスに issuer を含める。
        // 2026-07-28 のクライアントは iss があれば検証が MUST。
        $location = $authResponse->getHeaderLine('Location');
        if ($location !== '') {
            $authResponse = $authResponse->withHeader(
                'Location',
                OAuth2Util::addIssuerToUrl($location, OAuth2Util::getIssuer($this->request))
            );
        }

        return $authResponse;
    }
```

`use League\OAuth2\Server\RequestTypes\AuthorizationRequest;` を追記する。`BcMcp\OAuth2\Entity\User` の `use` は既にある。

- [ ] **Step 4: テストを実行して通ることを確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter Oauth2ConsentFlowTest --no-coverage`

Expected: 8 tests PASS。

- [ ] **Step 5: 既存の統合テストを新しいフローに合わせる**

`plugins/bc-mcp/tests/TestCase/Controller/Admin/OAuth2ControllerTest.php` の `testIntegration()` を修正する。

まず、クライアント登録に PKCE を通すため、テストメソッドの先頭に code_verifier を用意する。

```php
        $codeVerifier = 'bc-mcp-test-code-verifier-0123456789012345678901234567';
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
```

登録リクエストに `'token_endpoint_auth_method' => 'none'` を追加する。ログイン後の認可リクエスト（GET）は次に置き換える。

```php
        $this->loginAdmin($this->getRequest());
        $this->get('/bc-mcp/oauth2/authorize?' . http_build_query([
                'client_id' => $metadata['client_id'],
                'response_type' => 'code',
                'redirect_uri' => $metadata['redirect_uris'][0],
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => 'S256',
            ]));
        $this->assertResponseCode(200);
```

その直前にある、未ログイン状態での GET（302 を期待している箇所）も同じクエリに揃える。認可承認の POST は次に置き換える。

```php
        // 認可承認
        // 同意 POST は CSRF 保護下にあり、認可リクエストはセッションから取得される
        $this->enableCsrfToken();
        $this->post('/bc-mcp/oauth2/authorize', ['action' => 'approve']);
        $this->assertResponseCode(302);
```

トークン交換の POST から `'client_secret' => ...` を削除し、`'code_verifier' => $codeVerifier` を追加する。

- [ ] **Step 6: スイート全体を実行する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --no-coverage`

Expected: `Admin/OAuth2ControllerTest::testIntegration` が PASS に戻る。`OAuth2ControllerTest` の2件（`testTokenEndpointWithValidCredentials` / `testVerifyWithValidToken`）はまだ FAIL（Task 6 で修正する）。

- [ ] **Step 7: コミット**

```bash
git add plugins/bc-mcp/src/Controller/Admin/Oauth2Controller.php plugins/bc-mcp/tests/TestCase/Controller/Admin/Oauth2ConsentFlowTest.php plugins/bc-mcp/tests/TestCase/Controller/Admin/OAuth2ControllerTest.php
git commit -m "bc-mcp: 同意 POST をセッションの認可リクエストのみで完了させる"
```

---

### Task 4: 暗号化キーの必須化と設定不備時の 503

**Files:**
- Create: `plugins/bc-mcp/src/OAuth2/Exception/OAuth2ConfigurationException.php`
- Modify: `plugins/bc-mcp/src/OAuth2/Service/OAuth2Service.php:33-42,160-169`
- Modify: `plugins/bc-mcp/src/Controller/Oauth2Controller.php:37-55`
- Modify: `plugins/bc-mcp/src/Controller/Admin/Oauth2Controller.php`
- Modify: `plugins/bc-mcp/src/Controller/McpProxyController.php:41-90`
- Test: `plugins/bc-mcp/tests/TestCase/Service/OAuth2ServiceTest.php`, `plugins/bc-mcp/tests/TestCase/Controller/Admin/Oauth2ConsentFlowTest.php`

**Interfaces:**
- Consumes: なし
- Produces: `BcMcp\OAuth2\Exception\OAuth2ConfigurationException`（`\RuntimeException` を継承）。`OAuth2Service::__construct()` が未設定時にこれを投げる。各コントローラは `?OAuth2ConfigurationException $oauth2ConfigError` に保持し、`beforeFilter()` で 503 を返す。

- [ ] **Step 1: 失敗するテストを書く**

`plugins/bc-mcp/tests/TestCase/Service/OAuth2ServiceTest.php` に追加する。

```php
    /**
     * 暗号化キーが未設定なら例外を投げる
     *
     * @return void
     */
    public function testConstructThrowsWhenEncryptionKeyIsMissing(): void
    {
        $original = env('OAUTH2_ENC_KEY');
        putenv('OAUTH2_ENC_KEY');
        unset($_ENV['OAUTH2_ENC_KEY'], $_SERVER['OAUTH2_ENC_KEY']);
        try {
            $this->expectException(\BcMcp\OAuth2\Exception\OAuth2ConfigurationException::class);
            new \BcMcp\OAuth2\Service\OAuth2Service();
        } finally {
            if ($original !== null) {
                putenv('OAUTH2_ENC_KEY=' . $original);
                $_ENV['OAUTH2_ENC_KEY'] = $original;
            }
        }
    }
```

`Oauth2ConsentFlowTest` に追加する。

```php
    /**
     * 暗号化キー未設定なら OAuth2 / MCP エンドポイントは 503 になる
     *
     * @return void
     */
    public function testEndpointsReturnServiceUnavailableWhenEncryptionKeyIsMissing(): void
    {
        $original = env('OAUTH2_ENC_KEY');
        putenv('OAUTH2_ENC_KEY');
        unset($_ENV['OAUTH2_ENC_KEY'], $_SERVER['OAUTH2_ENC_KEY']);
        try {
            $this->post('/bc-mcp/oauth2/token', ['grant_type' => 'authorization_code']);
            $this->assertResponseCode(503);

            $this->post('/bc-mcp', json_encode(['jsonrpc' => '2.0', 'method' => 'tools/list']));
            $this->assertResponseCode(503);
            $body = json_decode((string)$this->_response->getBody(), true);
            $this->assertEquals(-32603, $body['error']['code']);
        } finally {
            if ($original !== null) {
                putenv('OAUTH2_ENC_KEY=' . $original);
                $_ENV['OAUTH2_ENC_KEY'] = $original;
            }
        }
    }
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter "testConstructThrowsWhenEncryptionKeyIsMissing|testEndpointsReturnServiceUnavailableWhenEncryptionKeyIsMissing" --no-coverage`

Expected: 2 tests FAIL（例外クラスが存在せず、フォールバック値で動作してしまう）。

- [ ] **Step 3: 例外クラスを作る**

`plugins/bc-mcp/src/OAuth2/Exception/OAuth2ConfigurationException.php` を新規作成する。

```php
<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Exception;

use RuntimeException;

/**
 * OAuth2 の設定不備を表す例外
 *
 * 暗号化キーのように、欠けたまま動かすと安全性が損なわれる設定が
 * 未設定である事を表す。各コントローラはこれを捕捉して 503 を返す。
 */
class OAuth2ConfigurationException extends RuntimeException
{
}
```

- [ ] **Step 4: OAuth2Service を修正する**

`plugins/bc-mcp/src/OAuth2/Service/OAuth2Service.php` のコンストラクタと `getEncryptionKey()` を差し替える。

```php
    /**
     * コンストラクタ
     *
     * 暗号化キーが未設定のまま動かすと、認可コードとリフレッシュトークンの
     * 機密性が失われるため、この時点で停止させる。
     */
    public function __construct()
    {
        $this->assertEncryptionKey();
        if (!file_exists(CONFIG . 'oauth2_public.key')) {
            $this->generateKeyPair();
        }
    }

    /**
     * 暗号化キーが設定されている事を確認する
     *
     * 形式や長さは検証しない。install 時に base64_encode(random_bytes(32)) が
     * 書かれる前提であり、形式チェックは誤検知の害のほうが大きい。
     *
     * @return void
     * @throws \BcMcp\OAuth2\Exception\OAuth2ConfigurationException
     */
    private function assertEncryptionKey(): void
    {
        if (!env('OAUTH2_ENC_KEY')) {
            throw new OAuth2ConfigurationException(
                'OAUTH2_ENC_KEY が設定されていないため MCP サーバーを起動できません。config/.env に設定してください。'
            );
        }
    }
```

```php
    /**
     * Get Encryption Key
     * @return string
     */
    private function getEncryptionKey(): string
    {
        return (string)env('OAUTH2_ENC_KEY');
    }
```

`use BcMcp\OAuth2\Exception\OAuth2ConfigurationException;` を追記する。

- [ ] **Step 5: 3つのコントローラで 503 を返す**

`plugins/bc-mcp/src/Controller/Oauth2Controller.php` のプロパティ宣言を `private ?OAuth2Service $oauth2Service = null;` / `private ?OAuth2ClientRegistrationService $clientRegistrationService = null;` に変更し、`initialize()` を修正して `beforeFilter()` を追加する。

```php
    /**
     * OAuth2 の設定不備
     *
     * @var OAuth2ConfigurationException|null
     */
    private ?OAuth2ConfigurationException $oauth2ConfigError = null;

    /**
     * 初期化
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->FormProtection->setConfig('validate', false);
        try {
            $this->oauth2Service = new OAuth2Service();
            $clientRepository = new OAuth2ClientRepository();
            $this->clientRegistrationService = new OAuth2ClientRegistrationService($clientRepository);
        } catch (OAuth2ConfigurationException $e) {
            // 設定不備は beforeFilter で 503 として返す
            $this->oauth2ConfigError = $e;
        }

        // CORS設定
        $this->response = $this->response->withHeader('Access-Control-Allow-Origin', '*');
        $this->response = $this->response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $this->response = $this->response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, MCP-Protocol-Version');
    }

    /**
     * リクエスト処理前に設定不備を確認する
     *
     * @param \Cake\Event\EventInterface $event イベント
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        if ($event->getResult()) return;
        if (!$this->oauth2ConfigError) return;

        $event->setResult($this->response
            ->withStatus(503)
            ->withType('application/json')
            ->withStringBody(json_encode([
                'error' => 'server_error',
                'error_description' => $this->oauth2ConfigError->getMessage(),
            ])));
    }
```

`use Cake\Event\EventInterface;` と `use BcMcp\OAuth2\Exception\OAuth2ConfigurationException;` を追記する。

`plugins/bc-mcp/src/Controller/Admin/Oauth2Controller.php` にも同じ形で `$oauth2ConfigError` と `beforeFilter()` を追加する（レスポンス本文は同じ JSON）。

`plugins/bc-mcp/src/Controller/McpProxyController.php` は `initialize()` の `new OAuth2Service()` を同じ try/catch で包み、既存の `beforeFilter()` の `parent::beforeFilter($event);` の直後に挿入する。

```php
        // 暗号化キー未設定などの設定不備は、認証より前に 503 として返す
        if ($this->oauth2ConfigError) {
            $event->setResult($this->response
                ->withStatus(503)
                ->withHeader('Content-Type', 'application/json')
                ->withStringBody(json_encode([
                    'jsonrpc' => '2.0',
                    'error' => [
                        'code' => -32603,
                        'message' => $this->oauth2ConfigError->getMessage(),
                    ]
                ], JSON_UNESCAPED_UNICODE)));
            return;
        }
```

- [ ] **Step 6: テストを実行して通ることを確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter "testConstructThrowsWhenEncryptionKeyIsMissing|testEndpointsReturnServiceUnavailableWhenEncryptionKeyIsMissing" --no-coverage`

Expected: 2 tests PASS。

- [ ] **Step 7: スイート全体を実行する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --no-coverage`

Expected: Task 3 時点と同じ結果（`OAuth2ControllerTest` の2件のみ FAIL）。多数のテストが 503 で落ちる場合は、テスト環境の `config/.env` に `OAUTH2_ENC_KEY` が無い。`export OAUTH2_ENC_KEY="<base64 の32バイト>"` を追記してから再実行する。

- [ ] **Step 8: コミット**

```bash
git add plugins/bc-mcp/src/OAuth2/Exception/OAuth2ConfigurationException.php plugins/bc-mcp/src/OAuth2/Service/OAuth2Service.php plugins/bc-mcp/src/Controller plugins/bc-mcp/tests
git commit -m "bc-mcp: 暗号化キーを必須化し未設定時は OAuth2/MCP を 503 で停止する"
```

---

### Task 5: 管理画面の警告と install 時の書き込み失敗検知

**Files:**
- Modify: `plugins/bc-mcp/src/Controller/Admin/McpServerManagerController.php:30-45`
- Modify: `plugins/bc-mcp/templates/Admin/McpServerManager/index.php:1-20`
- Modify: `plugins/bc-mcp/src/BcMcpPlugin.php:52-70`
- Test: `plugins/bc-mcp/tests/TestCase/Controller/Admin/McpServerManagerControllerTest.php`

**Interfaces:**
- Consumes: なし
- Produces: ビュー変数 `encryptionKeyMissing`（bool）。`BcMcpPlugin::install()` は `putEnv()` が false を返した場合に false を返す。

- [ ] **Step 1: 失敗するテストを書く**

`plugins/bc-mcp/tests/TestCase/Controller/Admin/McpServerManagerControllerTest.php` に追加する。

```php
    /**
     * 暗号化キー未設定なら管理画面に警告が出る
     *
     * @return void
     */
    public function testIndexShowsWarningWhenEncryptionKeyIsMissing(): void
    {
        $original = env('OAUTH2_ENC_KEY');
        putenv('OAUTH2_ENC_KEY');
        unset($_ENV['OAUTH2_ENC_KEY'], $_SERVER['OAUTH2_ENC_KEY']);
        try {
            $this->loginAdmin($this->getRequest());
            $this->get('/baser/admin/bc-mcp/mcp-server-manager');
            $this->assertResponseOk();
            $this->assertResponseContains('OAUTH2_ENC_KEY');
            $this->assertResponseContains('停止しています');
        } finally {
            if ($original !== null) {
                putenv('OAUTH2_ENC_KEY=' . $original);
                $_ENV['OAUTH2_ENC_KEY'] = $original;
            }
        }
    }
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter testIndexShowsWarningWhenEncryptionKeyIsMissing --no-coverage`

Expected: FAIL（警告文が存在しない）。

- [ ] **Step 3: コントローラでキーの有無を渡す**

`plugins/bc-mcp/src/Controller/Admin/McpServerManagerController.php` の `index()` の `$this->set([...])` に追加する。

```php
            // 暗号化キーが無いと OAuth2/MCP は 503 で停止するため、
            // 管理画面で気付けるようにする
            'encryptionKeyMissing' => !env('OAUTH2_ENC_KEY'),
```

- [ ] **Step 4: 警告パネルを表示する**

`plugins/bc-mcp/templates/Admin/McpServerManager/index.php` の docblock に `@var bool $encryptionKeyMissing` を追記し、`<!-- 接続情報 -->` の直前に挿入する。

```php
<?php if ($encryptionKeyMissing): ?>
  <div class="bca-panel-box">
    <div class="bca-panel-box__body">
      <p class="bca-alert bca-alert--error">
        暗号化キー（OAUTH2_ENC_KEY）が設定されていないため、MCP サーバーは停止しています。
        <code>config/.env</code> に <code>OAUTH2_ENC_KEY</code> を設定してください。
      </p>
    </div>
  </div>
<?php endif; ?>
```

- [ ] **Step 5: install の書き込み失敗を検知する**

`plugins/bc-mcp/src/BcMcpPlugin.php` の `install()` を修正する。

```php
        $oauth2EncKey = base64_encode(random_bytes(32));
        // 暗号化キーを書けないまま完了させると、OAuth2 が停止した状態で
        // インストール済みに見えてしまうため、失敗として扱う
        if (!$siteConfigsService->putEnv('OAUTH2_ENC_KEY', $oauth2EncKey)) {
            $this->log('OAUTH2_ENC_KEY を config/.env に書き込めませんでした。書き込み権限を確認してください。');
            return false;
        }
```

- [ ] **Step 6: テストを実行して通ることを確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter McpServerManagerControllerTest --no-coverage`

Expected: 全 PASS。

- [ ] **Step 7: コミット**

```bash
git add plugins/bc-mcp/src/Controller/Admin/McpServerManagerController.php plugins/bc-mcp/templates/Admin/McpServerManager/index.php plugins/bc-mcp/src/BcMcpPlugin.php plugins/bc-mcp/tests/TestCase/Controller/Admin/McpServerManagerControllerTest.php
git commit -m "bc-mcp: 暗号化キー未設定を管理画面とインストールで検知する"
```

---

### Task 6: client_credentials の除去と PKCE の S256 限定

**Files:**
- Modify: `plugins/bc-mcp/src/OAuth2/Service/OAuth2Service.php:88-96`
- Modify: `plugins/bc-mcp/src/OAuth2/Grant/AuthCodeGrant.php`
- Modify: `plugins/bc-mcp/src/OAuth2/Service/OAuth2ClientRegistrationService.php:41-47`
- Modify: `plugins/bc-mcp/src/Lib/OAuth2Util.php:70-95`
- Modify: `plugins/bc-mcp/tests/TestCase/Controller/OAuth2ControllerTest.php:33-41,84-126,168-185,211-250`
- Modify: `plugins/bc-mcp/tests/TestCase/Service/OAuth2ServiceTest.php:30-38`

**Interfaces:**
- Consumes: なし
- Produces: `AuthorizationServer` に登録されるグラントは `AuthCodeGrant` と `RefreshTokenGrant` のみ。`OAuth2ClientRegistrationService::$supportedGrantTypes` は `['authorization_code', 'refresh_token']`。`BcMcp\OAuth2\Grant\AuthCodeGrant::validateAuthorizationRequest()` は `code_challenge_method` が `S256` 以外なら `OAuthServerException` を投げる。

- [ ] **Step 1: 失敗するテストを書く**

`Oauth2ConsentFlowTest` に2件追加する。

```php
    /**
     * client_credentials は登録もトークン発行も拒否される
     *
     * @return void
     */
    public function testClientCredentialsIsRejected(): void
    {
        // 登録時に拒否される
        $this->post('/bc-mcp/oauth2/register', [
            'client_name' => 'Machine Client',
            'redirect_uris' => [self::REDIRECT_URI],
            'grant_types' => ['client_credentials'],
            'token_endpoint_auth_method' => 'none',
        ]);
        $this->assertResponseCode(400);

        // 正規に登録したクライアントでも client_credentials は使えない
        $client = $this->registerClient();
        $this->post('/bc-mcp/oauth2/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $client['client_id'],
            'scope' => 'mcp:read',
        ]);
        $this->assertResponseCode(400);
        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertEquals('unsupported_grant_type', $body['error']);
    }

    /**
     * PKCE は S256 のみを受け付ける
     *
     * @return void
     */
    public function testPlainCodeChallengeMethodIsRejected(): void
    {
        $client = $this->registerClient();
        $this->loginAdmin($this->getRequest());

        $this->get('/bc-mcp/oauth2/authorize?' . $this->authorizeQuery($client['client_id'], [
            'code_challenge_method' => 'plain',
        ]));

        $this->assertResponseCode(400);
    }
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter "testClientCredentialsIsRejected|testPlainCodeChallengeMethodIsRejected" --no-coverage`

Expected: 2 tests FAIL（登録が 201 で通り、`plain` も受理される）。

- [ ] **Step 3: サーバー側のグラントを削除する**

`plugins/bc-mcp/src/OAuth2/Service/OAuth2Service.php` の `createAuthorizationServer()` から次を削除する。

```php
        $clientCredentialsGrant = new ClientCredentialsGrant();
        $server->enableGrantType(
            $clientCredentialsGrant,
            new \DateInterval('PT1H')
        );
```

併せて `use League\OAuth2\Server\Grant\ClientCredentialsGrant;` を削除する。

- [ ] **Step 4: AuthCodeGrant で S256 に限定する**

`plugins/bc-mcp/src/OAuth2/Grant/AuthCodeGrant.php` にメソッドを追加する。

```php
    /**
     * 認可リクエストを検証する
     *
     * league は code_challenge_method の指定が無い場合に plain として扱うが、
     * 認可サーバーメタデータでは S256 のみを宣言している。宣言と実態を
     * 一致させるため、S256 以外を拒否する。
     *
     * @param ServerRequestInterface $request
     * @return \League\OAuth2\Server\RequestTypes\AuthorizationRequest
     * @throws OAuthServerException
     */
    public function validateAuthorizationRequest(ServerRequestInterface $request)
    {
        $queryParams = $request->getQueryParams();
        if (isset($queryParams['code_challenge'])
            && ($queryParams['code_challenge_method'] ?? 'plain') !== 'S256') {
            throw OAuthServerException::invalidRequest(
                'code_challenge_method',
                'Code challenge method must be S256'
            );
        }
        return parent::validateAuthorizationRequest($request);
    }
```

- [ ] **Step 5: 登録可能なグラントを絞る**

`plugins/bc-mcp/src/OAuth2/Service/OAuth2ClientRegistrationService.php` の `$supportedGrantTypes` を差し替える。

```php
    /**
     * サポートされるグラントタイプ
     *
     * MCP はユーザーの同意を前提とするため、ユーザー不在でトークンを
     * 発行できる client_credentials は受け付けない。
     *
     * @var array
     */
    private array $supportedGrantTypes = [
        'authorization_code',
        'refresh_token'
    ];
```

- [ ] **Step 6: PSR-7 変換から client_credentials 専用分岐を削除する**

`plugins/bc-mcp/src/Lib/OAuth2Util.php` の `createPsr7Request()` から、POST の `client_id` / `client_secret` を Basic 認証ヘッダへ変換するブロックを削除し、POST データの取得だけを残す。

```php
        // ボディに載ったパラメータをそのまま引き継ぐ
        $postData = [];
        if ($request->is('post')) {
            $postData = $request->getData();
        }
```

- [ ] **Step 7: OAuth2ControllerTest を新しい仕様に合わせる**

`plugins/bc-mcp/tests/TestCase/Controller/OAuth2ControllerTest.php` を修正する。

- L33-41 の `Configure::write('BcMcp.OAuth2.clients', ...)` の `'grants' => ['client_credentials']` を `['authorization_code', 'refresh_token']` に変更する
- L168-185 の `testClientRegistration()` は `'grant_types' => ['authorization_code']` に変更し、`'token_endpoint_auth_method' => 'none'` を追加、`$this->assertArrayHasKey('client_secret', $response);` を `$this->assertArrayNotHasKey('client_secret', $response);` に変更する
- L84-126 の `testTokenEndpointWithValidCredentials()` を次に置き換える

```php
    /**
     * Test token endpoint with valid client credentials (no auth required)
     *
     * @return void
     */
    public function testTokenEndpointWithValidCredentials(): void
    {
        // ファクトリの既定はパブリッククライアント（is_confidential = false）
        Oauth2ClientFactory::make()->persist();
        $this->loadFixtureScenario(InitAppScenario::class);

        $codeVerifier = 'bc-mcp-test-code-verifier-0123456789012345678901234567';
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        $this->loginAdmin($this->getRequest());
        // 同意画面を開いて認可リクエストをセッションへ保持させる
        $this->get('/bc-mcp/oauth2/authorize?' . http_build_query([
                'client_id' => 'mcp-client',
                'response_type' => 'code',
                'redirect_uri' => 'http://localhost',
                'scope' => 'mcp:read mcp:write',
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => 'S256',
            ]));
        $this->assertResponseOk();

        $this->enableCsrfToken();
        $this->post('/bc-mcp/oauth2/authorize', ['action' => 'approve']);

        $redirectUrl = $this->_response->getHeaderLine('Location');
        $queryParams = [];
        parse_str(parse_url($redirectUrl, PHP_URL_QUERY), $queryParams);
        $authCode = $queryParams['code'];

        // 認証なしでtokenエンドポイントをテスト
        $this->post('/bc-mcp/oauth2/token', [
            'grant_type' => 'authorization_code',
            'client_id' => 'mcp-client',
            'redirect_uri' => 'http://localhost',
            'code_verifier' => $codeVerifier,
            'code' => $authCode
        ]);

        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $response = json_decode((string)$this->_response->getBody(), true);
        $this->assertNotNull($response, 'Response should be valid JSON');
        $this->assertArrayHasKey('access_token', $response);
        $this->assertArrayHasKey('token_type', $response);
        $this->assertArrayHasKey('expires_in', $response);
        $this->assertEquals('Bearer', $response['token_type']);
    }
```

- L211 以降の `testVerifyWithValidToken()` も同じ形に置き換える（`Oauth2ClientFactory::make()->persist();` に変更、GET → `enableCsrfToken()` → POST の順にし、トークン要求から `client_secret` を除いて `code_verifier` を渡す）。verify エンドポイントのアサーションはそのまま残す。

- [ ] **Step 8: OAuth2ServiceTest を新しい仕様に合わせる**

`plugins/bc-mcp/tests/TestCase/Service/OAuth2ServiceTest.php:35` の `'grants' => ['client_credentials']` を `['authorization_code', 'refresh_token']` に変更する。

- [ ] **Step 9: スイート全体を実行する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --no-coverage`

Expected: 全 PASS。

- [ ] **Step 10: コミット**

```bash
git add plugins/bc-mcp/src plugins/bc-mcp/tests
git commit -m "bc-mcp: client_credentials を廃止し PKCE を S256 に限定する"
```

---

### Task 7: 登録メタデータの厳格化

**Files:**
- Create: `plugins/bc-mcp/src/OAuth2/Validator/RedirectUriRegistrationValidator.php`
- Create: `plugins/bc-mcp/tests/TestCase/OAuth2/Validator/RedirectUriRegistrationValidatorTest.php`
- Modify: `plugins/bc-mcp/src/OAuth2/Service/OAuth2ClientRegistrationService.php:55-76,95-115,283-300`
- Modify: `plugins/bc-mcp/tests/TestCase/Controller/OAuth2ControllerDynamicClientRegistrationTest.php:43-135,145-210,240-310`
- Modify: `plugins/bc-mcp/tests/TestCase/Service/OAuth2ClientRegistrationServiceTest.php:50-260`

**Interfaces:**
- Consumes: なし
- Produces: `RedirectUriRegistrationValidator::validate(array $redirectUris): void`（不正なら `InvalidArgumentException`）と定数 `MAX_URIS = 5`。登録サービスは `token_endpoint_auth_method` を `none` のみ受け付け、`client_secret` を発行しない。

- [ ] **Step 1: 失敗するテストを書く**

`plugins/bc-mcp/tests/TestCase/OAuth2/Validator/RedirectUriRegistrationValidatorTest.php` を新規作成する。

```php
<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\OAuth2\Validator;

use BcMcp\OAuth2\Validator\RedirectUriRegistrationValidator;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;

/**
 * RedirectUriRegistrationValidator Test Case
 */
class RedirectUriRegistrationValidatorTest extends TestCase
{

    /**
     * 許容される redirect_uri
     *
     * @return void
     */
    public function testValidateAllowsHttpsAndLoopback(): void
    {
        $validator = new RedirectUriRegistrationValidator();
        $validator->validate([
            'https://example.com/callback',
            'http://127.0.0.1:3000/callback',
            'http://[::1]:3000/callback',
            'http://localhost/callback',
        ]);
        $this->assertTrue(true, '例外が投げられないこと');
    }

    /**
     * 非ループバックの http は拒否する
     *
     * @return void
     */
    public function testValidateRejectsNonLoopbackHttp(): void
    {
        $validator = new RedirectUriRegistrationValidator();
        $this->expectException(InvalidArgumentException::class);
        $validator->validate(['http://example.com/callback']);
    }

    /**
     * フラグメント付きは拒否する
     *
     * @return void
     */
    public function testValidateRejectsFragment(): void
    {
        $validator = new RedirectUriRegistrationValidator();
        $this->expectException(InvalidArgumentException::class);
        $validator->validate(['https://example.com/callback#top']);
    }

    /**
     * 空の配列は拒否する
     *
     * @return void
     */
    public function testValidateRejectsEmptyList(): void
    {
        $validator = new RedirectUriRegistrationValidator();
        $this->expectException(InvalidArgumentException::class);
        $validator->validate([]);
    }

    /**
     * 上限を超える件数は拒否する
     *
     * @return void
     */
    public function testValidateRejectsTooManyUris(): void
    {
        $validator = new RedirectUriRegistrationValidator();
        $uris = [];
        for ($i = 0; $i <= RedirectUriRegistrationValidator::MAX_URIS; $i++) {
            $uris[] = 'https://example.com/callback' . $i;
        }
        $this->expectException(InvalidArgumentException::class);
        $validator->validate($uris);
    }
}
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter RedirectUriRegistrationValidatorTest --no-coverage`

Expected: 5 tests ERROR（クラスが存在しない）。

- [ ] **Step 3: バリデータを実装する**

`plugins/bc-mcp/src/OAuth2/Validator/RedirectUriRegistrationValidator.php` を新規作成する。

```php
<?php
declare(strict_types=1);

namespace BcMcp\OAuth2\Validator;

use InvalidArgumentException;

/**
 * 登録時の redirect_uri を検証する
 *
 * 認可時の照合（league の RedirectUriValidator）とは責務が異なる。こちらは
 * 「そもそも登録を受け付けてよい URI か」を判定する。動的クライアント登録が
 * 無認証で開いているため、平文の http で任意のホストへコードを飛ばせる登録を
 * 入口で弾く必要がある。
 */
class RedirectUriRegistrationValidator
{

    /**
     * 登録できる redirect_uri の上限件数
     */
    public const MAX_URIS = 5;

    /**
     * http を許容するループバックホスト
     *
     * RFC 8252 は IP リテラルを推奨しているが、ローカル環境の baserCMS は
     * http で動くため localhost も許容する。リダイレクト先が利用者の端末内に
     * 限られるためリスクは小さい。
     */
    private const LOOPBACK_HOSTS = ['127.0.0.1', '::1', 'localhost'];

    /**
     * redirect_uri の配列を検証する
     *
     * @param array $redirectUris redirect_uri の配列
     * @return void
     * @throws \InvalidArgumentException
     */
    public function validate(array $redirectUris): void
    {
        if (!$redirectUris) {
            throw new InvalidArgumentException('redirect_uris is required');
        }
        if (count($redirectUris) > self::MAX_URIS) {
            throw new InvalidArgumentException(
                'redirect_uris must not exceed ' . self::MAX_URIS . ' entries'
            );
        }
        foreach($redirectUris as $uri) {
            $this->validateOne($uri);
        }
    }

    /**
     * 単一の redirect_uri を検証する
     *
     * @param mixed $uri redirect_uri
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateOne($uri): void
    {
        if (!is_string($uri) || !filter_var($uri, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(
                'Invalid redirect_uri: ' . (is_string($uri)? $uri : gettype($uri))
            );
        }

        $parts = parse_url($uri);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            throw new InvalidArgumentException('Invalid redirect_uri: ' . $uri);
        }

        // RFC 6749: redirect_uri はフラグメントを含んではならない
        if (isset($parts['fragment'])) {
            throw new InvalidArgumentException('redirect_uri must not contain a fragment: ' . $uri);
        }

        $scheme = strtolower($parts['scheme']);
        if ($scheme === 'https') {
            return;
        }
        // parse_url は IPv6 のホストを角括弧付きで返す
        $host = strtolower(trim($parts['host'], '[]'));
        if ($scheme === 'http' && in_array($host, self::LOOPBACK_HOSTS, true)) {
            return;
        }

        throw new InvalidArgumentException(
            'redirect_uri must use https, or http with a loopback host: ' . $uri
        );
    }
}
```

- [ ] **Step 4: テストを実行して通ることを確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter RedirectUriRegistrationValidatorTest --no-coverage`

Expected: 5 tests PASS。

- [ ] **Step 5: 登録サービスに組み込む失敗するテストを書く**

`Oauth2ConsentFlowTest` に追加する。

```php
    /**
     * 登録メタデータの厳格化
     *
     * @return void
     */
    public function testRegistrationRejectsInvalidMetadata(): void
    {
        // 非ループバックの http は拒否
        $this->post('/bc-mcp/oauth2/register', [
            'client_name' => 'Insecure Client',
            'redirect_uris' => ['http://example.com/callback'],
        ]);
        $this->assertResponseCode(400);

        // redirect_uris 省略は拒否
        $this->post('/bc-mcp/oauth2/register', ['client_name' => 'No Redirect Client']);
        $this->assertResponseCode(400);

        // 機密クライアントは拒否
        $this->post('/bc-mcp/oauth2/register', [
            'client_name' => 'Confidential Client',
            'redirect_uris' => [self::REDIRECT_URI],
            'token_endpoint_auth_method' => 'client_secret_basic',
        ]);
        $this->assertResponseCode(400);

        // 存在しない admin スコープは拒否
        $this->post('/bc-mcp/oauth2/register', [
            'client_name' => 'Admin Scope Client',
            'redirect_uris' => [self::REDIRECT_URI],
            'scope' => 'admin',
        ]);
        $this->assertResponseCode(400);

        // localhost は許容し、シークレットは発行されない
        $this->post('/bc-mcp/oauth2/register', [
            'client_name' => 'Local Client',
            'redirect_uris' => ['http://localhost/callback'],
        ]);
        $this->assertResponseCode(201);
        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayNotHasKey('client_secret', $body);
        $this->assertEquals('none', $body['token_endpoint_auth_method']);
    }
```

- [ ] **Step 6: テストを実行して失敗を確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter testRegistrationRejectsInvalidMetadata --no-coverage`

Expected: FAIL（拒否されるべき登録が 201 で通る）。

- [ ] **Step 7: 登録サービスを修正する**

`plugins/bc-mcp/src/OAuth2/Service/OAuth2ClientRegistrationService.php` を変更する。

`$supportedAuthMethods` と `$supportedScopes` を差し替える。

```php
    /**
     * サポートされるトークンエンドポイント認証方法
     *
     * 動的クライアント登録が無認証で開いているため、誰でも機密クライアントを
     * 作れる状態を避け、PKCE 必須のパブリッククライアントに一本化する。
     * 認可サーバーメタデータの宣言（none のみ）とも一致させる。
     *
     * @var array
     */
    private array $supportedAuthMethods = [
        'none'
    ];

    /**
     * サポートされるスコープ
     *
     * OAuth2ScopeRepository に実体があるものだけを列挙する。
     *
     * @var array
     */
    private array $supportedScopes = [
        'mcp:read',
        'mcp:write'
    ];
```

`registerClient()` のシークレット生成部を差し替える。

```php
        // クライアントIDを生成
        $clientId = $this->generateClientId();
        // パブリッククライアントに一本化するため、シークレットは発行しない
        $clientSecret = null;
        $tokenEndpointAuthMethod = 'none';
```

`$clientData` の `'is_confidential' => $tokenEndpointAuthMethod !== 'none'` を `'is_confidential' => false` にし、末尾の `if ($clientSecret) { $saved->set('client_secret', $clientSecret); }` と、使われなくなる `generateClientSecret()` を削除する。

`validateRegistrationRequest()` の `redirect_uris` ブロックを差し替える。

```php
        // redirect_uris は authorization_code に必須であり、登録時に検証する
        (new RedirectUriRegistrationValidator())->validate($requestData['redirect_uris'] ?? []);
```

`updateClient()` は部分更新を受け付けるため、既存値とマージしてから検証する。`$this->validateRegistrationRequest($requestData);` の呼び出しを次に置き換える。

```php
        // 部分更新でも redirect_uris の必須検証が働くよう、既存値とマージして検証する
        $this->validateRegistrationRequest($requestData + [
            'redirect_uris' => $client->redirect_uris,
        ]);
```

`use BcMcp\OAuth2\Validator\RedirectUriRegistrationValidator;` を追記する。

- [ ] **Step 8: 登録系の既存テストを新しい仕様に合わせる**

`plugins/bc-mcp/tests/TestCase/Controller/OAuth2ControllerDynamicClientRegistrationTest.php` を修正する。

- L48 `'grant_types' => ['authorization_code', 'client_credentials']` → `['authorization_code', 'refresh_token']`
- L50 `'token_endpoint_auth_method' => 'client_secret_basic'` → `'none'`
- L74 `assertArrayHasKey('client_secret', ...)` → `assertArrayNotHasKey('client_secret', ...)`
- L82 期待値 `['authorization_code', 'client_credentials']` → `['authorization_code', 'refresh_token']`
- L84 期待値 `'client_secret_basic'` → `'none'`
- L101, L134, L149, L203, L305 の `'grant_types' => ['client_credentials']`（および期待値）→ `['authorization_code']`
- L245 の `'redirect_uris' => ['invalid-uri']` は引き続き 400 を期待するテストのため変更不要

`plugins/bc-mcp/tests/TestCase/Service/OAuth2ClientRegistrationServiceTest.php` を修正する。

- L57 `'token_endpoint_auth_method' => 'client_secret_basic'` → `'none'`、L69 の期待値も `'none'`
- L125, L150, L172, L205 の `'grant_types' => ['client_credentials']` → `['authorization_code']`
- L231 `['authorization_code', 'client_credentials']` → `['authorization_code', 'refresh_token']`、L256 の期待値も同様
- L233 `'token_endpoint_auth_method' => 'client_secret_post'` → `'none'`、L258 の期待値も `'none'`
- L248 `assertArrayHasKey('client_secret', ...)` → `assertArrayNotHasKey('client_secret', ...)`
- L88 の `'redirect_uris' => ['invalid-uri']` は引き続き例外を期待するテストのため変更不要

- [ ] **Step 9: スイート全体を実行する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --no-coverage`

Expected: 全 PASS。

- [ ] **Step 10: コミット**

```bash
git add plugins/bc-mcp/src plugins/bc-mcp/tests
git commit -m "bc-mcp: 動的クライアント登録で受け付けるメタデータを厳格化する"
```

---

### Task 8: クライアント登録のレート制限

**Files:**
- Create: `plugins/bc-mcp/src/Service/RegistrationRateLimiter.php`
- Create: `plugins/bc-mcp/tests/TestCase/Service/RegistrationRateLimiterTest.php`
- Modify: `plugins/bc-mcp/src/BcMcpPlugin.php`（`bootstrap()` に Cache 設定の登録）
- Modify: `plugins/bc-mcp/config/setting.php`
- Modify: `plugins/bc-mcp/src/Controller/Oauth2Controller.php`（`register()`）
- Modify: `plugins/bc-mcp/tests/TestCase/Controller/Admin/Oauth2ConsentFlowTest.php`（`setUp()` でキャッシュ消去）

**Interfaces:**
- Consumes: なし
- Produces: `RegistrationRateLimiter::CACHE_CONFIG = 'bc_mcp_registration'`、`isExceeded(string $clientIp): bool`、`hit(string $clientIp): void`。設定は `BcMcp.registration.maxPerHour`。

- [ ] **Step 1: 失敗するテストを書く**

`plugins/bc-mcp/tests/TestCase/Service/RegistrationRateLimiterTest.php` を新規作成する。

```php
<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Service;

use BcMcp\Service\RegistrationRateLimiter;
use Cake\Cache\Cache;
use Cake\Cache\Engine\FileEngine;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * RegistrationRateLimiter Test Case
 */
class RegistrationRateLimiterTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        if (!Cache::getConfig(RegistrationRateLimiter::CACHE_CONFIG)) {
            Cache::setConfig(RegistrationRateLimiter::CACHE_CONFIG, [
                'className' => FileEngine::class,
                'duration' => '+1 hours',
                'path' => CACHE . 'bc_mcp' . DS,
                'prefix' => 'bc_mcp_registration_',
            ]);
        }
        Cache::clear(RegistrationRateLimiter::CACHE_CONFIG);
        Configure::write('BcMcp.registration.maxPerHour', 3);
    }

    /**
     * 上限までは通り、超えたら止まる
     *
     * @return void
     */
    public function testIsExceededAfterReachingLimit(): void
    {
        $limiter = new RegistrationRateLimiter();
        for ($i = 0; $i < 3; $i++) {
            $this->assertFalse($limiter->isExceeded('192.0.2.1'), $i . '回目は通ること');
            $limiter->hit('192.0.2.1');
        }
        $this->assertTrue($limiter->isExceeded('192.0.2.1'));
    }

    /**
     * IP ごとに独立して数える
     *
     * @return void
     */
    public function testCountsPerClientIp(): void
    {
        $limiter = new RegistrationRateLimiter();
        for ($i = 0; $i < 3; $i++) {
            $limiter->hit('192.0.2.1');
        }
        $this->assertTrue($limiter->isExceeded('192.0.2.1'));
        $this->assertFalse($limiter->isExceeded('192.0.2.2'));
    }
}
```

- [ ] **Step 2: テストを実行して失敗を確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter RegistrationRateLimiterTest --no-coverage`

Expected: ERROR（クラスが存在しない）。

- [ ] **Step 3: レートリミッタを実装する**

`plugins/bc-mcp/src/Service/RegistrationRateLimiter.php` を新規作成する。

```php
<?php
declare(strict_types=1);

namespace BcMcp\Service;

use Cake\Cache\Cache;
use Cake\Core\Configure;

/**
 * クライアント登録のレート制限
 *
 * 動的クライアント登録は無認証で開いているため、無制限に行を増やせる。
 * IP 単位で回数を数え、上限を超えた登録を拒否する。
 *
 * 枠はキャッシュの有効期限（1時間）で切れる。登録のたびに書き直すため、
 * 実際には「最後の登録から1時間」で枠がリセットされる。
 */
class RegistrationRateLimiter
{

    /**
     * キャッシュ設定名
     */
    public const CACHE_CONFIG = 'bc_mcp_registration';

    /**
     * 上限に達しているかを判定する
     *
     * @param string $clientIp クライアントのIPアドレス
     * @return bool
     */
    public function isExceeded(string $clientIp): bool
    {
        return $this->readCount($clientIp) >= $this->getMaxPerHour();
    }

    /**
     * 登録回数を1つ進める
     *
     * @param string $clientIp クライアントのIPアドレス
     * @return void
     */
    public function hit(string $clientIp): void
    {
        Cache::write($this->buildKey($clientIp), $this->readCount($clientIp) + 1, self::CACHE_CONFIG);
    }

    /**
     * 現在の登録回数を取得する
     *
     * @param string $clientIp クライアントのIPアドレス
     * @return int
     */
    private function readCount(string $clientIp): int
    {
        return (int)Cache::read($this->buildKey($clientIp), self::CACHE_CONFIG);
    }

    /**
     * キャッシュキーを組み立てる
     *
     * IP アドレスをそのままキーにすると使用できない文字が混ざるため、
     * ハッシュ化する。
     *
     * @param string $clientIp クライアントのIPアドレス
     * @return string
     */
    private function buildKey(string $clientIp): string
    {
        return 'count_' . sha1($clientIp);
    }

    /**
     * 1時間あたりの上限件数を取得する
     *
     * @return int
     */
    private function getMaxPerHour(): int
    {
        return (int)Configure::read('BcMcp.registration.maxPerHour', 10);
    }
}
```

- [ ] **Step 4: テストを実行して通ることを確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter RegistrationRateLimiterTest --no-coverage`

Expected: 2 tests PASS。

- [ ] **Step 5: 設定と Cache 登録を追加する**

`plugins/bc-mcp/config/setting.php` の `BcMcp` 配下に追加する。

```php
        /**
         * 動的クライアント登録の制限
         *
         * maxPerHour: 同一IPから1時間に登録できる件数
         * unusedClientRetentionDays: 一度も認可に使われないクライアントの保持日数
         */
        'registration' => [
            'maxPerHour' => 10,
            'unusedClientRetentionDays' => 30,
        ],
```

同ファイルの `Log` 設定の下に Cache 設定を追加する。

```php
    'Cache' => [
        'bc_mcp_registration' => [
            'className' => \Cake\Cache\Engine\FileEngine::class,
            'duration' => '+1 hours',
            'path' => CACHE . 'bc_mcp' . DS,
            'prefix' => 'bc_mcp_registration_',
        ]
    ],
```

`plugins/bc-mcp/src/BcMcpPlugin.php` の `bootstrap()` に、Log と同じ形で追加する。

```php
        // setting.php の Cache 設定は baser-core の読み込み順の都合で
        // Cache へ登録されないため、ここで登録する
        if (!Cache::getConfig(RegistrationRateLimiter::CACHE_CONFIG)
            && Configure::check('Cache.' . RegistrationRateLimiter::CACHE_CONFIG)) {
            Cache::setConfig(
                RegistrationRateLimiter::CACHE_CONFIG,
                Configure::read('Cache.' . RegistrationRateLimiter::CACHE_CONFIG)
            );
        }
```

`use Cake\Cache\Cache;` と `use BcMcp\Service\RegistrationRateLimiter;` を追記する。

- [ ] **Step 6: register エンドポイントに組み込む**

`plugins/bc-mcp/src/Controller/Oauth2Controller.php` の `register()` の POST 判定の直後に追加する。

```php
        $rateLimiter = new RegistrationRateLimiter();
        $clientIp = (string)$this->request->clientIp();
        if ($rateLimiter->isExceeded($clientIp)) {
            return $this->response
                ->withStatus(429)
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'error' => 'invalid_request',
                    'error_description' => 'Too many client registrations. Please try again later.'
                ]));
        }
```

201 を返す直前に `$rateLimiter->hit($clientIp);` を実行する。`use BcMcp\Service\RegistrationRateLimiter;` を追記する。

- [ ] **Step 7: テストが枠に引っかからないようにする**

`Oauth2ConsentFlowTest` と `OAuth2ControllerDynamicClientRegistrationTest`、`OAuth2ControllerTest` の `setUp()` に追加する（同一プロセス内で多数の登録を行うため）。

```php
        // レート制限の枠がテスト間で持ち越されないようにする
        if (Cache::getConfig(RegistrationRateLimiter::CACHE_CONFIG)) {
            Cache::clear(RegistrationRateLimiter::CACHE_CONFIG);
        }
```

必要な `use Cake\Cache\Cache;` と `use BcMcp\Service\RegistrationRateLimiter;` を各テストクラスに追記する。

- [ ] **Step 8: スイート全体を実行する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --no-coverage`

Expected: 全 PASS。

- [ ] **Step 9: コミット**

```bash
git add plugins/bc-mcp/src plugins/bc-mcp/config/setting.php plugins/bc-mcp/tests
git commit -m "bc-mcp: クライアント登録にレート制限を入れる"
```

---

### Task 9: 未使用クライアントの掃除

**Files:**
- Modify: `plugins/bc-mcp/src/Command/Oauth2CleanupCommand.php:38-70`
- Create: `plugins/bc-mcp/tests/TestCase/Command/Oauth2CleanupCommandTest.php`

**Interfaces:**
- Consumes: `BcMcp.registration.unusedClientRetentionDays`（Task 8 で追加）
- Produces: `Oauth2CleanupCommand` が未使用クライアントも削除し、削除件数を出力する。

- [ ] **Step 1: コマンド名を確認する**

Run: `docker exec -w /var/www/html basercms bin/cake --help`

出力から bc-mcp のクリーンアップコマンドの呼び出し名を確認し、次のステップのテストで使う。

- [ ] **Step 2: 失敗するテストを書く**

`plugins/bc-mcp/tests/TestCase/Command/Oauth2CleanupCommandTest.php` を新規作成する。`$this->exec()` に渡すコマンド名は Step 1 で確認したものに置き換える。

```php
<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Command;

use BaserCore\TestSuite\BcTestCase;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;

/**
 * Oauth2CleanupCommand Test Case
 */
class Oauth2CleanupCommandTest extends BcTestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * 一度も使われていない古いクライアントを削除する
     *
     * @return void
     */
    public function testExecuteRemovesUnusedClients(): void
    {
        $clientsTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2Clients');
        $tokensTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2AccessTokens');

        // 古くて未使用のクライアント（削除される）
        $clientsTable->saveOrFail($clientsTable->newEntity([
            'client_id' => 'client_unused_old',
            'name' => 'Unused Old',
            'redirect_uris' => ['https://example.com/callback'],
            'grants' => ['authorization_code'],
            'scopes' => ['mcp:read'],
            'is_confidential' => false,
            'created' => new DateTime('-40 days'),
            'modified' => new DateTime('-40 days'),
        ]));

        // 古いが使用実績のあるクライアント（残る）
        $clientsTable->saveOrFail($clientsTable->newEntity([
            'client_id' => 'client_used_old',
            'name' => 'Used Old',
            'redirect_uris' => ['https://example.com/callback'],
            'grants' => ['authorization_code'],
            'scopes' => ['mcp:read'],
            'is_confidential' => false,
            'created' => new DateTime('-40 days'),
            'modified' => new DateTime('-40 days'),
        ]));
        $tokensTable->saveOrFail($tokensTable->newEntity([
            'token_id' => 'token_for_used_old',
            'client_id' => 'client_used_old',
            'user_id' => 1,
            'scopes' => 'mcp:read',
            'expires_at' => new DateTime('+1 hour'),
            'revoked' => false,
        ]));

        // 新しくて未使用のクライアント（残る）
        $clientsTable->saveOrFail($clientsTable->newEntity([
            'client_id' => 'client_unused_new',
            'name' => 'Unused New',
            'redirect_uris' => ['https://example.com/callback'],
            'grants' => ['authorization_code'],
            'scopes' => ['mcp:read'],
            'is_confidential' => false,
        ]));

        $this->exec('bc_mcp oauth2_cleanup');
        $this->assertExitSuccess();

        $this->assertNull($clientsTable->findByClientId('client_unused_old'));
        $this->assertNotNull($clientsTable->findByClientId('client_used_old'));
        $this->assertNotNull($clientsTable->findByClientId('client_unused_new'));
    }
}
```

`created` を明示的に過去日時で保存するには Timestamp ビヘイビアの上書きが必要になる場合がある。
`saveOrFail()` 後に値が現在時刻になっている場合は、保存後に `updateAll()` で `created` を直接更新する。

```php
        $clientsTable->updateAll(
            ['created' => new DateTime('-40 days')],
            ['client_id IN' => ['client_unused_old', 'client_used_old']]
        );
```

- [ ] **Step 3: テストを実行して失敗を確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter Oauth2CleanupCommandTest --no-coverage`

Expected: FAIL（`client_unused_old` が削除されず `assertNull` で落ちる）。

- [ ] **Step 4: コマンドに削除処理を追加する**

`plugins/bc-mcp/src/Command/Oauth2CleanupCommand.php` の `execute()` に、リフレッシュトークンの掃除の後へ追加する。

```php
            // 未使用クライアントのクリーンアップ
            $removedClients = $this->cleanUnusedClients();
            $io->success("一度も認可に使われていないクライアント {$removedClients} 件を削除しました");
```

同クラスに次のメソッドを追加する。

```php
    /**
     * 一度も認可に使われていない古いクライアントを削除する
     *
     * 動的クライアント登録は無認証で開いているため、使われないクライアントが
     * 溜まり続ける。アクセストークンの発行実績が無く、登録から一定期間が
     * 経過したものを削除する。
     *
     * @return int 削除した件数
     */
    private function cleanUnusedClients(): int
    {
        $retentionDays = (int)Configure::read('BcMcp.registration.unusedClientRetentionDays', 30);
        $clientsTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2Clients');
        $accessTokensTable = TableRegistry::getTableLocator()->get('BcMcp.Oauth2AccessTokens');

        $usedClientIds = $accessTokensTable->find()
            ->select(['client_id'])
            ->distinct(['client_id'])
            ->all()
            ->extract('client_id')
            ->toArray();

        $conditions = ['created <' => new DateTime('-' . $retentionDays . ' days')];
        if ($usedClientIds) {
            // 空配列を NOT IN に渡すと SQL が壊れるため、実績がある場合のみ付ける
            $conditions['client_id NOT IN'] = $usedClientIds;
        }

        return $clientsTable->deleteAll($conditions);
    }
```

`use Cake\Core\Configure;` と `use Cake\I18n\DateTime;` を追記する。

- [ ] **Step 5: テストを実行して通ることを確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --filter Oauth2CleanupCommandTest --no-coverage`

Expected: PASS。

- [ ] **Step 6: スイート全体を実行する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --no-coverage`

Expected: 全 PASS。

- [ ] **Step 7: コミット**

```bash
git add plugins/bc-mcp/src/Command/Oauth2CleanupCommand.php plugins/bc-mcp/tests/TestCase/Command/Oauth2CleanupCommandTest.php
git commit -m "bc-mcp: 未使用クライアントを掃除コマンドで削除する"
```

---

### Task 10: 接続要件の明記と最終確認

**Files:**
- Modify: `plugins/bc-mcp/templates/Admin/McpServerManager/index.php`（「AIエージェントでの設定方法」パネル）
- Test: スイート全体

**Interfaces:**
- Consumes: Task 1〜9 の成果
- Produces: なし

`plugins/bc-mcp/README.md` は baserCMS 共通のボイラープレートで、bc-mcp 固有の接続手順を持たない。
利用者向けの周知は、実際に接続情報を見る「MCPサーバー管理」画面に書く。

- [ ] **Step 1: 接続要件を管理画面に明記する**

`plugins/bc-mcp/templates/Admin/McpServerManager/index.php` の「AIエージェントでの設定方法」パネルの
`bca-data-list` に、手順2の後へ追加する。

```php
      <div class="bca-data-list__item">
        <div class="bca-data-list__item-label">接続の要件</div>
        <div class="bca-data-list__item-value">
          <ul>
            <li>認可方式は Authorization Code + PKCE（S256）のみに対応しています</li>
            <li>クライアントはパブリッククライアント（<code>token_endpoint_auth_method: none</code>）として登録されます</li>
            <li>リダイレクト先は https、または <code>127.0.0.1</code> / <code>[::1]</code> / <code>localhost</code> の http のみ登録できます</li>
            <li>クライアント登録は同一のIPアドレスから1時間に10件までです</li>
          </ul>
        </div>
      </div>
```

- [ ] **Step 2: スイート全体を実行する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BcMcp --no-coverage`

Expected: 全 PASS。ベースライン（215 tests）より増えていること。

- [ ] **Step 3: baser-core スイートに影響が無いか確認する**

Run: `docker exec -w /var/www/html basercms vendor/bin/phpunit --testsuite BaserCore --no-coverage`

Expected: 変更前と同じ結果。bc-mcp のみを変更しているため、差分が出た場合は原因を調査する。

- [ ] **Step 4: 手動確認の手順を記録する**

自動テストで担保できない項目を、確認結果と併せて PR の説明に書く。

- 実際の MCP クライアント（Claude / ChatGPT）から `/bc-mcp` を登録し、DCR → 同意 → トークン取得 → `tools/call` が通ること
- 同意画面で表示されるクライアント名とスコープ説明が正しいこと
- `config/.env` から `OAUTH2_ENC_KEY` を一時的に外すと、管理画面に警告が出て `/bc-mcp` が 503 になること

- [ ] **Step 5: コミット**

```bash
git add plugins/bc-mcp/templates/Admin/McpServerManager/index.php
git commit -m "bc-mcp: 管理画面に OAuth2 の接続要件を明記する"
```
