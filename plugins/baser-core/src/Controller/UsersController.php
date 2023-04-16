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

namespace BaserCore\Controller;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Service\Admin\UsersAdminServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use Cake\Core\Configure;
use Cake\Routing\Router;

/**
 * UsersController
 */
class UsersController extends BcFrontAppController
{

    /**
     * initialize
     *
     * ログインページ認証除外
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        if(Configure::read('BcPrefixAuth.Front.disabled')) $this->notFound();
        $this->loadComponent('Authentication.Authentication', [
            'logoutRedirect' => Router::url(Configure::read("BcPrefixAuth.Front.loginAction"), true),
        ]);
        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * ログインする
     *
     * @param UsersAdminServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function login(UsersAdminServiceInterface $service)
    {
        $this->set($service->getViewVarsForLogin($this->getRequest()));
        $target = $this->Authentication->getLoginRedirect() ?? Router::url(Configure::read('BcPrefixAuth.Front.loginRedirect'));

        // EVENT Users.beforeLogin
        $event = $this->dispatchLayerEvent('beforeLogin', [
            'user' => $this->request
        ]);
        if ($event !== false) {
            $this->request = ($event->getResult() === null || $event->getResult() === true)? $event->getData('user') : $event->getResult();
        }

        if ($this->request->is('post')) {
            $result = $this->Authentication->getResult();
            if ($result->isValid()) {
                $user = $result->getData();
                // EVENT Users.afterLogin
                $this->dispatchLayerEvent('afterLogin', [
                    'user' => $user,
                    'loginRedirect' => $target
                ]);
                $service->removeLoginKey($user->id);
                if ($this->request->is('ssl') && $this->request->getData('saved')) {
                    // 自動ログイン保存
                    $this->response = $service->setCookieAutoLoginKey($this->response, $user->id);
                }
                $this->BcMessage->setInfo(__d('baser_core', 'ようこそ、{0}さん。', $user->getDisplayName()));
                return $this->redirect($target);
            } else {
                $this->BcMessage->setError(__d('baser_core', 'Eメール、または、パスワードが間違っています。'));
            }
        } else {
            $result = $this->Authentication->getResult();
            if ($result->isValid()) {
                return $this->redirect($target);
            }
        }
    }

    /**
     * ログイン状態のセッションを破棄する
     *
     * @param UsersServiceInterface $service
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function logout(UsersServiceInterface $service)
    {
        /* @var User $user */
        $user = $this->Authentication->getIdentity();
        $service->logout($this->request, $this->response, $user->id);
        $this->BcMessage->setInfo(__d('baser_core', 'ログアウトしました'));
        $this->redirect($this->Authentication->logout());
    }

}
