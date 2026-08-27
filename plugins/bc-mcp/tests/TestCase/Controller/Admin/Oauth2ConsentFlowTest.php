<?php
declare(strict_types=1);

namespace BcMcp\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\Utility\Hash;
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
     * セッションに保持されている認可リクエストのマップを取得する
     *
     * Server::run() 後の Session::read() はデータを消してしまうため、
     * $_SESSION を直接参照する（CakePHP 本体の SessionEquals/SessionHasKey
     * 制約と同様の作法）。
     *
     * @return array<string, mixed>
     */
    private function pendingAuthRequests(): array
    {
        return (array)Hash::get($_SESSION, 'BcMcp.authRequests', []);
    }

    /**
     * 直近の GET で発行された consent_id を取得する
     *
     * @return string
     */
    private function lastConsentId(): string
    {
        $pending = $this->pendingAuthRequests();
        return (string)array_key_last($pending);
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

        // 発行された consent_id をキーに、認可リクエストが1件だけ保持されている
        $pending = $this->pendingAuthRequests();
        $this->assertCount(1, $pending);
        $consentId = $this->lastConsentId();
        $this->assertNotSame('', $consentId);
        // フォームの hidden にも同じ consent_id が埋め込まれている
        $this->assertResponseContains('value="' . $consentId . '"');

        $authRequest = $pending[$consentId];
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
        $this->assertSame([], $this->pendingAuthRequests());
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
        $consentId = $this->lastConsentId();

        // IntegrationTestTrait は次のリクエストのセッションを都度作り直すため、
        // GET でセッションに書き込んだ認可リクエストを $_SESSION から引き継ぐ。
        $this->session($_SESSION);
        $this->enableCsrfToken();

        // 攻撃者のリダイレクト先をクエリとボディの両方に混ぜる。consent_id は
        // 正規の同意画面のものを使う（consent_id 以外はフォームの値として
        // 読まれないことを確認するのが本テストの目的）
        $this->post('/bc-mcp/oauth2/authorize?' . http_build_query([
            'client_id' => 'client_attacker',
            'redirect_uri' => 'https://attacker.example.com/callback',
        ]), [
            'action' => 'approve',
            'consent_id' => $consentId,
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
        $consentId = $this->lastConsentId();

        // IntegrationTestTrait は次のリクエストのセッションを都度作り直すため、
        // GET でセッションに書き込んだ認可リクエストを $_SESSION から引き継ぐ。
        $this->session($_SESSION);
        $this->enableCsrfToken();
        $this->post('/bc-mcp/oauth2/authorize', ['action' => 'deny', 'consent_id' => $consentId]);

        $this->assertResponseCode(302);
        $location = $this->_response->getHeaderLine('Location');
        $this->assertStringContainsString('error=access_denied', $location);
        $this->assertStringContainsString('state=test-state', $location);
        $this->assertStringContainsString('iss=', $location);
    }

    /**
     * 別タブで開いた同意画面の consent_id では、先に開いた画面の認可を発行できない
     *
     * 「セッション上の認可リクエストが GET のたびに無条件で上書きされる」経路を
     * 塞いだことの回帰テスト。consent_id ごとに認可リクエストを区別できるため、
     * 別クライアントで GET し直しても、先に開いた同意画面の consent_id は
     * 先に開いた画面のクライアント向けのままであることを確認する。
     *
     * @return void
     */
    public function testConcurrentAuthorizeGetDoesNotSwapPendingConsent(): void
    {
        $legitimate = $this->registerClient();
        // 攻撃者クライアントは正規クライアントとは別の redirect_uri で登録する。
        // 同じ URI のままだと、実装が単一キー上書き（＝すり替え可能）に退行して
        // 攻撃者クライアント向けの認可コードが発行されてしまっても、リダイレクト
        // 先の URI 自体は変わらないため assertStringStartsWith だけでは退行を
        // 検出できない。別 URI にすることで、すり替わりが起きれば攻撃者の URI
        // へ飛ぶという形で確実に検出できるようにする。
        $attacker = $this->registerClient([
            'client_name' => 'Attacker Client',
            'redirect_uris' => ['https://attacker.example.com/callback'],
        ]);
        $this->loginAdmin($this->getRequest());

        // タブ1: 正規のクライアントで同意画面を開く
        $this->get('/bc-mcp/oauth2/authorize?' . $this->authorizeQuery($legitimate['client_id']));
        $this->assertResponseOk();
        $legitimateConsentId = $this->lastConsentId();

        // タブ2扱い: 同じセッションのまま、別クライアントで同意画面を開き直す
        // (攻撃者がトップレベル遷移で踏ませる GET を模す)
        // authorizeQuery() は既定で self::REDIRECT_URI を使うため、攻撃者
        // クライアントの登録済み redirect_uri に合わせて上書きする。
        $this->session($_SESSION);
        $this->get('/bc-mcp/oauth2/authorize?' . $this->authorizeQuery($attacker['client_id'], [
            'redirect_uri' => 'https://attacker.example.com/callback',
        ]));
        $this->assertResponseOk();

        // タブ1に残っていた consent_id は、依然として正規クライアントの
        // 認可リクエストを指したまま消費できる（別クライアントに差し替わらない）
        $this->session($_SESSION);
        $this->enableCsrfToken();
        $this->post('/bc-mcp/oauth2/authorize', [
            'action' => 'approve',
            'consent_id' => $legitimateConsentId,
        ]);

        $this->assertResponseCode(302);
        $location = $this->_response->getHeaderLine('Location');
        $this->assertStringStartsWith(self::REDIRECT_URI, $location);
        $this->assertStringNotContainsString('attacker.example.com', $location);
    }

    /**
     * consent_id が無い、または一致しない POST は 400 になる
     *
     * @return void
     */
    public function testApproveWithUnknownConsentIdReturnsBadRequest(): void
    {
        $client = $this->registerClient();
        $this->loginAdmin($this->getRequest());

        $this->get('/bc-mcp/oauth2/authorize?' . $this->authorizeQuery($client['client_id']));
        $this->assertResponseOk();

        $this->session($_SESSION);
        $this->enableCsrfToken();
        // 存在しない consent_id を指定する
        $this->post('/bc-mcp/oauth2/authorize', [
            'action' => 'approve',
            'consent_id' => 'unknown-consent-id',
        ]);

        $this->assertResponseCode(400);
        $this->assertResponseNotContains('code=');
    }

    /**
     * 同じ認可リクエストで承認 POST を2回投げると、2回目は 400 になる（ワンショット破棄の回帰テスト）
     *
     * @return void
     */
    public function testApproveTwiceWithSameConsentIdFailsOnSecondAttempt(): void
    {
        $client = $this->registerClient();
        $this->loginAdmin($this->getRequest());

        $this->get('/bc-mcp/oauth2/authorize?' . $this->authorizeQuery($client['client_id']));
        $this->assertResponseOk();
        $consentId = $this->lastConsentId();

        $this->session($_SESSION);
        $this->enableCsrfToken();
        $this->post('/bc-mcp/oauth2/authorize', ['action' => 'approve', 'consent_id' => $consentId]);
        $this->assertResponseCode(302);
        $firstLocation = $this->_response->getHeaderLine('Location');
        $this->assertStringContainsString('code=', $firstLocation);

        // 同じ consent_id で再度 POST しても、既に消費済みのため認可コードは発行されない
        $this->session($_SESSION);
        $this->enableCsrfToken();
        $this->post('/bc-mcp/oauth2/authorize', ['action' => 'approve', 'consent_id' => $consentId]);
        $this->assertResponseCode(400);
        $this->assertResponseNotContains('code=');
    }

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
}
