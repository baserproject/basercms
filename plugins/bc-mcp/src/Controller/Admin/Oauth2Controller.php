<?php
declare(strict_types=1);

namespace BcMcp\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BcMcp\Lib\OAuth2Util;
use BcMcp\OAuth2\Entity\User;
use BcMcp\OAuth2\Exception\OAuth2ConfigurationException;
use BcMcp\OAuth2\Service\OAuth2Service;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;

/**
 * Admin OAuth2 Controller
 *
 * OAuth2認証エンドポイントを提供（認証が必要なエンドポイントのみ）
 */
class Oauth2Controller extends BcAdminAppController
{

    /**
     * OAuth2サービス
     *
     * @var OAuth2Service|null
     */
    private ?OAuth2Service $oauth2Service = null;

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
        try {
            $this->oauth2Service = new OAuth2Service();
        } catch (OAuth2ConfigurationException $e) {
            // 設定不備は beforeFilter で 503 として返す
            $this->oauth2ConfigError = $e;
        }
        $this->loadComponent('FormProtection');
        $this->FormProtection->setConfig('validate', false);
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

    /**
     * OPTIONSリクエスト対応
     *
     * @return Response
     */
    public function options(): Response
    {
        return $this->response->withStatus(200);
    }

    /**
     * 認可リクエストを保持するセッションキー
     *
     * 同時に開かれた複数の同意画面(別タブでの GET など)を区別できるよう、
     * 単一の値ではなく consent_id => AuthorizationRequest のマップとして保持する。
     * これにより「タブ1で開いた同意画面のまま、セッション上の認可リクエストだけが
     * 別クライアントのものにすり替わる」経路を防ぐ。
     */
    public const SESSION_AUTH_REQUESTS = 'BcMcp.authRequests';

    /**
     * セッションに保持する認可リクエストの最大件数
     *
     * 同意画面を開いたまま放置される（GET だけして POST しない）ケースが
     * 積み重なってもセッションが際限なく肥大化しないよう、上限を超えた
     * 分は古いものから破棄する。
     */
    private const MAX_PENDING_AUTH_REQUESTS = 5;

    /**
     * 認可エンドポイント
     *
     * Authorization Code Grant の開始点。GET で認可リクエストを検証して
     * セッションへ保持し、同意画面にはセッションの内容だけを表示する。
     * baserCMS の管理画面認証が必要。
     *
     * @return Response|\Psr\Http\Message\ResponseInterface
     */
    public function authorize()
    {
        // ユーザーがログインしているかチェック
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
        // POSTリクエストの場合は、クエリもボディも読まずセッションの認可
        // リクエストのみで処理を完結させる(同意画面と実際の発行内容の
        // すり替えを防ぐため)
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
            // getMessage() は invalid_request 系で共通の定型文になるため、
            // 具体的な原因（hint）が有れば error_description に付記する。
            $description = $e->getMessage();
            if ($e->getHint()) {
                $description .= ' ' . $e->getHint();
            }
            return $this->response
                ->withStatus(400)
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'error' => $e->getErrorType(),
                    'error_description' => $description,
                ]));
        }

        // 同意画面に見せた内容と、実際に発行される権限を一致させるため、
        // 検証済みの認可リクエストをセッションへ保持する。GET のたびに
        // 単一キーを上書きすると、別タブ・並行タブでの GET によって
        // 表示中の同意画面と紐づく認可リクエストがすり替わってしまうため、
        // 推測不可能な consent_id を発行してマップに追加する形で保持する。
        $session = $this->request->getSession();
        $pending = (array)$session->read(self::SESSION_AUTH_REQUESTS);
        $consentId = bin2hex(random_bytes(16));
        $pending[$consentId] = $authRequest;
        // 上限を超えたら古いものから破棄する
        if (count($pending) > self::MAX_PENDING_AUTH_REQUESTS) {
            $pending = array_slice($pending, -self::MAX_PENDING_AUTH_REQUESTS, null, true);
        }
        $session->write(self::SESSION_AUTH_REQUESTS, $pending);

        $this->set([
            'client' => $authRequest->getClient(),
            'scopes' => $authRequest->getScopes(),
            'user' => $user,
            'consentId' => $consentId,
        ]);

        return $this->render('authorize');
    }

    /**
     * 認可を完了させる
     *
     * クエリもボディも認可の内容としては読まず、GET 時に検証してセッションへ
     * 保持した認可リクエストのみを使う。ボディから読むのは、どの同意画面の
     * ものかを指す consent_id のみで、スコープやリダイレクト先といった認可の
     * 内容は一切含まれない。これにより同意画面で見せた権限と、実際に
     * 発行される権限がすり替わる余地を無くす。
     *
     * @return \Cake\Http\Response|\Psr\Http\Message\ResponseInterface
     */
    private function completeAuthorization()
    {
        $session = $this->request->getSession();
        $pending = (array)$session->read(self::SESSION_AUTH_REQUESTS);
        $consentId = (string)$this->request->getData('consent_id');
        $authRequest = $pending[$consentId] ?? null;
        // 認可コードの二重発行や、他の同意画面の認可リクエストの流用を防ぐため、
        // 消費した consent_id は結果によらず必ずセッションから破棄する。
        unset($pending[$consentId]);
        $session->write(self::SESSION_AUTH_REQUESTS, $pending);

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
}
