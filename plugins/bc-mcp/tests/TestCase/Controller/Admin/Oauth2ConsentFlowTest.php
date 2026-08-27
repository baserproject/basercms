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

        // Server::run() がリクエストの最後にセッションを close() するため、
        // Session::read() で再度アクセスすると再startされてデータが消える。
        // CakePHP 本体の SessionEquals/SessionHasKey 制約と同様に $_SESSION を直接参照する。
        $authRequest = Hash::get($_SESSION, 'BcMcp.authRequest');
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
        // Server::run() 後の Session::read() はデータを消してしまうため $_SESSION を直接参照する。
        $this->assertNull(Hash::get($_SESSION, 'BcMcp.authRequest'));
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
}
