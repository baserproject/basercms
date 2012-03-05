<?php
/* SVN FILE: $Id$ */
/**
 * デモデータ操作用シェルスクリプト
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.vendors.shells
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Core','Security');
App::import('Component','BaserManager');
/**
 * デモデータ操作用シェルスクリプト
 *
 * @package baser.vendors.shells
 */
class DemoShell extends Shell {
/**
 * コンポーネント
 * 
 * @var array
 */
	var $components = array('BaserManager');
/**
 * スタートアップ
 */
    function startup() {
        $this->BaserManager = new BaserManagerComponent();
    }
/**
 * デモ用のCSVデータを初期化する
 */
	function initcsv() {
	
		// データベース初期化
		if(!$this->BaserManager->initDb()){
			echo "データベースの初期化に失敗しました\n";
			return;
		}
		
		// キャッシュ削除
		clearAllCache();

		// ユーザー作成
		if(!$this->_initUsers()){
			echo "ユーザー「operator」の作成に失敗しました\n";
			return;
		}

		// サイト設定
		if(!$this->_initSiteConfigs()){
			echo "システム設定の更新に失敗しました\n";
			return;
		}
		
		// プラグインの有効化
		if(!$this->_initPlugin()) {
			echo "プラグインの有効化に失敗しました\n";
			return;
		}
		
		// ブログ記事の投稿日更新
		if(!$this->_initBlogPosts()) {
			echo "ブログ記事の投稿日の更新に失敗しました\n";
			return;
		}
		
		// でもテーマの配置
		if(!$this->BaserManager->deployTheme()) {
			echo "デモテーマの配置に失敗しました。\n";
			return;
		}
		
		// スケルトンテーマの配置
		if(!$this->BaserManager->deployTheme('skelton')) {
			echo "テーマの配置に失敗しました。\n";
			return;
		}
		
		// ページ初期化
		if($this->_initPages()){
			echo "デモデータの初期化に成功しました\n";
		} else {
			echo "ページテンプレートの更新に失敗しました\n";
		}
		
	}
/**
 * ページを初期化
 * 
 * @return boolean 
 */
	function _initPages() {

		$ret = true;
		$Page = ClassRegistry::init('Page');
		$pages = $Page->find('all');
		// シェルでリクエストアクションを呼び出すとエラーになるので、検索テーブルへの保存は行わない
		$Page->contentSaving = false;
		foreach($pages as $page){
			$Page->set($page);
			if(!$Page->save()){
				$ret = false;
			}
		}
		return $ret;
		
	}
/**
 * サイト設定の初期化
 * 
 * @return boolean
 */
	function _initSiteConfigs() {
		
		$SiteConfig = ClassRegistry::init('SiteConfig');
		$siteConfig = $SiteConfig->findExpanded();
		$siteConfig['address'] = '福岡県福岡市博多区博多駅前';
		$siteConfig['googlemaps_key'] = 'ABQIAAAAQMyp8zF7wiAa55GiH41tChRi112SkUmf5PlwRnh_fS51Rtf0jhTHomwxjCmm-iGR9GwA8zG7_kn6dg';
		$siteConfig['demo_on'] = true;
		return $SiteConfig->saveKeyValue($siteConfig);
		
	}
/**
 * 初期ユーザーの作成
 * 
 * @return boolean 
 */
	function _initUsers() {
		
		$User = ClassRegistry::init('User');
	
		$ret = true;
		$user['User']['name'] = 'admin';
		$user['User']['password'] = Security::hash('demodemo', null, true);
		$user['User']['password_1'] = 'demodemo';
		$user['User']['password_2'] = 'demodemo';
		$user['User']['real_name_1'] = 'admin';
		$user['User']['user_group_id'] = 1;
		$User->create($user);
		if(!$User->save()) $ret = false;

		$user['User']['name'] = 'operator';
		$user['User']['password'] = Security::hash('demodemo', null, true);
		$user['User']['password_1'] = 'demodemo';
		$user['User']['password_2'] = 'demodemo';
		$user['User']['real_name_1'] = 'member';
		$user['User']['user_group_id'] = 2;
		$User->create($user);
		if(!$User->save()) $ret = false;
		
		return $ret;
		
	}
/**
 * プラグインを有効にする
 * 
 * @return boolean 
 */
	function _initPlugin() {
		
		$ret = true;
		$Plugin = ClassRegistry::init('Plugin');
		$datas = $Plugin->find('all', array('conditions' => array('Plugin.id' => array('1', '2', '3'))));
		foreach($datas as $data){
			$data['Plugin']['status'] = 1;
			$Plugin->set($data);
			if(!$Plugin->save()){
				$ret = false;
			}
		}
		return $ret;
		
	}
/**
 * プラグインを有効にする
 * 
 * @return boolean 
 */
	function _initBlogPosts() {
		
		$ret = true;
		$BlogPost = ClassRegistry::init('Blog.BlogPost');
		$datas = $BlogPost->find('all');
		foreach($datas as $data){
			$data['BlogPost']['posts_date'] = date('Y-m-d H:i:s');
			$BlogPost->set($data);
			if(!$BlogPost->save()){
				$ret = false;
			}
		}
		return $ret;
		
	}
}
?>