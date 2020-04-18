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

	public function beforeFilter(EventInterface $event)
    {
		$this->siteConfigs['admin_list_num'] = 20;
		$this->request = $this->request->withParam('pass', ['num' => 20]);
    }

	/**
	 * ログインユーザーリスト
	 *
	 * 管理画面にログインすることができるユーザーの一覧を表示する
	 * - 新規登録画面への動線が存在する
	 * - カラムの定義：ID、名前、
	 * -
	 *
	 * [例]
	 * - list head
	 *	- add button
	 *
	 *
	 * - list view
	 *	- User.name
	 *  - User.mail
	 *  - User.zip
	 *  - User.pref
	 *  - User.addres
	 *
	 * - search input
	 *	- User.name
	 *	- User.name
	 *
	 * - pagination
	 * - view num
	 * @return void
	 */
    public function index()
    {
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
        if ($this->request->is('post')) {
            // $user = $this->Auth->identify();
            // var_dump($user);exit;
            // if ($user) {
            //     $this->Auth->setUser($user);
            //     return $this->redirect($this->Auth->redirectUrl());
            // }
            // $this->Flash->error(__('Invalid username or password, try again'));
        } else {
            // $userTable = TableRegistry::getTableLocator()->get('users');
            // // $user = $userTable->newEntity(
            // $user = $this->Users->newEntity(
            //     [
            //         'name' => 'test',
            //         'password' => 'password',
            //         'real_name_1' => 'test',
            //         'email' => 'admin@example.com',
            //         'user_group_id' => 1,
            //     ]
            );
            // var_dump($user);exit;
            // $userTable->save($user);
            // var_dump($userTable->save($user));
            // exit;
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

    }
}
