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

use BaserCore\Controller\AppController;
use Cake\Event\Event;

/**
 * Users Controller
 *
 * @property \BaserCore\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
	public $siteConfigs = [];
	public function beforeFilter(Event $event) {
		// ダミーデータ
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
}
