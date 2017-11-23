<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

class DblogsController extends AppController {

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];
	
/**
 * 一覧を取得
 */
	public function admin_ajax_index() {
		$this->autoLayout = false;
		$default = ['named' => ['num' => $this->siteConfigs['admin_list_num']]];
		$this->setViewConditions('Dblog', ['default' => $default, 'action' => 'admin_index']);
		$this->paginate = [
			'order' => ['Dblog.created '=> 'DESC', 'Dblog.id' => 'DESC'],
			'limit' => $this->passedArgs['num']
		];
		$this->set('dblogs', $this->paginate('Dblog'));
	}

/**
 * [ADMIN] 最近の動きを削除
 *
 * @return void
 */
	public function admin_del() {
		$this->_checkSubmitToken();
		if ($this->Dblog->deleteAll('1 = 1')) {
			$this->setMessage('最近の動きのログを削除しました。');
		} else {
			$this->setMessage('最近の動きのログ削除に失敗しました。', true);
		}
		$this->redirect(['controller' => 'dashboard', 'action' => 'index']);
	}
	
}
