<?php
/* SVN FILE: $Id$ */
/**
 * Plugin 拡張クラス
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Plugin 拡張クラス
 * プラグインのコントローラーより継承して利用する
 *
 * @package baser.controllers
 */
class PluginsController extends AppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Plugins';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('GlobalMenu','Plugin','PluginContent');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('BcAuth','Cookie','BcAuthConfigure');
/**
 * ヘルパ
 *
 * @var array
 * @access public
 */
	var $helpers = array(BC_TIME_HELPER, BC_FORM_HELPER);
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $crumbs = array(
		array('name' => 'プラグイン管理', 'url' => array('plugin' => '', 'controller' => 'plugins', 'action' => 'index'))
	);
/**
 * コンテンツID
 *
 * @var int
 * @deprecated BaserPluginAppController に移行
 */
	var $contentId = null;
/**
 * beforeFilter
 *
 * @return void
 * @access private
 * @deprecated BaserPluginAppController に移行
 */
	function beforeFilter() {

		parent::beforeFilter();

		if(!isset($this->Plugin)) {
			$this->cakeError('missingClass', array(array('className' => 'Plugin',
							'notice'=>'プラグインでは、コントローラーで、Pluginモデルを読み込んでおく必要があります。usesプロパティを確認してください。')));
		}

		// 有効でないプラグインを実行させない
		if($this->name != 'Plugins' && !$this->Plugin->find('all',array('conditions'=>array('name'=>$this->params['plugin'], 'status'=>true)))) {
			$this->notFound();
		}

		$this->contentId = $this->getContentId();
		
	}
/**
 * コンテンツIDを取得する
 * 一つのプラグインで複数のコンテンツを実装する際に利用する。
 *
 * @return int $pluginNo
 * @access public
 * @deprecated BaserPluginAppController に移行
 */
	function getContentId() {

		// 管理画面の場合には取得しない
		if(!empty($this->params['admin'])){
			return null;
		}

		if(!isset($this->PluginContent)) {
			return null;
		}

		if(!isset($this->params['url']['url'])) {
			return null;
		}
		$contentName = '';
		$url = preg_replace('/^\//', '', $this->params['url']['url']);
		$url = split('/', $url);
		if($url[0]!=Configure::read('BcRequest.agentAlias')) {
			if(!empty($this->params['prefix']) && $url[0] == $this->params['prefix']) {
				if(isset($url[1])) {
					$contentName = $url[1];
				}
			}else {
				$contentName = $url[0];
			}
		}else {
			if(!empty($this->params['prefix']) && $url[0] == $this->params['prefix']) {
				$contentName = $url[2];
			}else {
				$contentName = $url[1];
			}
		}

		// プラグインと同じ名前のコンテンツ名の場合に正常に動作しないので
		// とりあえずコメントアウト
		/*if( Inflector::camelize($url) == $this->name){
			return null;
		}*/
		$pluginContent = $this->PluginContent->findByName($contentName);
		if($pluginContent) {
			return $pluginContent['PluginContent']['content_id'];
		}else {
			return null;
		}

	}
/**
 * プラグインの一覧を表示する
 *
 * @return void
 * @access public
 */
	function admin_index() {

		$datas = $this->Plugin->find('all', array('order' => 'Plugin.name'));
		if(!$datas) {
			$datas = array();
		}
		
		// プラグインフォルダーのチェックを行う。
		$pluginInfos = array();
		$Folder = new Folder(APP.'plugins'.DS);
		$files = $Folder->read(true, true, true);
		foreach($files[0] as $file) {
			$pluginInfos[basename($file)] = $this->_getPluginInfo($datas, $file);
		}
		$Folder = new Folder(BASER_PLUGINS);
		$files = $Folder->read(true, true, true);
		foreach($files[0] as $file) {
			$pluginInfos[basename($file)] = $this->_getPluginInfo($datas, $file, true);
		}

		$pluginInfos = array_values($pluginInfos); // Set::sortの為、一旦キーを初期化
		$pluginInfos = array_reverse($pluginInfos); // Set::sortの為、逆順に変更
		$pluginInfos = Set::sort($pluginInfos, '{n}.Plugin.status', 'desc');
		
		// 表示設定
		$this->set('datas',$pluginInfos);
		$this->set('corePlugins', Configure::read('BcApp.corePlugins'));
		$this->subMenuElements = array('plugins');
		$this->pageTitle = 'プラグイン一覧';
		$this->help = 'plugins_index';

	}
/**
 * プラグイン情報を取得する
 * 
 * @param array $pluginDatas
 * @param string $file
 * @return array 
 */
	function _getPluginInfo($datas, $file, $core = false) {
		
		$plugin = basename($file);
		$pluginData = array();
		$exists = false;
		foreach($datas as $data) {
			if($plugin == $data['Plugin']['name']) {
				$pluginData = $data;
				$exists = true;
				break;
			}
		}

		// プラグインのバージョンを取得
		$corePlugins = Configure::read('BcApp.corePlugins');
		if(in_array($plugin, $corePlugins)) {
			$version = $this->getBaserVersion();
		} else {
			$version = $this->getBaserVersion($plugin);
		}

		// 設定ファイル読み込み
		$title = $description = $author = $url = $adminLink = '';

		// TODO 互換性のため古いパスも対応
		$oldAppConfigPath = $file.DS.'config'.DS.'config.php';
		$appConfigPath = $file.DS.'config.php';
		if(!file_exists($appConfigPath)) {
			$appConfigPath = $oldAppConfigPath;
		}

		if(file_exists($appConfigPath)) {
			include $appConfigPath;
		} elseif(file_exists($oldAppConfigPath)) {
			include $oldAppConfigPath;
		}

		if(isset($title))
			$pluginData['Plugin']['title'] = $title;
		if(isset($description))
			$pluginData['Plugin']['description'] = $description;
		if(isset($author))
			$pluginData['Plugin']['author'] = $author;
		if(isset($url))
			$pluginData['Plugin']['url'] = $url;

		$pluginData['Plugin']['update'] = false;
		$pluginData['Plugin']['old_version'] = false;
		$pluginData['Plugin']['core'] = $core;
		
		if($exists) {
			
			if(isset($adminLink))
				$pluginData['Plugin']['admin_link'] = $adminLink;
			// バージョンにBaserから始まるプラグイン名が入っている場合は古いバージョン
			if(!$pluginData['Plugin']['version'] && preg_match('/^Baser[a-zA-Z]+\s([0-9\.]+)$/', $version, $matches)) {
				$pluginData['Plugin']['version'] = $matches[1];
				$pluginData['Plugin']['old_version'] = true;
			}elseif(verpoint ($pluginData['Plugin']['version']) < verpoint($version) && !in_array($pluginData['Plugin']['name'], Configure::read('BcApp.corePlugins'))) {
				$pluginData['Plugin']['update'] = true;
			}
			$pluginData['Plugin']['registered'] = true;
			
		} else {
			// バージョンにBaserから始まるプラグイン名が入っている場合は古いバージョン
			if(preg_match('/^Baser[a-zA-Z]+\s([0-9\.]+)$/', $version,$matches)) {
				$version = $matches[1];
				$pluginData['Plugin']['old_version'] = true;
			}
			$pluginData['Plugin']['id'] = '';
			$pluginData['Plugin']['name'] = $plugin;
			$pluginData['Plugin']['created'] = '';
			$pluginData['Plugin']['version'] = $version;
			$pluginData['Plugin']['status'] = false;
			$pluginData['Plugin']['modified'] = '';
			$pluginData['Plugin']['admin_link'] = '';
			$pluginData['Plugin']['registered'] = false;
		}
		return $pluginData;

	}
/**
 * [ADMIN] ファイル削除
 *
 * @param string プライグイン名
 * @return void
 * @access public
 * @deprecated admin_ajax_delete_file に移行
 */
	function admin_delete_file($pluginName) {
		
		$this->__deletePluginFile($pluginName);
		$message = 'プラグイン「'.$pluginName.'」 を完全に削除しました。';
		$this->Session->setFlash($message);
		$this->redirect(array('action' => 'index'));
		
	}
/**
 * [ADMIN] ファイル削除
 *
 * @param string プライグイン名
 * @return void
 * @access public
 */
	function admin_ajax_delete_file($pluginName) {
		
		if($pluginName) {
			$this->ajaxError(500, '無効な処理です。');
		}
		
		$pluginName = urldecode($pluginName);
		$this->__deletePluginFile($pluginName);
		$this->Plugin->saveDbLog('プラグイン「'.$pluginName.'」 を完全に削除しました。');
		exit(true);
		
	}
/**
 * プラグインファイルを削除する
 *
 * @param string $pluginName
 * @return void
 * @access private
 */
	function __deletePluginFile($pluginName) {

		$appPath = APP.'plugins'.DS.$pluginName.DS.'config'.DS.'sql'.DS;
		$baserPath = BASER_PLUGINS.$pluginName.DS.'config'.DS.'sql'.DS;
		$tmpPath = TMP.'schemas'.DS.'uninstall'.DS;
		$folder = new Folder();
		$folder->delete($tmpPath);
		$folder->create($tmpPath);

		if(is_dir($appPath)) {
			$path = $appPath;
		}else {
			$path = $baserPath;
		}

		// インストール用スキーマをdropスキーマとして一時フォルダに移動
		$folder = new Folder($path);
		$files = $folder->read(true, true);
		if(is_array($files[1])) {
			foreach($files[1] as $file) {
				if(preg_match('/\.php$/', $file)) {
					$from = $path.DS.$file;
					$to = $tmpPath.'drop_'.$file;
					copy($from, $to);
					chmod($to, 0666);
				}
			}
		}

		// テーブルを削除
		$this->Plugin->loadSchema('plugin', $tmpPath);

		// プラグインフォルダを削除
		$folder->delete(APP.'plugins'.DS.$pluginName);

		// 一時フォルダを削除
		$folder->delete($tmpPath);
		
	}
/**
 * [ADMIN] 登録処理
 *
 * @param string 	$name
 * @return  void
 * @access  public
 */
	function admin_add($name) {
		
		$name = urldecode($name);
		if(!$this->data) {
			
			// TODO 互換性のため古いパスも対応
			$oldAppConfigPath = APP.DS.'plugins'.DS.$name.DS.'config'.DS.'config.php';
			$appConfigPath = APP.DS.'plugins'.DS.$name.DS.'config.php';
			if(!file_exists($appConfigPath)) {
				$appConfigPath = $oldAppConfigPath;
			}
			$baserConfigPath = BASER_PLUGINS.$name.DS.'config.php';
			if(file_exists($appConfigPath)) {
				include $appConfigPath;
			} elseif(file_exists($oldAppConfigPath)) {
				include $oldAppConfigPath;
			}elseif(file_exists($baserConfigPath)) {
				include $baserConfigPath;
			}

			$this->data['Plugin']['name']=$name;
			if(isset($title)) {
				$this->data['Plugin']['title'] = $title;
			} else {
				$this->data['Plugin']['title'] = $name;
			}
			$this->data['Plugin']['status'] = true;
			$corePlugins = Configure::read('BcApp.corePlugins');
			if(in_array($name, $corePlugins)) {
				$this->data['Plugin']['version'] = $this->getBaserVersion();
			} else {
				$this->data['Plugin']['version'] = $this->getBaserVersion($name);
			}

			if(!empty($installMessage)) {
				$this->Session->setFlash($installMessage);
			}

		}else {

			$data = $this->Plugin->find('first',array('conditions'=>array('name'=>$this->data['Plugin']['name'])));

			if($data) {
				// 既にインストールデータが存在する場合は、DBのバージョンは変更しない
				$data['Plugin']['name']=$this->data['Plugin']['name'];
				$data['Plugin']['title']=$this->data['Plugin']['title'];
				$data['Plugin']['status']=$this->data['Plugin']['status'];
				$this->Plugin->set($data);
			} else {
				if(file_exists(APP.'plugins'.DS.$name.DS.'config'.DS.'init.php')) {
					include APP.'plugins'.DS.$name.DS.'config'.DS.'init.php';
				}elseif(file_exists(BASER_PLUGINS.$name.DS.'config'.DS.'init.php')) {
					include BASER_PLUGINS.$name.DS.'config'.DS.'init.php';
				}
				$data = $this->data;
				$this->Plugin->create($data);
			}

			// データを保存
			if($this->Plugin->save()) {
				
				clearAllCache();
				$message = '新規プラグイン「'.$data['Plugin']['name'].'」を baserCMS に登録しました。';
				$this->Session->setFlash($message);
				$this->Plugin->saveDbLog($message);
				$this->redirect(array('action' => 'index'));

			}else {
				$this->Session->setFlash('プラグインに問題がある為インストールを完了できません。開発者に確認してください。');
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('plugins');
		$this->pageTitle = '新規プラグイン登録';
		$this->help = 'plugins_form';
		$this->render('form');

	}
/**
 * [ADMIN] 削除処理
 *
 * @param int ID
 * @return void
 * @access public
 * @deprecated admin_ajax_delete に移行
 */
	function admin_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action' => 'index'));
		}


		$data = $this->Plugin->read(null, $id);
		$data['Plugin']['status'] = false;

		/* 削除処理 */
		if($this->Plugin->save($data)) {
			clearAllCache();
			$message = 'プラグイン「'.$data['Plugin']['title'].'」 を 無効化しました。';
			$this->Session->setFlash($message);
			$this->Plugin->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action' => 'index'));

	}
/**
 * [ADMIN] 削除処理　(ajax)
 *
 * @param int ID
 * @return void
 * @access public
 */
	function admin_ajax_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		$data = $this->Plugin->read(null, $id);
		$data['Plugin']['status'] = false;
		$this->Plugin->set($data);
		/* 削除処理 */
		if($this->Plugin->save()) {
			clearAllCache();
			$this->Plugin->saveDbLog('プラグイン「'.$data['Plugin']['title'].'」 を 無効化しました。');
			exit(true);
		}
		
		exit();

	}
/**
 * 一括無効
 * 
 * @param array $ids
 * @return boolean
 * @access protected
 */
	function _batch_del($ids) {
		
		if($ids) {
			foreach($ids as $id) {
				$data = $this->Plugin->read(null, $id);
				$data['Plugin']['status'] = false;
				$this->Plugin->set($data);
				if($this->Plugin->save()) {
					$this->Plugin->saveDbLog('プラグイン「'.$data['Plugin']['title'].'」 を 無効化しました。');
				}
			}
			clearAllCache();
		}
		return true;
		
	}
	
}
?>