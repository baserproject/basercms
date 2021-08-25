<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Api;

use Authentication\Authenticator\JwtAuthenticator;
use Authentication\Authenticator\ResultInterface;
use Authentication\Controller\Component\AuthenticationComponent;
use BaserCore\Controller\AppController;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcApiUtil;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Firebase\JWT\JWT;

/**
 * Class BcApiController
 * @package BaserCore\Controller\Api
 * @property AuthenticationComponent $Authentication
 */
class BcApiController extends AppController
{

    /**
     * Initialize
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication.Authentication');
        $this->Security->setConfig('validatePost', false);
    }

    /**
     * トークンを取得する
     * @param ResultInterface $result
     * @return array
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
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        if(in_array($this->getRequest()->getParam('action'), $this->Authentication->getUnauthenticatedActions())) {
            return;
        }

        // ユーザーの有効チェック
        $user = $this->Authentication->getResult()->getData();
        if($user && !$this->loadModel('BaserCore.Users')->find()->where(['id' => $user->id, 'status' => true])->count()){
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

}
