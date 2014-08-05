<?php

/**
 * Plugin 拡張クラス
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * Plugin 拡張クラス
 * プラグインのコントローラーより継承して利用する
 *
 * @package Baser.Controller
 */
class PluginsController extends AppController {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Plugins';

/**
 * モデル
 *
 * @var array
 * @access public
 */
	public $uses = array('Menu', 'Plugin', 'PluginContent');

/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');

/**
 * ヘルパ
 *
 * @var array
 * @access public
 */
	public $helpers = array('BcTime', 'BcForm');

/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	public $subMenuElements = array();

/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	public $crumbs = array(
		array('name' => 'プラグイン管理', 'url' => array('plugin' => '', 'controller' => 'plugins', 'action' => 'index'))
	);

/**
 * プラグインをアップロードしてインストールする
 */
	public function admin_add() {
		
		$this->pageTitle = 'プラグインアップロード';
		$this->subMenuElements = array('plugins');
		
		if($this->request->data) {
			if(empty($this->request->data['Plugin']['file']['tmp_name'])) {
				$this->setMessage('ファイルのアップロードに失敗しました。', true);
			} else {
				$name = $this->request->data['Plugin']['file']['name'];
				move_uploaded_file($this->request->data['Plugin']['file']['tmp_name'], TMP . $name);
				exec('unzip -o ' . TMP . $name . ' -d ' . BASER_PLUGINS, $return);
				if(!empty($return[2])) {
					$plugin = str_replace('  inflating: ' . BASER_PLUGINS, '', $return[2]);
					$plugin = explode(DS, $plugin);
					$plugin = $plugin[0];
					$pluginPath = BASER_THEMES . $plugin;
					$Folder = new Folder();
					$Folder->chmod($pluginPath, 0777);
					$plugin = Inflector::camelize($plugin);
					$Folder->move(array(
						'to' => BASER_THEMES . $plugin, 
						'from' => $pluginPath, 
						'mode' => 0777
					));
					unlink(TMP . $name);
					// プラグインをインストール
					if ($this->BcManager->installPlugin($plugin)) {
						clearAllCache();
						$this->setMessage('新規プラグイン「' . $plugin . '」を baserCMS に登録しました。', false, true);
						$this->redirect(array('action' => 'index'));
					} else {
						$this->setMessage('プラグインに問題がある為インストールを完了できません。プラグインの開発者に確認してください。', true);
					}				
					
				} else {
					$this->setMessage('アップロードしたZIPファイルの展開に失敗しました。', true);
					$this->redirect(array('action' => 'add'));
				}
			}
		}
		
	}
	
/**
 * プラグインの一覧を表示する
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		$datas = $this->Plugin->find('all', array('order' => 'Plugin.priority'));
		if (!$datas) {
			$datas = array();
		}

		// プラグインフォルダーのチェックを行う。
		$pluginInfos = array();
		$paths = App::path('Plugin');
		foreach($paths as $path) {
			$Folder = new Folder($path);
			$files = $Folder->read(true, true, true);
			foreach ($files[0] as $file) {
				$pluginInfos[basename($file)] = $this->_getPluginInfo($datas, $file);
			}
		}
		
		$pluginInfos = array_values($pluginInfos); // Hash::sortの為、一旦キーを初期化
		$pluginInfos = array_reverse($pluginInfos); // Hash::sortの為、逆順に変更
		$pluginInfos = Hash::sort($pluginInfos, '{n}.Plugin.status', 'desc');

		$baserPlugins = array();
		if(strtotime('2014-03-31 17:00:00') <= time()) {
			$cachePath = 'views' . DS . 'baser_market_plugins.rss';
			if (Configure::read('debug') > 0) {
				clearCache('baser_market_plugins', 'views', '.rss');
			}
			$baserPlugins = cache($cachePath);
			if(!$baserPlugins) {
				$Xml = new Xml();
				try {
					$baserPlugins = $Xml->build(Configure::read('BcApp.marketPluginRss'));
				} catch (Exception $ex) {}
				if($baserPlugins) {
					$baserPlugins = $Xml->toArray($baserPlugins->channel);
					$baserPlugins = $baserPlugins['channel']['item'];
					cache($cachePath, BcUtil::serialize($baserPlugins));
					chmod(CACHE . $cachePath, 0666);
				} else {
					$baserPlugins = array();
				}
			} else {
				$baserPlugins = BcUtil::unserialize($baserPlugins);
			}
		}
		
		// 表示設定
		$this->set('baserPlugins', $baserPlugins);
		$this->set('datas', $pluginInfos);
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
	protected function _getPluginInfo($datas, $file) {
		$plugin = basename($file);
		$pluginData = array();
		$exists = false;
		foreach ($datas as $data) {
			if ($plugin == $data['Plugin']['name']) {
				$pluginData = $data;
				$exists = true;
				break;
			}
		}

		// プラグインのバージョンを取得
		$corePlugins = Configure::read('BcApp.corePlugins');
		$core = false;
		if (in_array($plugin, $corePlugins)) {
			$core = true;
			$version = $this->getBaserVersion();
		} else {
			$version = $this->getBaserVersion($plugin);
		}

		// 設定ファイル読み込み
		$title = $description = $author = $url = $adminLink = '';

		// TODO 互換性のため古いパスも対応
		$oldAppConfigPath = $file . DS . 'Config' . DS . 'config.php';
		$appConfigPath = $file . DS . 'config.php';
		if (!file_exists($appConfigPath)) {
			$appConfigPath = $oldAppConfigPath;
		}

		if (file_exists($appConfigPath)) {
			include $appConfigPath;
		} elseif (file_exists($oldAppConfigPath)) {
			include $oldAppConfigPath;
		}

		if (isset($title)) {
			$pluginData['Plugin']['title'] = $title;
		}
		if (isset($description)) {
			$pluginData['Plugin']['description'] = $description;
		}
		if (isset($author)) {
			$pluginData['Plugin']['author'] = $author;
		}
		if (isset($url)) {
			$pluginData['Plugin']['url'] = $url;
		}

		$pluginData['Plugin']['update'] = false;
		$pluginData['Plugin']['old_version'] = false;
		$pluginData['Plugin']['core'] = $core;

		if ($exists) {

			if (isset($adminLink)) {
				$pluginData['Plugin']['admin_link'] = $adminLink;
			}
			// バージョンにBaserから始まるプラグイン名が入っている場合は古いバージョン
			if (!$pluginData['Plugin']['version'] && preg_match('/^Baser[a-zA-Z]+\s([0-9\.]+)$/', $version, $matches)) {
				$pluginData['Plugin']['version'] = $matches[1];
				$pluginData['Plugin']['old_version'] = true;
			} elseif (verpoint($pluginData['Plugin']['version']) < verpoint($version) && !in_array($pluginData['Plugin']['name'], Configure::read('BcApp.corePlugins'))) {
				$pluginData['Plugin']['update'] = true;
			}
			$pluginData['Plugin']['registered'] = true;
		} else {
			// バージョンにBaserから始まるプラグイン名が入っている場合は古いバージョン
			if (preg_match('/^Baser[a-zA-Z]+\s([0-9\.]+)$/', $version, $matches)) {
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
	public function admin_delete_file($pluginName) {
		$this->__deletePluginFile($pluginName);
		$this->setMessage('プラグイン「' . $pluginName . '」 を完全に削除しました。');
		$this->redirect(array('action' => 'index'));
	}

/**
 * [ADMIN] ファイル削除
 *
 * @param string プライグイン名
 * @return void
 * @access public
 */
	public function admin_ajax_delete_file($pluginName) {
		if (!$pluginName) {
			$this->ajaxError(500, '無効な処理です。');
		}

		$pluginName = urldecode($pluginName);
		$this->__deletePluginFile($pluginName);
		$this->Plugin->saveDbLog('プラグイン「' . $pluginName . '」 を完全に削除しました。');
		exit(true);
	}

/**
 * プラグインファイルを削除する
 *
 * @param string $pluginName
 * @return void
 * @access private
 */
	private function __deletePluginFile($pluginName) {
		$paths = App::path('Plugin');
		foreach($paths as $path) {
			$pluginPath = $path . $pluginName;
			if(is_dir($pluginPath)) {
				break;
			}
		}
		
		$tmpPath = TMP . 'schemas' . DS . 'uninstall' . DS;
		$folder = new Folder();
		$folder->delete($tmpPath);
		$folder->create($tmpPath);

		// インストール用スキーマをdropスキーマとして一時フォルダに移動
		$path = BcUtil::getSchemaPath($pluginName);
		$folder = new Folder($path);
		$files = $folder->read(true, true);
		if (is_array($files[1])) {
			foreach ($files[1] as $file) {
				if (preg_match('/\.php$/', $file)) {
					$from = $path . DS . $file;
					$to = $tmpPath . 'drop_' . $file;
					copy($from, $to);
					chmod($to, 0666);
				}
			}
		}

		// テーブルを削除
		$this->Plugin->loadSchema('plugin', $tmpPath);

		// プラグインフォルダを削除
		$folder->delete($pluginPath);

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
	public function admin_install($name) {
		$name = urldecode($name);
		$dbInited = false;
		$installMessage = '';
		
		$paths = App::path('Plugin');

		if (!$this->request->data) {

			foreach($paths as $path) {
				$path .= $name . DS . 'config.php';
				if (file_exists($path)) {
					include $path;
					break;
				}
			}

			$this->request->data['Plugin']['name'] = $name;
			if (isset($title)) {
				$this->request->data['Plugin']['title'] = $title;
			} else {
				$this->request->data['Plugin']['title'] = $name;
			}
			$this->request->data['Plugin']['status'] = true;
			$corePlugins = Configure::read('BcApp.corePlugins');
			if (in_array($name, $corePlugins)) {
				$this->request->data['Plugin']['version'] = $this->getBaserVersion();
			} else {
				$this->request->data['Plugin']['version'] = $this->getBaserVersion($name);
			}

			$data = $this->Plugin->find('first', array('conditions' => array('name' => $this->request->data['Plugin']['name'])));
			if ($data) {
				$dbInited = $data['Plugin']['db_inited'];
			}
		} else {
			// プラグインをインストール
			if ($this->BcManager->installPlugin($this->request->data['Plugin']['name'])) {
				clearAllCache();
				$this->setMessage('新規プラグイン「' . $name . '」を baserCMS に登録しました。', false, true);
				$this->redirect(array('action' => 'index'));
			} else {
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
	public function admin_reset_db() {
		if (!$this->request->data) {
			$this->setMessage('無効な処理です。', true);
		} else {

			$data = $this->Plugin->find('first', array('conditions' => array('name' => $this->request->data['Plugin']['name'])));
			$this->Plugin->resetDb($this->request->data['Plugin']['name']);
			$data['Plugin']['db_inited'] = false;
			$this->Plugin->set($data);

			// データを保存
			if ($this->Plugin->save()) {
				clearAllCache();
				$this->BcAuth->relogin();
				$this->setMessage($data['Plugin']['title'] . ' プラグインのデータを初期化しました。', false, true);
				$this->redirect(array('action' => 'install', $data['Plugin']['name']));
			} else {
				$this->setMessage('処理中にエラーが発生しました。プラグインの開発者に確認してください。', true);
			}
		}
	}

/**
 * [ADMIN] 削除処理　(ajax)
 *
 * @param int ID
 * @return void
 * @access public
 */
	public function admin_ajax_delete($name = null) {
		/* 除外処理 */
		if (!$name) {
			$this->ajaxError(500, '無効な処理です。');
		}

		if ($this->BcManager->uninstallPlugin($name)) {
			clearAllCache();
			$this->Plugin->saveDbLog('プラグイン「' . $name . '」 を 無効化しました。');
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
	protected function _batch_del($ids) {
		if ($ids) {
			foreach ($ids as $id) {
				$data = $this->Plugin->read(null, $id);
				if ($this->BcManager->uninstallPlugin($data['Plugin']['name'])) {
					$this->Plugin->saveDbLog('プラグイン「' . $data['Plugin']['title'] . '」 を 無効化しました。');
				}
			}
			clearAllCache();
		}
		return true;
	}

}
