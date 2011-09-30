<?php
/* SVN FILE: $Id$ */
/**
 * フィード設定コントローラー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
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
 * @package baser.plugins.feed.controllers
 */
class FeedConfigsController extends FeedAppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'FeedConfigs';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array("Feed.FeedConfig","Feed.FeedDetail","Feed.RssEx");
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array('TextEx','TimeEx','FormEx','Feed.Feed');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('AuthEx','Cookie','AuthConfigure');
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $navis = array('フィード管理'=>'/admin/feed/feed_configs/index');
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * before_filter
 *
 * @return void
 * @access public
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
 * @return void
 * @access public
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
 * @return void
 * @access public
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
 * @param int $id
 * @return void
 * @access public
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
				
				$this->_clearCache($this->data['FeedConfig']['id']);
				$this->Session->setFlash('フィード「'.$this->data['FeedConfig']['name'].'」を更新しました。');
				$this->FeedConfig->saveDbLog('フィード「'.$this->data['FeedConfig']['name'].'」を更新しました。');

				if($this->data['FeedConfig']['edit_template']){
					$this->redirectEditTemplate($this->data['FeedConfig']['template']);
				}else{
					$this->redirect(array('action'=>'index'));
				}

			}else {

				$this->Session->setFlash('入力エラーです。内容を修正してください。');

			}

		}

		// 表示設定
		$this->subMenuElements = am($this->subMenuElements,array('feed_details'));
		$this->pageTitle = 'フィード設定編集';
		$this->render('form');

	}
/**
 * テンプレート編集画面にリダイレクトする
 * 
 * @param string $template
 * @return void
 * @access public
 */
	function redirectEditTemplate($template){
		
		$path = 'feed'.DS.$template.'.ctp';
		$target = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.$path;
		$sorces = array(BASER_PLUGINS.'mail'.DS.'views'.DS.$path);
		if($this->siteConfigs['theme']){
			if(!file_exists($target)){
				foreach($sorces as $source){
					if(file_exists($source)){
						$folder = new Folder();
						$folder->create(dirname($target), 0777);
						copy($source,$target);
						chmod($target,0666);
						break;
					}
				}
			}
			$this->redirect(array('plugin'=>null,'mail'=>false,'prefix'=>false,'controller'=>'theme_files','action'=>'edit',$this->siteConfigs['theme'],'etc',$path));
		}else{
			$this->Session->setFlash('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。');
			$this->redirect(array('action'=>'index'));
		}
		
	}
/**
 * [ADMIN] 削除
 *
 * @param int $id
 * @return void
 * @access public
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
 * @return void
 * @access public
 */
	function admin_preview($id) {
		
		if(!$id) $this->notFound();
		$this->pageTitle = 'プレビュー：'.$this->FeedConfig->field('name',array('FeedConfig.id'=>$id));
		$this->set('id',$id);
		
	}
/**
 * フィードのキャッシュを全て削除する
 * 
 * @return void
 * @access public
 */
	function admin_delete_cache() {
		
		$this->_clearCache();
		$this->Session->setFlash('フィードのキャッシュを削除しました。');
		$this->redirect('/admin/feed/feed_configs/index');
		
	}
/**
 * フィードのキャッシュを削除する（requestAction用）
 *
 * @param string $feedConfigId
 * @param string $url
 * @return void
 * @access protected
 */
	function admin_clear_cache($feedConfigId = '', $url = '') {

		$this->_clearCache($feedConfigId, $url);
		
	}
/**
 * フィードのキャッシュを削除する
 * TODO 第2引き数がない場合、全てのRSSのキャッシュを削除してしまう仕様となっているので
 * RSSキャッシュ保存名をURLのハッシュ文字列ではなく、feed_detail_idを元にした文字列に変更し、
 * feed_detail_idで指定して削除できるようにする
 *
 * @param	string	$feedConfigId
 * @param	string	$url
 * @return	void
 * @access	protected
 */
	function _clearCache($feedConfigId = '', $url = '') {
		
		if($feedConfigId) {
			clearViewCache('/feed/index/'.$feedConfigId);
			clearViewCache('/feed/ajax/'.$feedConfigId);
			clearViewCache('/feed/cachetime/'.$feedConfigId);
		} else {
			clearViewCache('/feed/index');
			clearViewCache('/feed/ajax');
			clearViewCache('/feed/cachetime');
		}
		if($url) {
			if(strpos($url,'http')===false) {
				// 実際のキャッシュではSSLを利用しているかどうかわからないので、両方削除する
				clearCache($this->RssEx->__createCacheHash('', 'http://'.$_SERVER['HTTP_HOST'].$this->base.$url), 'views', '.rss');
				clearCache($this->RssEx->__createCacheHash('', 'https://'.$_SERVER['HTTP_HOST'].$this->base.$url), 'views', '.rss');
			}else {
				clearCache($this->RssEx->__createCacheHash('', $url),'views','.rss');
			}
		} else {
			clearViewCache(null,'rss');
		}
		
	}
	
}
?>