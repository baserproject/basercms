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
		$this->passedArgs['num'] = 20;
	}

	/**
	 * ユーザーリスト
	 *
	 * @return void
	 */
    public function index()
    {
		$default = ['named' => ['num' => $this->siteConfigs['admin_list_num']]];
		$this->setViewConditions('User', ['default' => $default]);
		$users = $this->paginate($this->Users->find('all')
			->limit($this->passedArgs['num'])
			->order('Users.user_group_id, Users.id')
		);
        $this->set([
            'users' => $users,
            '_serialize' => ['users']
        ]);
        $this->set('title', 'ユーザー一覧');
    }
}
