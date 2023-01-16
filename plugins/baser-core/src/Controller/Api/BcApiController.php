<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Api;

use Authentication\Authenticator\JwtAuthenticator;
use Authentication\Authenticator\ResultInterface;
use Authentication\Controller\Component\AuthenticationComponent;
use BaserCore\Controller\AppController;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcApiUtil;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;

/**
 * Class BcApiController
 * @property AuthenticationComponent $Authentication
 */
class BcApiController extends AppController
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Initialize
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();

        if(!filter_var(env('USE_API', false), FILTER_VALIDATE_BOOLEAN)) {
            if($this->getRequest()->is('ajax')) {
                $siteDomain = BcUtil::getCurrentDomain();
                if (empty($_SERVER['HTTP_REFERER'])) {
                    throw new ForbiddenException(__d('baser', 'Web APIは許可されていません。'));
                }
                $refererDomain = BcUtil::getDomain($_SERVER['HTTP_REFERER']);
                if (!preg_match('/^' . preg_quote($siteDomain, '/') . '/', $refererDomain)) {
                    throw new ForbiddenException(__d('baser', 'Web APIは許可されていません。'));
                }
            } else {
                throw new ForbiddenException(__d('baser', 'Web APIは許可されていません。'));
            }
        }
        $this->loadComponent('Authentication.Authentication');
        $this->Security->setConfig('validatePost', false);
    }

    /**
     * トークンを取得する
     * @param ResultInterface $result
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAccessToken(ResultInterface $result): array
    {
        if ($result->isValid()) {
            return BcApiUtil::createAccessToken($result->getData()->id);
        } else {
            return [];
        }
    }

    /**
     * Before Filter
     * @param EventInterface $event
     * @return \Cake\Http\Response|void|null
     * @throws \Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        if(in_array($this->getRequest()->getParam('action'), $this->Authentication->getUnauthenticatedActions())) {
            return;
        }

        // ユーザーの有効チェック
        $user = $this->Authentication->getResult()->getData();
        $usersService = $this->getService(UsersServiceInterface::class);
        if($user && !$usersService->isAvailable($user->id)){
            $this->setResponse($this->response->withStatus(401));
            return;
        }

        // トークンタイプチェック
        $auth = $this->Authentication->getAuthenticationService()->getAuthenticationProvider();
        if($auth instanceof JwtAuthenticator){
            $payload = $auth->getPayload();
            if($payload->token_type !== 'access_token' && $this->getRequest()->getParam('action') !== 'refresh_token') {
                $this->setResponse($this->response->withStatus(401));
            }
        }
    }

    /**
     * Before render
     * 日本語を Unicode エスケープしないようにする
     * @param EventInterface $event
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        $this->viewBuilder()->setOption('jsonOptions', JSON_UNESCAPED_UNICODE);
    }

}
