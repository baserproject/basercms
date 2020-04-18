<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          http://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

/**
 * Users Controller
 *
 * @property \BaserCore\Model\Table\UsersTable $Users
 */
class UsersController extends BcAdminAppController
{
	public $siteConfigs = [];

    /**
     * initialize
     * ログインページ認証除外
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * Before Filter
     * @param EventInterface $event
     * @return \Cake\Http\Response|void|null
     */
	public function beforeFilter(EventInterface $event)
    {
        // TODO 取り急ぎ動作させるためのコード
        // >>>
		$this->siteConfigs['admin_list_num'] = 20;
		$this->request = $this->request->withParam('pass', ['num' => 20]);
		// <<<
    }

	/**
	 * ログインユーザーリスト
	 *
	 * 管理画面にログインすることができるユーザーの一覧を表示する
	 *
	 * - list view
     *  - User.id
	 *	- User.name
     *  - User.nickname
     *  - User.user_group_id
     *  - User.real_name_1 && User.real_name_2
     *  - User.created && User.modified
	 *
	 * - search input
	 *	- User.user_group_id
	 *
	 * - pagination
	 * - view num
     *
	 * @return void
	 */
    public function index()
    {
        var_dump($_SESSION);
        exit;

		$default = ['named' => ['num' => $this->siteConfigs['admin_list_num']]];
		$this->setViewConditions('User', ['default' => $default]);
		$users = $this->paginate(
		    $this->Users->find('all')
			    ->limit($this->request->getParam('pass')['num'])
			    ->order('Users.user_group_id, Users.id')
		);
        $this->set([
            'users' => $users,
            '_serialize' => ['users']
        ]);
        $this->set('title', 'ユーザー一覧');
    }

    /**
     * 管理画面へログインする
	 * - link
     *	- パスワード再発行
     *
     * - viewVars
     *  - title
	 *
	 * - input
	 *	- User.name or User.email
     *	- User.password
     *  - remember login
     *  - submit
     *
     * @return void
     */
    public function login()
    {
        $this->set('title', 'ログイン');
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $target = $this->Authentication->getLoginRedirect() ?? env('BC_BASER_CORE_PATH') . env('BC_ADMIN_PREFIX') . '/';
            return $this->redirect($target);
        }
        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error('Invalid username or password');
        }
    }

    /**
     * ログイン状態のセッションを破棄する
     *
     * - redirect
     *   - login
     * @return void
     */
    public function logout()
    {
        $this->Authentication->logout();
        return $this->redirect(['action' => 'login']);
    }
}
