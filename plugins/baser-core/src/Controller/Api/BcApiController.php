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

use Authentication\Authenticator\AuthenticationRequiredException;
use Authentication\Authenticator\JwtAuthenticator;
use Authentication\Authenticator\ResultInterface;
use Authentication\Controller\Component\AuthenticationComponent;
use BaserCore\Controller\AppController;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Error\BcException;
use BaserCore\Utility\BcApiUtil;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Controller\Exception\AuthSecurityException;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\TableRegistry;

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

        if (in_array($this->getRequest()->getParam('action'), $this->Authentication->getUnauthenticatedActions())) {
            return;
        }

        // 管理画面APIが許可されていない場合は弾く
        // ただし、同じリファラからのアクセスは、Webブラウザの管理画面からのAJAXとして通す
        if (!filter_var(env('USE_CORE_ADMIN_API', false), FILTER_VALIDATE_BOOLEAN)) {
            if (!BcUtil::isSameReferrerAsCurrent()) {
                return $this->response->withStatus(401);
            }
        }

        // ユーザーの有効チェック
        if (!$this->isAvailableUser()) {
            return $this->response->withStatus(401);
        }

        // トークンタイプチェック
        $auth = $this->Authentication->getAuthenticationService()->getAuthenticationProvider();
        if ($auth instanceof JwtAuthenticator) {
            $payload = $auth->getPayload();
            if ($payload->token_type !== 'access_token' && $this->getRequest()->getParam('action') !== 'refresh_token') {
                $this->setResponse($this->response->withStatus(401));
            }
        }
    }

    /**
     * 認証が必要なAPIを利用可能かどうか判定
     *
     * @return bool
     */
    public function isAdminApiEnabled()
    {
        if (!filter_var(env('USE_CORE_ADMIN_API', false), FILTER_VALIDATE_BOOLEAN)) {
            return false;
        }
        return (bool) $this->Authentication->getIdentity();
    }

    /**
     * ログインユーザーが有効か判定する
     *
     * サービスクラス、または、テーブルクラスにおいて、isAvailable / get メソッドが
     * 存在するかを確認し、あれば実行し、その結果を返す。
     * ない場合は、 true を返却する
     *
     * @return bool
     */
    public function isAvailableUser(): bool
    {
        // ユーザーの有効チェック
        $user = $this->Authentication->getResult()->getData();
        if(!$user) return false;

        $prefix = $this->getRequest()->getParam('prefix');
        $userModel = Configure::read("BcPrefixAuth.{$prefix}.userModel");

        // サービスクラスチェック
        [$plugin, $model] = pluginSplit($userModel);
        if(!$plugin) throw new BcException(__d('baser_core', 'BcPrefixAuth の userModel の設定ではプラグイン記法を利用してください。'));

        $serviceName = "{$plugin}\\Service\\{$model}ServiceInterface";
        if (interface_exists($serviceName)) {
            $service = $this->getService($serviceName);
            if (method_exists($service, 'isAvailable')) {
                return $service->isAvailable($user->id);
            }
            if (method_exists($service, 'get')) {
                return (bool)$service->get($user->id);
            }
        }

        // テーブルクラスチェック
        $tableName = "{$plugin}\\Model\\Table\\{$model}Table";
        if(class_exists($tableName)) {
            $table = TableRegistry::getTableLocator()->get($userModel);
            if(method_exists($table, 'isAvailable')) {
                return $table->isAvailable($user->id);
            }
            if(method_exists($table, 'get')) {
                return (bool) $table->get($user->id);
            }
        }
        throw new BcException(__d('baser_core', 'BcPrefixAuth の userModel にユーザーの存在確認メソッドが存在しません。'));
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
