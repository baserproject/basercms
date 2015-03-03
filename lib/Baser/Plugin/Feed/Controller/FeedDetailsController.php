<?php

/**
 * フィード詳細コントローラー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */

/**
 * フィード詳細コントローラー
 *
 * @package Feed.Controller
 * @property Feed $Feed
 * @property FeedConfig $FeedConfig
 * @property FeedDetail $FeedDetail
 */
class FeedDetailsController extends FeedAppController {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'FeedDetails';

/**
 * モデル
 *
 * @var array
 * @access public
 */
	public $uses = array('Feed.FeedDetail', 'Feed.FeedConfig', 'Feed.Feed');

/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	public $helpers = array('BcForm');

/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');

/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	public $crumbs = array(
		array('name' => 'フィード管理', 'url' => array('controller' => 'feed_configs', 'action' => 'index'))
	);

/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	public $subMenuElements = array();

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		parent::beforeFilter();

		$feedConfig = $this->FeedConfig->read(null, $this->params['pass'][0]);
		$this->crumbs[] = array('name' => 'フィード設定情報： ' . $feedConfig['FeedConfig']['name'], 'url' => array('controller' => 'feed_configs', 'action' => 'edit', $this->params['pass'][0]));

		if ($this->params['prefix'] == 'admin') {
			$this->subMenuElements = array('feed_details');
			$this->help = 'feed_details_form';
		}
	}

/**
 * [ADMIN] 登録
 *
 * @param int feed_config_id
 * @return void
 * @access public
 */
	public function admin_add($feedConfigId) {
		/* 除外処理 */
		if (!$feedConfigId) {
			$this->setMessage('無効なIDです', true);
			$this->redirect(array('controller' => 'feed_configs', 'action' => 'index'));
		}

		if (empty($this->request->data)) {

			$this->request->data = $this->FeedDetail->getDefaultValue($feedConfigId);
		} else {

			if (!preg_match('/^http/is', $this->request->data['FeedDetail']['url']) && !preg_match('/^\//is', $this->request->data['FeedDetail']['url'])) {
				$this->request->data['FeedDetail']['url'] = '/' . $this->request->data['FeedDetail']['url'];
			}

			$this->FeedDetail->create($this->request->data);

			// データを保存
			if ($this->FeedDetail->save()) {

				$id = $this->FeedDetail->getLastInsertId();
				$this->setMessage('フィード「' . $this->request->data['FeedDetail']['name'] . '」を追加しました。', false, true);
				$this->redirect(array('controller' => 'feed_configs', 'action' => 'edit', $feedConfigId, $id, '#' => 'headFeedDetail'));
			} else {

				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		// 表示設定
		$this->pageTitle = '新規フィード情報登録';
		$this->render('form');
	}

/**
 * [ADMIN] 編集
 *
 * @param int $feedConfigId
 * @param int $id
 * @return	void
 * @access	public
 */
	public function admin_edit($feedConfigId, $id) {
		if (!$id && empty($this->request->data)) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('controller' => 'feed_configs', 'action' => 'index'));
		}

		if (empty($this->request->data)) {

			$this->request->data = $this->FeedDetail->read(null, $id);
		} else {

			if (!preg_match('/^http/is', $this->request->data['FeedDetail']['url']) && !preg_match('/^\//is', $this->request->data['FeedDetail']['url'])) {
				$this->request->data['FeedDetail']['url'] = '/' . $this->request->data['FeedDetail']['url'];
			}

			$this->FeedDetail->set($this->request->data);

			// データを保存
			if ($this->FeedDetail->save()) {

				$this->requestAction(array('controller' => 'feed_configs', 'action' => 'clear_cache'), array('pass' => array($this->request->data['FeedDetail']['feed_config_id'], $this->request->data['FeedDetail']['url'])));
				$this->setMessage('フィード詳細「' . $this->request->data['FeedDetail']['name'] . '」を更新しました。', false, true);
				$this->redirect(array('controller' => 'feed_configs', 'action' => 'edit', $feedConfigId, $id, '#' => 'headFeedDetail'));
			} else {

				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		// 表示設定
		$this->pageTitle = 'フィード情報編集';
		$this->render('form');
	}

/**
 * フィードのキャッシュを削除する
 *
 * @param string $feedConfigId
 * @param string $url
 * @return	void
 * @access	protected
 */
	protected function _clearViewCatch($feedConfigId, $url) {
		clearViewCache('/feed/index/' . $feedConfigId);
		clearViewCache('/feed/ajax/' . $feedConfigId);
		clearViewCache('/feed/cachetime/' . $feedConfigId);
		if (strpos($url, 'http') === false) {
			// 実際のキャッシュではSSLを利用しているかどうかわからないので、両方削除する
			clearCache($this->Feed->createCacheHash('', 'http://' . $_SERVER['HTTP_HOST'] . $this->base . $url), 'views', '.rss');
			clearCache($this->Feed->createCacheHash('', 'https://' . $_SERVER['HTTP_HOST'] . $this->base . $url), 'views', '.rss');
		} else {
			clearCache($this->Feed->createCacheHash('', $url), 'views', '.rss');
		}
	}

/**
 * [ADMIN] 削除　(ajax)
 *
 * @param int $feedConfigId
 * @param	int $id
 * @return void
 * @access	public
 */
	public function admin_ajax_delete($feedConfigId, $id = null) {
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_del($id)) {
			exit(true);
		}
		exit();
	}

/**
 * [ADMIN] 削除
 *
 * @param int $feedConfigId
 * @param	int $id
 * @return void
 * @access	public
 */
	public function admin_delete($feedConfigId, $id = null) {
		/* 除外処理 */
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('controller' => 'feed_configs', 'action' => 'index'));
		}

		// メッセージ用にデータを取得
		$FeedDetail = $this->FeedDetail->read(null, $id);

		// 削除実行
		if ($this->FeedDetail->delete($id)) {
			$this->setMessage($FeedDetail['FeedDetail']['name'] . ' を削除しました。', false, true);
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}

		$this->redirect(array('controller' => 'feed_configs', 'action' => 'edit', $feedConfigId, $id, '#' => 'headFeedDetail'));
	}

/**
 * 一括削除
 * 
 * @param array $ids
 * @return boolean
 * @access protected
 */
	protected function _batch_del($ids) {
		if ($ids) {
			foreach ($ids as $id) {
				$this->_del($id);
			}
		}
		return true;
	}

/**
 * データを削除する
 * 
 * @param int $id
 * @return boolean 
 * @access protected
 */
	protected function _del($id) {
		$data = $this->FeedDetail->read(null, $id);
		if ($this->FeedDetail->delete($id)) {
			$this->FeedDetail->saveDbLog('フィード「' . $data['FeedDetail']['name'] . '」を削除しました。');
			return true;
		} else {
			return false;
		}
	}

}
