<?php
/* SVN FILE: $Id$ */
/**
 * フィード設定コントローラー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.feed.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * フィード設定コントローラー
 *
 * @package			baser.plugins.feed.controllers
 */
class FeedConfigsController extends FeedAppController {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'FeedConfigs';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array("Feed.FeedConfig","Feed.FeedDetail","Feed.RssEx");
/**
 * ヘルパー
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('TextEx','TimeEx','FormEx');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
	var $components = array('Auth','Cookie','AuthConfigure');
/**
 * ぱんくずナビ
 *
 * @var		string
 * @access 	public
 */
	var $navis = array('システム設定'=>'/admin/site_configs/form',
			'プラグイン管理'=>'/admin/plugins/index',
			'フィード管理'=>'/admin/feed/feed_configs/index');
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * before_filter
 *
 * @return	void
 * @access 	public
 */
	function beforeFilter() {
		parent::beforeFilter();
		if($this->params['prefix']=='admin') {
			$this->subMenuElements = array('feed_common');
		}
	}
/**
 * [ADMIN] 一覧表示
 *
 * @return	void
 * @access 	public
 */
	function admin_index() {

		// データを取得
		$this->paginate = array('conditions'=>array(),
				'fields'=>array(),
				'order'=>'FeedConfig.id',
				'limit'=>10
		);
		$feedConfigs = $this->paginate('FeedConfig');

		if($feedConfigs) {
			$this->set('feedConfigs',$feedConfigs);
		}

		// 表示設定
		$this->pageTitle = 'フィード設定一覧';

	}
/**
 * [ADMIN] 登録
 *
 * @return	void
 * @access 	public
 */
	function admin_add() {

		if(empty($this->data)) {

			$this->data = $this->FeedConfig->getDefaultValue();

		}else {

			$this->FeedConfig->create($this->data);

			// データを保存
			if($this->FeedConfig->save()) {
				$id = $this->FeedConfig->getLastInsertId();
				$this->Session->setFlash('フィード「'.$this->data['FeedConfig']['name'].'」を追加しました。');
				$this->FeedConfig->saveDbLog('フィード「'.$this->data['FeedConfig']['name'].'」を追加しました。');
				$this->redirect('/admin/feed/feed_configs/edit/'.$id.'#headFeedDetail');

			}else {

				$this->Session->setFlash('入力エラーです。内容を修正してください。');

			}

		}

		// 表示設定
		$this->pageTitle = '新規フィード設定登録';
		$this->render('form');

	}
/**
 * [ADMIN] 編集
 *
 * @param	int		feed_confg_id
 * @return	void
 * @access 	public
 */
	function admin_edit($id) {

		if(!$id && empty($this->data)) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		if(empty($this->data)) {

			$this->data = $this->FeedConfig->read(null, $id);
			$this->set('feedConfig',$this->data);

		}else {

			// データを保存
			if($this->FeedConfig->save($this->data)) {

				$this->Session->setFlash('フィード「'.$this->data['FeedConfig']['name'].'」を更新しました。');
				$this->FeedConfig->saveDbLog('フィード「'.$this->data['FeedConfig']['name'].'」を更新しました。');
				$this->redirect('/admin/feed/feed_configs/index');

			}else {

				$this->Session->setFlash('入力エラーです。内容を修正してください。');

			}

		}

		// 表示設定
		$this->subMenuElements = am($this->subMenuElements,array('feed_details'));
		$this->pageTitle = 'フィード設定情報編集';
		$this->render('form');

	}
/**
 * [ADMIN] 削除
 *
 * @param	int		feed_confg_id
 * @return	void
 * @access 	public
 */
	function admin_delete($id = null) {

		if(!$id) {

			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
			return;

		}

		// メッセージ用にデータを取得
		$feedConfig = $this->FeedConfig->read(null, $id);

		// 削除実行
		if($this->FeedConfig->del($id)) {

			$this->Session->setFlash($feedConfig['FeedConfig']['name'].' を削除しました。');
			$this->FeedConfig->saveDbLog('フィード「'.$feedConfig['FeedConfig']['name'].'」を削除しました。');


		}else {

			$this->Session->setFlash('データベース処理中にエラーが発生しました。');

		}

		$this->redirect(array('action'=>'admin_index'));

	}
/**
 * 読み込んだフィードをプレビュー表示する
 *
 * @param string $id
 */
	function admin_preview($id) {
		if(!$id) $this->notFound();
		$this->pageTitle = 'プレビュー：'.$this->FeedConfig->field('name',array('FeedConfig.id'=>$id));
		$this->set('id',$id);
	}
/**
 * フィードのキャッシュを削除する
 * @return	void
 * @access	public
 */
	function admin_delete_cache() {
		$baseUrl = Configure::read('App.baseUrl');
		clearViewCache(null,'rss');
		clearViewCache($baseUrl.'/feed/index');
		$this->Session->setFlash('フィードのキャッシュを削除しました。');
		$this->redirect('/admin/feed/feed_configs/index');
	}
}
?>