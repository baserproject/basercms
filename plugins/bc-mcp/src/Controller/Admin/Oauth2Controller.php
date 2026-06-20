<?php
declare(strict_types=1);

namespace BcMcp\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BcMcp\Lib\OAuth2Util;
use BcMcp\OAuth2\Entity\User;
use BcMcp\OAuth2\Service\OAuth2Service;
use BcMcp\OAuth2\Repository\OAuth2ClientRepository;
use Cake\Http\Response;

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
     * 認可エンドポイント
     * Authorization Code Grantの開始点
     * baserCMSのAdmin認証が必要
     *
     * @return Response|\Psr\Http\Message\ResponseInterface
     */
    public function authorize()
    {
        try {
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

            $request = $this->request;

            // 必須パラメータをチェック
            $clientId = $request->getQuery('client_id');
            $responseType = $request->getQuery('response_type');
            $redirectUri = $request->getQuery('redirect_uri');
            $state = $request->getQuery('state');
            $scope = $request->getQuery('scope');
            if (!$scope) {
                $scope = 'mcp:read mcp:write'; // デフォルトスコープ
            }

            if (!$clientId || !$responseType || !$redirectUri) {
                return $this->response
                    ->withStatus(400)
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'error' => 'invalid_request',
                        'error_description' => 'Missing required parameters: client_id, response_type, redirect_uri'
                    ]));
            }

            if ($responseType !== 'code') {
                return $this->response
                    ->withStatus(400)
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'error' => 'unsupported_response_type',
                        'error_description' => 'Only response_type=code is supported'
                    ]));
            }

            // クライアントの妥当性をチェック
            $clientRepository = new OAuth2ClientRepository();
            $client = $clientRepository->getClientEntity($clientId);

            if (!$client) {
                $siteUrl = env('SITE_URL', 'https://localhost');
                $baseUrl = rtrim($siteUrl, '/');
                $resourceMetadataUrl = $baseUrl . '/.well-known/oauth-protected-resource/bc-mcp';
                return $this->response
                    ->withStatus(401)
                    ->withType('application/json')
                    ->withHeader('WWW-Authenticate', 'Bearer resource_metadata="' . $resourceMetadataUrl . '"')
                    ->withStringBody(json_encode([
                        'error' => 'invalid_client',
                        'error_description' => 'Client registration required. Please register a new client.'
                    ]));
            }

            // リダイレクトURIの妥当性をチェック
            if (!in_array($redirectUri, $client->getRedirectUri())) {
                return $this->response
                    ->withStatus(400)
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'error' => 'invalid_redirect_uri',
                        'error_description' => 'Invalid redirect_uri'
                    ]));
            }

            // POSTリクエストの場合は認可処理
            if ($this->request->is('post')) {
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

                    return $server->completeAuthorizationRequest($authRequest, $this->response);
                } elseif ($action === 'deny') {
                    // アクセス拒否
                    $params = [
                        'error' => 'access_denied',
                        'error_description' => 'The user denied the request'
                    ];
                    if ($state) {
                        $params['state'] = $state;
                    }

                    $redirectUrl = $redirectUri . '?' . http_build_query($params);
                    return $this->redirect($redirectUrl);
                }
            }

            // 認可画面を表示
            $this->set([
                'client' => $client,
                'clientId' => $clientId,
                'redirectUri' => $redirectUri,
                'scope' => $scope,
                'state' => $state,
                'user' => $user
            ]);

            return $this->render('authorize');

        } catch (\Exception $exception) {
            return $this->response
                ->withStatus(500)
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'error' => 'server_error',
                    'error_description' => 'An unexpected error occurred.',
                    'message' => $exception->getMessage()
                ]));
        }
    }
}
