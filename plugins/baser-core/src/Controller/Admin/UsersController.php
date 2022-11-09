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

namespace BaserCore\Controller\Admin;

use BaserCore\Model\Entity\User;
use BaserCore\Service\Admin\UsersAdminServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\UsersService;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\Routing\Router;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class UsersController
 */
class UsersController extends BcAdminAppController
{

    /**
     * initialize
     *
     * ログインページ認証除外
     *
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * 管理画面へログインする
     *
     * @param UsersAdminServiceInterface $service
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function login(UsersAdminServiceInterface $service)
    {
        $this->set($service->getViewVarsForLogin($this->getRequest()));
        if ($this->Authentication->getResult()->isValid()) {
            $this->redirect(Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect')));
        }
    }

    /**
     * 代理ログイン
     *
     * 別のユーザにログインできる
     *
     * @param UsersServiceInterface $service
     * @param string|null
     * @return Response|void
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @unitTest
     * @noTodo
     */
    public function login_agent(UsersServiceInterface $service, $id): ?Response
    {
        // 特権確認
        if (BcUtil::isSuperUser() === false) {
            throw new ForbiddenException();
        }
        // 既に代理ログイン済み
        if (BcUtil::isAgentUser()) {
            $this->BcMessage->setError(__d('baser', '既に代理ログイン中のため失敗しました。'));
            return $this->redirect(['action' => 'index']);
        }
        /* @var UsersService * $service */
        $service->loginToAgent($this->request, $this->response, $id, $this->referer());
        return $this->redirect($this->Authentication->getLoginRedirect() ?? Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect')));
    }

    /**
     * 代理ログイン解除
     * @param UsersServiceInterface $service
     * @return Response
     * @unitTest
     * @noTodo
     * @checked
     */
    public function back_agent(UsersServiceInterface $service)
    {
        try {
            $redirectUrl = $service->returnLoginUserFromAgent($this->request, $this->response);
            $this->BcMessage->setInfo(__d('baser', '元のユーザーに戻りました。'));
            return $this->redirect($redirectUrl);
        } catch (\Exception $e) {
            $this->BcMessage->setError($e->getMessage());
            return $this->redirect($this->referer());
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
        $this->BcMessage->setInfo(__d('baser', 'ログアウトしました'));
        $this->redirect($this->Authentication->logout());
    }

    /**
     * ログインユーザーリスト
     *
     * 管理画面にログインすることができるユーザーの一覧を表示する
     *
     * @param UsersServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UsersServiceInterface $service)
    {
        $this->setViewConditions('User', ['default' => ['query' => [
            'limit' => BcSiteConfig::get('admin_list_num'),
            'sort' => 'id',
            'direction' => 'asc',
        ]]]);

        // EVENT Users.searchIndex
        $event = $this->dispatchLayerEvent('searchIndex', [
            'request' => $this->request
        ]);
        if ($event !== false) {
            $this->request = ($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult();
        }
        try {
            $entities = $this->paginate($service->getIndex($this->getRequest()->getQueryParams()));
        } catch (NotFoundException $e) {
            return $this->redirect(['action' => 'index']);
        }
        $this->set('users', $entities);
        $this->request = $this->request->withParsedBody($this->request->getQuery());
    }

    /**
     * ログインユーザー新規追加
     *
     * 管理画面にログインすることができるユーザーの各種情報を新規追加する
     *
     * @param UsersServiceInterface $service
     * @return Response|null|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UsersAdminServiceInterface $service)
    {
        if ($this->request->is('post')) {
            try {
                $user = $service->create($this->request->getData());
                // EVENT Users.afterAdd
                $this->dispatchLayerEvent('afterAdd', [
                    'user' => $user
                ]);
                $this->BcMessage->setSuccess(__d('baser', 'ユーザー「{0}」を追加しました。', $user->name));
                return $this->redirect(['action' => 'edit', $user->id]);
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $user = $e->getEntity();
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set($service->getViewVarsForAdd($user ?? $service->getNew()));
    }

    /**
     * ログインユーザー編集
     *
     * 管理画面にログインすることができるユーザーの各種情報を編集する
     *
     * @param UsersServiceInterface $service
     * @param string|null $id User id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(UsersAdminServiceInterface $service, $id = null)
    {
        if (!$id && empty($this->request->getData())) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }
        $user = $service->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            try {
                $user = $service->update($user, $this->request->getData());
                // EVENT Users.afterEdit
                $this->dispatchLayerEvent('afterEdit', [
                    'user' => $user
                ]);
                if($service->isSelf($id)) {
                    $service->reLogin($this->request, $this->response);
                }
                $this->BcMessage->setSuccess(__d('baser', 'ユーザー「{0}」を更新しました。', $user->name));
                return $this->redirect(['action' => 'edit', $user->id]);
            } catch (\Exception $e) {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set($service->getViewVarsForEdit($user));
    }

    /**
     * ログインユーザー削除
     *
     * 管理画面にログインすることができるユーザーを削除する
     *
     * @param UsersServiceInterface $service
     * @param string|null $id
     * @return Response|null|void
     * @throws RecordNotFoundException
     * @checked
     * @unitTest
     * @noTodo
     */
    public function delete(UsersServiceInterface $service, $id = null)
    {
        if (!$id) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod(['post', 'delete']);
        $user = $service->get($id);
        try {
            if ($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser', 'ユーザー: {0} を削除しました。', $user->name));
            }
        } catch (Exception $e) {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index']);
    }

}
