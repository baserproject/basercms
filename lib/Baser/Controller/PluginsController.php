<?php

/**
 * Plugin 拡張クラス
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
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
 * @property Menu $Menu
 * @property Plugin $Plugin
 * @property PluginContent $PluginContent
 * @property BcManagerComponent $BcManager
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
 *
 * @return void
 */
	public function admin_add() {
		$this->pageTitle = 'プラグインアップロード';
		$this->subMenuElements = array('plugins');

		if ($this->request->data) {
			if (empty($this->request->data['Plugin']['file']['tmp_name'])) {
				$this->setMessage('ファイルのアップロードに失敗しました。', true);
			} else {
				$name = $this->request->data['Plugin']['file']['name'];
				move_uploaded_file($this->request->data['Plugin']['file']['tmp_name'], TMP . $name);
				exec('unzip -o ' . TMP . $name . ' -d ' . APP.'Plugin'.DS, $return);
				if (!empty($return[2])) {
					$plugin = preg_replace('/^\s*?(creating|inflating):\s*' . preg_quote(APP . 'Plugin' . DS, '/') . '/', '', $return[2]);
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
                    $msg = 'アップロードしたZIPファイルの展開に失敗しました。';
                    exec('unzip 2>&1', $errs);
                    $msg .= '<br />'.implode('<br />', $errs);
					$this->setMessage($msg, true);
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
		$this->Plugin->cacheQueries = false;
		$datas = $this->Plugin->find('all', array('order' => 'Plugin.priority'));
		if (!$datas) {
			$datas = array();
		}

		// プラグインフォルダーのチェックを行う。
		$pluginInfos = array();
		$paths = App::path('Plugin');
		foreach ($paths as $path) {
			$Folder = new Folder($path);
			$files = $Folder->read(true, true, true);
			foreach ($files[0] as $file) {
				$pluginInfos[basename($file)] = $this->_getPluginInfo($datas, $file);
			}
		}

		$pluginInfos = array_values($pluginInfos); // Hash::sortの為、一旦キーを初期化
		$pluginInfos = array_reverse($pluginInfos); // Hash::sortの為、逆順に変更

		$availables = $unavailables = array();
		foreach($pluginInfos as $pluginInfo) {
			if(isset($pluginInfo['Plugin']['priority'])) {
				$availables[] = $pluginInfo;
			} else {
				$unavailables[] = $pluginInfo;
			}
		}		
		
		//並び替えモードの場合はDBにデータが登録されていないプラグインを表示しない
		if (!empty($this->passedArgs['sortmode'])) {
			$sortmode = true;
			$pluginInfos = Hash::sort($availables, '{n}.Plugin.priority', 'asc', 'numeric');
		} else {
			$sortmode = false;
			$pluginInfos = array_merge(Hash::sort($availables, '{n}.Plugin.priority', 'asc', 'numeric'), $unavailables);
		}

		// 表示設定
		$this->set('datas', $pluginInfos);
		$this->set('corePlugins', Configure::read('BcApp.corePlugins'));
		$this->set('sortmode', $sortmode);

		if ($this->request->is('ajax')) {
			$this->render('ajax_index');
		}

		$this->subMenuElements = array('plugins');
		$this->pageTitle = 'プラグイン一覧';
		$this->help = 'plugins_index';
	}

/**
 * baserマーケットのプラグインデータを取得する
 *
 * @return void
 */
	public function admin_ajax_get_market_plugins() {
		$baserPlugins = array();

		$cachePath = 'views' . DS . 'baser_market_plugins.rss';
		if (Configure::read('debug') > 0) {
			clearCache('baser_market_plugins', 'views', '.rss');
		}
		$baserPlugins = cache($cachePath);
		if (!$baserPlugins) {
			$Xml = new Xml();
			try {
				$baserPlugins = $Xml->build(Configure::read('BcApp.marketPluginRss'));
			} catch (Exception $ex) {

			}
			if ($baserPlugins) {
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

		$this->set('baserPlugins', $baserPlugins);
	}

/**
 * プラグイン情報を取得する
 * 
 * @param array $datas
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
 * @param string $pluginName プラグイン名
 * @return void
 *
 * @deprecated admin_ajax_delete_file に移行
 */
	public function admin_delete_file($pluginName) {
		$this->__deletePluginFile($pluginName);
		$this->setMessage('プラグイン「' . $pluginName . '」 を完全に削除しました。');
		$this->redirect(array('action' => 'index'));
	}

/**
 * 並び替えを更新する [AJAX]
 *
 * @return bool
 */
	public function admin_ajax_update_sort() {
		if ($this->request->data) {
			if ($this->Plugin->changePriority($this->request->data['Sort']['id'], $this->request->data['Sort']['offset'])) {
				clearViewCache();
				clearDataCache();
				echo true;
			} else {
				$this->ajaxError(500, '一度リロードしてから再実行してみてください。');
			}
		} else {
			$this->ajaxError(500, '無効な処理です。');
		}
		exit();
	}
/**
 * [ADMIN] ファイル削除
 *
 * @param string $pluginName プラグイン名
 * @return void
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
 * @param string $pluginName プラグイン名
 * @return void
 */
	private function __deletePluginFile($pluginName) {
		$paths = App::path('Plugin');
		foreach ($paths as $path) {
			$pluginPath = $path . $pluginName;
			if (is_dir($pluginPath)) {
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
 * @param string $name プラグイン名
 * @return void
 */
	public function admin_install($name) {
		$name = urldecode($name);
		$dbInited = false;
		$installMessage = '';

		$paths = App::path('Plugin');

		if (!$this->request->data) {

			foreach ($paths as $path) {
				$path .= $name . DS . 'config.php';
				if (file_exists($path)) {
					include $path;
					break;
				}
			}

			if (!isset($title)) {
				$title = $name;
			}
			$corePlugins = Configure::read('BcApp.corePlugins');
			if (in_array($name, $corePlugins)) {
				$version = $this->getBaserVersion();
			} else {
				$version = $this->getBaserVersion($name);
			}
			
			$this->request->data = array('Plugin' => array(
				'name'	=> $name,
				'title'	=> $title,
				'status'=> true,
				'version'	=> $version,
				'permission'=> 1
			));

			$data = $this->Plugin->find('first', array('conditions' => array('name' => $this->request->data['Plugin']['name'])));
			if ($data) {
				$dbInited = $data['Plugin']['db_inited'];
			}
		} else {
			// プラグインをインストール
			if ($this->BcManager->installPlugin($this->request->data['Plugin']['name'])) {
				clearAllCache();
				$this->setMessage('新規プラグイン「' . $name . '」を baserCMS に登録しました。', false, true);

				$plugin = $this->Plugin->findByName($name);
				$dirPath = $this->Plugin->getDirectoryPath($name);
				$pluginInfo = $this->_getPluginInfo(array($plugin), $dirPath);

				if(!empty($pluginInfo['Plugin']['admin_link'])) {
					if (ClassRegistry::isKeySet('Favorite')) {
						$Favorite = ClassRegistry::getObject('Favorite');
					} else {
						$Favorite = ClassRegistry::init('Favorite');
					}
					$user = $this->BcAuth->user();
					$adminLinkUrl = preg_replace('|^/index.php|', '', Router::url($pluginInfo['Plugin']['admin_link']));
					$favorite = array(
						'name' => $pluginInfo['Plugin']['title'] . '管理',
						'url' => $adminLinkUrl,
						'sort' => $Favorite->getMax('sort') + 1,
						'user_id' => $user['id'],
					);

					if(!$Favorite->find('count', array('conditions' => array('Favorite.url' => $adminLinkUrl), 'recirsive' => -1))) {
						$Favorite->create($favorite);
						$Favorite->save();
					}
				}
				
				$this->_addPermission($this->request->data);
				
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
 * アクセス制限設定を追加する
 * 
 * @param array $data リクエストデータ
 */
	public function _addPermission($data) {
		
		if (ClassRegistry::isKeySet('Permission')) {
			$Permission = ClassRegistry::getObject('Permission');
		} else {
			$Permission = ClassRegistry::init('Permission');
		}
		
		$userGroups = $Permission->UserGroup->find('all', array('conditions' => array('UserGroup.id <>' => Configure::read('BcApp.adminGroupId')), 'recursive' => -1));
		if($userGroups) {
			foreach($userGroups as $userGroup) {
				//$permissionAuthPrefix = $Permission->UserGroup->getAuthPrefix($userGroup['UserGroup']['id']);
				// TODO 現在 admin 固定、今後、mypage 等にも対応する
				$permissionAuthPrefix = 'admin';
				$url = '/' . $permissionAuthPrefix . '/' . Inflector::underscore($data['Plugin']['name']) . '/*';
				$permission = $Permission->find('first', array('conditions' => array('Permission.url' => $url), 'recursive' => -1));
				switch ($data['Plugin']['permission']) {
					case 1:
						if($permission) {
							$Permission->delete($permission['Permission']['id']);
						}
						break;
					case 2:
						if(!$permission) {
							$Permission->create(array(
								'name'			=> $data['Plugin']['title'] . '管理',
								'user_group_id'	=> $userGroup['UserGroup']['id'],
								'auth'			=> true,
								'status'		=> true,
								'url'			=> $url,
								'no'			=> $Permission->getMax('no', array('user_group_id' => $userGroup['UserGroup']['id'])) + 1,
								'sort'			=> $Permission->getMax('sort', array('user_group_id' => $userGroup['UserGroup']['id'])) + 1
							));
							$Permission->save();
						}
						break;
				}
			}
		}
		
	}

/**
 * データベースをリセットする
 *
 * @return void
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
 * @param string $name プラグイン名
 * @return void
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
 * @param array $ids プラグインIDの配列
 * @return bool
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
