<?php
declare(strict_types=1);

namespace BcMcp\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BcMcp\Lib\OAuth2Util;
use BcMcp\OAuth2\Entity\User;
use BcMcp\OAuth2\Service\OAuth2Service;
use Cake\Http\Response;
use League\OAuth2\Server\Exception\OAuthServerException;

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

        // POSTリクエストの場合は認可処理（この段階の実装は次のタスクで置き換える）
        if ($this->request->is('post')) {
            $redirectUri = $this->request->getQuery('redirect_uri');
            $state = $this->request->getQuery('state');
            $action = $this->request->getData('action');

            if ($action === 'approve') {
                $server = $this->oauth2Service->getAuthorizationServer();

                // PSR-7リクエストを作成（クエリパラメータとPOSTデータの両方を含む）
                $psrRequest = OAuth2Util::createPsr7Request($this->request);

                // 認可リクエストを検証（PKCEパラメータも含む）
                $authRequest = $server->validateAuthorizationRequest($psrRequest);

                $userEntity = new User();
                $userEntity->setIdentifier($user->getIdentifier());
                $authRequest->setUser($userEntity);
                $authRequest->setAuthorizationApproved(true);

                $authResponse = $server->completeAuthorizationRequest($authRequest, $this->response);

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
            } elseif ($action === 'deny') {
                // アクセス拒否
                $params = [
                    'error' => 'access_denied',
                    'error_description' => 'The user denied the request'
                ];
                if ($state) {
                    $params['state'] = $state;
                }

                // エラー応答も認可レスポンスであるため issuer を付与する（RFC 9207）
                $redirectUrl = OAuth2Util::addIssuerToUrl(
                    $redirectUri . '?' . http_build_query($params),
                    OAuth2Util::getIssuer($this->request)
                );
                return $this->redirect($redirectUrl);
            }
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
}
