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
use Cake\Event\EventInterface;

/**
 * Users Controller
 *
 * @property \BaserCore\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
	public $siteConfigs = [];
    
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
