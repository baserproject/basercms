<?php
declare(strict_types=1);

namespace BcMcp\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BcMcp\Lib\OAuth2Util;
use BcMcp\OAuth2\Entity\User;
use BcMcp\OAuth2\Service\OAuth2Service;
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
     * @var OAuth2Service
     */
    private OAuth2Service $oauth2Service;

    /**
     * 初期化
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->oauth2Service = new OAuth2Service();
        $this->loadComponent('FormProtection');
        $this->FormProtection->setConfig('validate', false);
        // CORS設定
        $this->response = $this->response->withHeader('Access-Control-Allow-Origin', '*');
        $this->response = $this->response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $this->response = $this->response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, MCP-Protocol-Version');
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
     */
    public const SESSION_AUTH_REQUEST = 'BcMcp.authRequest';

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
}
