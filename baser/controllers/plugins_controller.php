<?php
/* SVN FILE: $Id$ */
/**
 * Plugin 拡張クラス
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
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
		$this->setMessage('プラグイン「'.$pluginName.'」 を完全に削除しました。');
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
		$dbInited = false;
		
		if(!$this->data) {
			
			$installMessage = '';
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

			$data = $this->Plugin->find('first',array('conditions'=>array('name'=>$this->data['Plugin']['name'])));
			if($data) {
				$dbInited = $data['Plugin']['db_inited'];
			}

		}else {

			$data = $this->Plugin->find('first',array('conditions'=>array('name'=>$this->data['Plugin']['name'])));

			if(empty($data['Plugin']['db_inited'])) {
				if(file_exists(APP.'plugins'.DS.$name.DS.'config'.DS.'init.php')) {
					include APP.'plugins'.DS.$name.DS.'config'.DS.'init.php';
				}elseif(file_exists(BASER_PLUGINS.$name.DS.'config'.DS.'init.php')) {
					include BASER_PLUGINS.$name.DS.'config'.DS.'init.php';
				}
			}
			
			if($data) {
				// 既にインストールデータが存在する場合は、DBのバージョンは変更しない
				$data['Plugin']['name'] = $this->data['Plugin']['name'];
				$data['Plugin']['title'] = $this->data['Plugin']['title'];
				$data['Plugin']['status'] = $this->data['Plugin']['status'];
				$data['Plugin']['db_inited'] = true;
				$this->Plugin->set($data);
			} else {
				$this->data['Plugin']['db_inited'] = true;
				$this->Plugin->create($this->data);
			}

			// データを保存
			if($this->Plugin->save()) {
				
				clearAllCache();
				$this->setMessage('新規プラグイン「'.$name.'」を baserCMS に登録しました。', false, true);
				$this->redirect(array('action' => 'index'));

			}else {
				$this->setMessage('プラグインに問題がある為インストールを完了できません。プラグインの開発者に確認してください。', true);
			}

		}

		/* 表示設定 */
		$this->set('installMessage', $installMessage);
		$this->set('dbInited', $dbInited);
		$this->subMenuElements = array('plugins');
		$this->pageTitle = '新規プラグイン登録';
		$this->help = 'plugins_form';
		$this->render('form');

	}
/**
 * データベースをリセットする 
 */
	function admin_reset_db() {
		
		if(!$this->data) {
			$this->setMessage('無効な処理です。', true);
		} else {
			
			$data = $this->Plugin->find('first',array('conditions'=>array('name'=>$this->data['Plugin']['name'])));
			$this->Plugin->resetDb($this->data['Plugin']['name']);
			$data['Plugin']['db_inited'] = false;
			$this->Plugin->set($data);
			
			// データを保存
			if($this->Plugin->save()) {
				clearAllCache();
				$this->setMessage($data['Plugin']['title'] . ' プラグインのデータを初期化しました。', false, true);
				$this->redirect(array('action' => 'add', $data['Plugin']['name']));
			}else {
				$this->setMessage('処理中にエラーが発生しました。プラグインの開発者に確認してください。', true);
			}
			
		}
		
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
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}


		$data = $this->Plugin->read(null, $id);
		$data['Plugin']['status'] = false;

		/* 削除処理 */
		if($this->Plugin->save($data)) {
			clearAllCache();
			$this->setMessage('プラグイン「'.$data['Plugin']['title'].'」 を 無効化しました。', false, true);
		}else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
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
