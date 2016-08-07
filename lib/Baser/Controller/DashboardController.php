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

/**
 * ダッシュボードコントローラー
 * 管理者ログインやメンバーログインのダッシュボードページを表示する
 *
 * @package Baser.Controller
 */
class DashboardController extends AppController {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'Dashboard';

/**
 * モデル
 *
 * @var array
 */
	public $uses = array('Dblog', 'User', 'Menu', 'Page');

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = array('BcTime', 'Js');

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');

/**
 * ぱんくずナビ
 *
 * @var string
 */
	public $crumbs = array();

/**
 * サブメニューエレメント
 *
 * @var array
 */
	public $subMenuElements = array();

/**
 * [ADMIN] 管理者ダッシュボードページにajaxでデータを取得する
 *
 * @return void
 */
	public function admin_ajax_dblog_index() {
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('Dblog', array('default' => $default, 'action' => 'admin_index'));
		$this->paginate = array(
				'order' => array('Dblog.created '=> 'DESC', 'Dblog.id' => 'DESC'),
				'limit' => $this->passedArgs['num']
		);
		$this->set('viewDblogs', $this->paginate('Dblog'));
	}

/**
 * [ADMIN] 管理者ダッシュボードページを表示する
 *
 * @return void
 */
	public function admin_index() {
		$this->pageTitle = '管理者ダッシュボード';
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('Dblog', array('default' => $default));

		$this->paginate = array(
				'order' => array('Dblog.created '=> 'DESC', 'Dblog.id' => 'DESC'),
				'limit' => $this->passedArgs['num']
		);

		$this->set('viewDblogs', $this->paginate('Dblog'));
		$publishedPages = $this->Page->find('count', array('conditions' => $this->Page->Content->getConditionAllowPublish()));
		$unpublishedPages = $this->Page->find('count', array('conditions' => $this->Page->Content->getConditionAllowPublish()));
		$totalPages = $publishedPages + $unpublishedPages;
		$this->set(compact('publishedPages', 'unpublishedPages', 'totalPages'));
		$this->help = 'dashboard_index';
	}

/**
 * [ADMIN] 最近の動きを削除
 * 
 * @return void
 */
	public function admin_del() {
		if ($this->Dblog->deleteAll('1 = 1')) {
			$this->setMessage('最近の動きのログを削除しました。');
		} else {
			$this->setMessage('最近の動きのログ削除に失敗しました。', true);
		}
		$this->redirect(array('action' => 'index'));
	}

}
