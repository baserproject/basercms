<?php

/**
 * テーマコントローラー
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
App::uses('Simplezip', 'Vendor');

class ThemesController extends AppController {

/**
 * コントローラー名
 * @var string
 * @access	public
 */
	public $name = 'Themes';

/**
 * モデル
 * @var array
 * @access public
 */
	public $uses = array('Theme', 'Page');

/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure', 'BcManager');

/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	public $helpers = array('BcForm');

/**
 * パンくずナビ
 * @var array
 * @access public
 */
	public $crumbs = array(
		array('name' => 'テーマ管理', 'url' => array('controller' => 'themes', 'action' => 'index'))
	);

/**
 * テーマをアップロードして適用する
 */
	public function admin_add() {
		
		$this->pageTitle = 'テーマアップロード';
		$this->subMenuElements = array('themes');
		
		if($this->request->data) {
			if(empty($this->request->data['Theme']['file']['tmp_name'])) {
				$this->setMessage('ファイルのアップロードに失敗しました。', true);
			} else {
				$name = $this->request->data['Theme']['file']['name'];
				move_uploaded_file($this->request->data['Theme']['file']['tmp_name'], TMP . $name);
				exec('unzip -o ' . TMP . $name . ' -d ' . BASER_THEMES, $return);
				if(!empty($return[2])) {
					$theme = str_replace('  inflating: ' . BASER_THEMES, '', $return[2]);
					$theme = explode(DS, $theme);
					$theme = $theme[0];
					$themePath = BASER_THEMES . $theme;
					$Folder = new Folder();
					$Folder->chmod($themePath, 0777);
					unlink(TMP . $name);
					$this->_applyTheme($theme);
					$this->redirect(array('action' => 'index'));
				} else {
					$this->setMessage('アップロードしたZIPファイルの展開に失敗しました。', true);
				}
			}
		}
		
	}
	
/**
 * テーマ一覧
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		$this->pageTitle = 'テーマ一覧';
		$path = WWW_ROOT . 'theme';
		$folder = new Folder($path);
		$files = $folder->read(true, true);
		$datas = array();
		$currentTheme = array();
		foreach ($files[0] as $themename) {
			if ($themename != 'core' && $themename != '_notes') {
				if ($themename == $this->siteConfigs['theme']) {
					$currentTheme = $this->_loadThemeInfo($themename);
				} else {
					$datas[] = $this->_loadThemeInfo($themename);
				}
			}
		}
		
		$baserThemes = array();
		if(strtotime('2014-03-31 17:00:00') <= time()) {
			$cachePath = 'views' . DS . 'baser_market_themes.rss';
			if (Configure::read('debug') > 0) {
				clearCache('baser_market_themes', 'views', '.rss');
			}
			$baserThemes = cache($cachePath);
			if(!$baserThemes) {
				$Xml = new Xml();
				try {
					$baserThemes = $Xml->build(Configure::read('BcApp.marketThemeRss'));
				} catch (Exception $ex) {}
				
				if($baserThemes) {
					$baserThemes = $Xml->toArray($baserThemes->channel);
					$baserThemes = $baserThemes['channel']['item'];
					cache($cachePath, BcUtil::serialize($baserThemes));
					chmod(CACHE . $cachePath, 0666);
				} else {
					$baserThemes = array();
				}
				
			} else {
				$baserThemes = BcUtil::unserialize($baserThemes);
			}
		}
		
		$this->set('baserThemes', $baserThemes);
		$this->set('datas', $datas);
		$this->set('currentTheme', $currentTheme);
		$this->set('defaultDataPatterns', $this->BcManager->getDefaultDataPatterns($this->siteConfigs['theme'], array('useTitle' => false)));

		$this->subMenuElements = array('themes');
		$this->help = 'themes_index';
	}

/**
 * 初期データセットを読み込む
 * 
 * @return void
 */
	public function admin_load_default_data_pattern() {
		if (empty($this->request->data['Theme']['default_data_pattern'])) {
			$this->setMessage('不正な操作です。', true);
			$this->redirect('index');
		}

		$result = $this->_load_default_data_pattern($this->request->data['Theme']['default_data_pattern']);

		if ($result) {
			$this->setMessage('初期データの読み込みが完了しました。');
		} else {
			$this->setMessage('初期データの読み込みが完了しましたが、いくつかの処理に失敗しています。ログを確認してください。', true);
		}

		$this->redirect('index');
	}

/**
 * コアの初期データを読み込む
 * 
 * @return void
 */
	public function admin_reset_data() {

		$result = $this->_load_default_data_pattern('core.Default', $this->siteConfigs['theme']);

		if ($result) {
			$this->setMessage('初期データの読み込みが完了しました。');
		} else {
			$this->setMessage('初期データの読み込みが完了しましたが、いくつかの処理に失敗しています。ログを確認してください。', true);
		}

		$this->redirect('index');
		
	}
	
/**
 * 初期データを読み込む
 * 
 * @param string $dbDataPattern
 */
	protected function _load_default_data_pattern($dbDataPattern, $currentTheme = '') {
		
		$excludes = array('plugins', 'dblogs', 'users');

		$User = ClassRegistry::init('User');

		/* データを削除する */
		$this->BcManager->resetAllTables(null, $excludes);

		list($theme, $pattern) = explode('.', $dbDataPattern);
		$result = true;

		/* コアデータ */
		if (!$this->BcManager->loadDefaultDataPattern('baser', null, $pattern, $theme, 'core', $excludes)) {
			$result = false;
			$this->log($dbDataPattern . " の初期データのロードに失敗しました。");
		}
		
		/* プラグインデータ */
		$corePlugins = Configure::read('BcApp.corePlugins');
		$plugins = array_merge($corePlugins, BcUtil::getCurrentThemesPlugins());
		
		foreach ($plugins as $plugin) {
			$this->BcManager->loadDefaultDataPattern('plugin', null, $pattern, $theme, $plugin, $excludes);
		}

		if (!$result) {
			/* 指定したデータセットでの読み込みに失敗した場合、コアのデータ読み込みを試みる */
			if (!$this->BcManager->loadDefaultDataPattern('baser', null, 'default', 'core', 'core', $excludes)) {
				$this->log("コアの初期データのロードに失敗しました。");
				$result = false;
			}
			foreach ($corePlugins as $corePlugin) {
				if (!$this->BcManager->loadDefaultDataPattern('plugin', null, 'default', 'core', $corePlugin, $excludes)) {
					$this->log("コアのプラグインの初期データのロードに失敗しました。");
					$result = false;
				}
			}
			if ($result) {
				$this->setMessage('初期データの読み込みに失敗しましたので baserCMSコアの初期データを読み込みました。', true);
			} else {
				$this->setMessage('初期データの読み込みに失敗しました。データが不完全な状態です。正常に動作しない可能性があります。', true);
			}
		}
		
		clearAllCache();
		
		// メール受信テーブルの作成
		App::uses('Message', 'Mail.Model');
		$Message = new Message();
		if (!$Message->reconstructionAll()) {
			$this->log('メールプラグインのメール受信用テーブルの生成に失敗しました。');
			$result = false;
		}

		clearAllCache();

		$SiteConfig = ClassRegistry::init('SiteConfig');
		if($currentTheme) {
			$siteConfigs = array('SiteConfig' => array('theme' => $currentTheme));
			$SiteConfig->saveKeyValue($siteConfigs);
		}
		
		if (!$this->Page->createAllPageTemplate()) {
			$result = false;
			$this->log(
				'初期データの読み込み中にページテンプレートの生成に失敗しました。' .
				'「Pages」フォルダに書き込み権限が付与されていない可能性があります。' .
				'権限設定後、テーマの適用をやり直すか、表示できないページについて固定ページ管理より更新処理を行ってください。'
			);
		}

		// システムデータの初期化
		// TODO $this->BcManager->initSystemData() は、$this->Page->createAllPageTemplate() の
		// 後に呼出さないと $this->Page の実体が何故か AppModel にすりかわってしまい、
		// createAllPageTemplate メソッドが呼び出せないので注意
		if (!$this->BcManager->initSystemData(null, array('excludeUsers' => true))) {
			$result = false;
			$this->log('システムデータの初期化に失敗しました。');
		}

		// ユーザーデータの初期化
		$UserGroup = ClassRegistry::init('UserGroup');
		$adminGroupId = $UserGroup->field('id', array('UserGroup.name' => 'admins'));
		$users = $User->find('all', array('recursive' => -1));
		foreach($users as $user) {
			$user['User']['user_group_id'] = $adminGroupId;
			unset($user['User']['password']);
			if(!$User->save($user)) {
				$result = false;
				$this->log('ユーザーデータの初期化に失敗しました。手動で各ユーザーのユーザーグループの設定を行なってください。');
			}
			if(!$User->applyDefaultFavorites($user['User']['id'], $user['User']['user_group_id'])) {
				$result = false;
				$this->log('ユーザーのよく使う項目データの初期化に失敗しました。手動で各ユーザーのよく使う項目の設定を行なってください。');
			}
		}

		$Db = ConnectionManager::getDataSource('baser');
		if($Db->config['datasource'] == 'Database/BcPostgres') {
			$Db->updateSequence();
		}
		$Db = ConnectionManager::getDataSource('plugin');
		if($Db->config['datasource'] == 'Database/BcPostgres') {
			$Db->updateSequence();
		}
		
		// システム基本設定の更新
		$siteConfigs = array('SiteConfig' => array(
				'email' => $this->siteConfigs['email'],
				'google_analytics_id' => $this->siteConfigs['google_analytics_id'],
				'first_access' => null,
				'version' => $this->siteConfigs['version']
		));
		$SiteConfig->saveKeyValue($siteConfigs);
		

		
		return $result;
		
	}
	
/**
 * テーマ情報を読み込む
 * 
 * @param string $theme 
 * @return array
 * @access protected
 */
	protected function _loadThemeInfo($themename) {
		$path = WWW_ROOT . 'theme';
		$title = $description = $author = $url = $screenshot = '';
		$theme = array();
		if (file_exists($path . DS . $themename . DS . 'config.php')) {
			include $path . DS . $themename . DS . 'config.php';
		}
		if (file_exists($path . DS . $themename . DS . 'screenshot.png')) {
			$theme['screenshot'] = true;
		} else {
			$theme['screenshot'] = false;
		}
		if (is_writable($path . DS . $themename . DS . 'Pages' . DS)) {
			$theme['is_writable_pages'] = true;
		} else {
			$theme['is_writable_pages'] = false;
		}
		$theme['name'] = $themename;
		$theme['title'] = $title;
		$theme['description'] = $description;
		$theme['author'] = $author;
		$theme['url'] = $url;
		$theme['version'] = $this->getThemeVersion($theme['name']);
		return $theme;
	}

/**
 * テーマ名編集
 * 
 * @param string $theme
 * @return void
 * @access public
 */
	public function admin_edit($theme) {
		if (!$theme) {
			$this->notFound();
		}
		$themePath = WWW_ROOT . 'theme' . DS . $theme . DS;
		$title = $description = $author = $url = '';
		include $themePath . 'config.php';

		if (!$this->request->data) {
			$this->request->data['Theme']['name'] = $theme;
			$this->request->data['Theme']['title'] = $title;
			$this->request->data['Theme']['description'] = $description;
			$this->request->data['Theme']['author'] = $author;
			$this->request->data['Theme']['url'] = $url;
		} else {
			$this->request->data['Theme']['old_name'] = $theme;
			$this->Theme->set($this->request->data);
			if ($this->Theme->save()) {
				$this->setMessage('テーマ「' . $this->request->data['Theme']['name'] . '」を更新しました。');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->setMessage('テーマ情報の変更に失敗しました。入力内容を確認してください。', true);
			}
		}

		if (is_writable($themePath)) {
			$folderDisabled = '';
		} else {
			$folderDisabled = 'disabled';
			$this->request->data['Theme']['name'] = $theme;
		}

		if (is_writable($themePath . 'config.php')) {
			$configDisabled = '';
		} else {
			$configDisabled = 'disabled';
			$this->request->data['Theme']['title'] = $title;
			$this->request->data['Theme']['description'] = $description;
			$this->request->data['Theme']['author'] = $author;
			$this->request->data['Theme']['url'] = $url;
		}

		$this->pageTitle = 'テーマ情報編集';
		$this->subMenuElements = array('themes');
		$this->set('theme', $theme);
		$this->set('configDisabled', $configDisabled);
		$this->set('folderDisabled', $folderDisabled);
		$this->help = 'themes_form';
		$this->render('form');
	}

/**
 * テーマをコピーする
 *
 * @param string $theme
 * @return void
 * @access public
 */
	public function admin_ajax_copy($theme) {
		if (!$theme) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$result = $this->_copy($theme);
		if ($result) {
			exit(true);
		} else {
			$this->ajaxError(500, 'テーマフォルダのアクセス権限を見直してください。');
		}
	}

/**
 * テーマをコピーする
 *
 * @param string $theme
 * @return boolean
 * @access public
 */
	protected function _copy($theme) {
		$basePath = WWW_ROOT . 'theme' . DS;
		$newTheme = $theme . '_copy';
		while (true) {
			if (!is_dir($basePath . $newTheme)) {
				break;
			}
			$newTheme .= '_copy';
		}
		$folder = new Folder();
		if ($folder->copy(array('from' => $basePath . $theme, 'to' => $basePath . $newTheme, 'mode' => 0777, 'skip' => array('_notes')))) {
			$this->Theme->saveDblog('テーマ「' . $theme . '」をコピーしました。');
			return $this->_loadThemeInfo($newTheme);
		} else {
			return false;
		}
	}

/**
 * テーマを削除する　(ajax)
 *
 * @param string $theme
 * @return void
 * @access public
 */
	public function admin_ajax_delete($theme) {
		if (!$theme) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_del($theme)) {
			clearViewCache();
			exit(true);
		} else {
			$this->ajaxError(500, 'テーマフォルダを手動で削除してください。');
		}
		exit();
	}

/**
 * データを削除する
 * 
 * @param int $id
 * @return boolean 
 * @access protected
 */
	protected function _del($theme) {
		$path = WWW_ROOT . 'theme' . DS . $theme;
		$folder = new Folder();
		if ($folder->delete($path)) {
			$siteConfig = array('SiteConfig' => $this->siteConfigs);
			if ($theme == $siteConfig['SiteConfig']['theme']) {
				$siteConfig['SiteConfig']['theme'] = '';
				$SiteConfig = ClassRegistry::getObject('SiteConfig');
				$SiteConfig->saveKeyValue($siteConfig);
			}
			return true;
		} else {
			return false;
		}
	}

/**
 * テーマを削除する
 *
 * @param string $theme
 * @return void
 * @access public
 */
	public function admin_del($theme) {
		if (!$theme) {
			$this->notFound();
		}
		$siteConfig = array('SiteConfig' => $this->siteConfigs);
		$path = WWW_ROOT . 'theme' . DS . $theme;
		$folder = new Folder();
		$folder->delete($path);
		if ($theme == $siteConfig['SiteConfig']['theme']) {
			$siteConfig['SiteConfig']['theme'] = '';
			$SiteConfig = ClassRegistry::getObject('SiteConfig');
			$SiteConfig->saveKeyValue($siteConfig);
		}
		clearViewCache();
		$this->setMessage('テーマ「' . $theme . '」を削除しました。');
		$this->redirect(array('action' => 'index'));
	}

/**
 * テーマを適用する
 *
 * @param string $theme
 * @return void
 * @access public
 */
	public function admin_apply($theme) {
		if (!$theme) {
			$this->notFound();
		}
		
		$this->_applyTheme($theme);
		$this->redirect(array('action' => 'index'));
		
	}

	protected function _applyTheme($theme) {
		
		$plugins = BcUtil::getCurrentThemesPlugins();
		// テーマ梱包のプラグインをアンインストール
		foreach($plugins as $plugin) {
			$this->BcManager->uninstallPlugin($plugin);
		}
		
		$this->BcManager->deleteDeployedAdminAssets();
		$siteConfig['SiteConfig']['theme'] = $theme;
		$SiteConfig = ClassRegistry::getObject('SiteConfig');
		$SiteConfig->saveKeyValue($siteConfig);
		clearViewCache();
		$this->BcManager->deployAdminAssets();
		
		$info = array();
		$themePath = BASER_THEMES . $theme . DS;
		
		$Folder = new Folder($themePath . 'Plugin');
		$files = $Folder->read(true, true, false);
		if(!empty($files[0])) {
			$info = array_merge($info, array(
				'このテーマは下記のプラグインを同梱しています。'
			));
			foreach($files[0] as $file) {
				$info[] = '	・' . $file;
			}
		}
		
		Configure::write('BcSite.theme', $theme);
		$plugins = BcUtil::getCurrentThemesPlugins();
		
		App::build(array('Plugin' => array_merge(array(BASER_THEMES . $theme . DS . 'Plugin' . DS), App::path('Plugin'))));
		// テーマ梱包のプラグインをインストール
		foreach($plugins as $plugin) {
			$this->BcManager->installPlugin($plugin);
		}
		
		$path = BcUtil::getDefaultDataPath('Core', $theme);
		if(strpos($path, '/theme/' . $theme . '/') !== false) {
			if($info) {
				$info = array_merge($info, array(''));
			}
			$info = array_merge($info, array(
				'このテーマは初期データを保有しています。',
				'Webサイトにテーマに合ったデータを適用するには、初期データ読込を実行してください。',
			));

		}
		
		if (!$this->Page->createAllPageTemplate()) {
			$message = array(
				'テーマ変更中にページテンプレートの生成に失敗しました。',
				'「Pages」フォルダに書き込み権限が付与されていない可能性があります。',
				'権限設定後、テーマの適用をやり直すか、表示できないページについて固定ページ管理より更新処理を行ってください。'
			);
			if($info) {
				$message = array_merge($message, array(''), $info );
			}
			$this->setMessage(implode('<br />', $message), true);
		} else {
			$message = array('テーマ「' . $theme . '」を適用しました。');
			if($info) {
				$message = array_merge($message, array(''), $info );
			}
			
			$this->setMessage(implode('<br />', $message));
		}
		return true;
		
	}
/**
 * 初期データセットをダウンロードする 
 */
	public function admin_download_default_data_pattern() {
		/* コアのCSVを生成 */
		$tmpDir = TMP . 'csv' . DS;
		$Folder = new Folder();
		$Folder->create($tmpDir);
		emptyFolder($tmpDir);
		clearAllCache();

		$excludes = array('plugins', 'dblogs', 'users', 'favorites');
		$this->_writeCsv('baser', 'core', $tmpDir, $excludes);

		/* プラグインのCSVを生成 */
		$plugins = CakePlugin::loaded();
		foreach ($plugins as $plugin) {
			$Folder->create($tmpDir . $plugin);
			emptyFolder($tmpDir . $plugin);
			$this->_writeCsv('plugin', $plugin, $tmpDir . $plugin . DS);
		}

		/* site_configsの編集 (email / google_analytics_id / version) */
		$targets = array('email', 'google_analytics_id', 'version');
		$path = $tmpDir . 'site_configs.csv';
		$fp = fopen($path, 'a+');
		$records = array();
		while (($record = fgetcsvReg($fp, 10240)) !== false) {
			if (in_array($record[1], $targets)) {
				$record[2] = '';
			}
			$records[] = '"' . implode('","', $record) . '"';
		}
		ftruncate($fp, 0);
		fwrite($fp, implode("\n", $records));

		/* ZIPに固めてダウンロード */
		$fileName = 'default';
		$Simplezip = new Simplezip();
		$Simplezip->addFolder($tmpDir);
		$Simplezip->download($fileName);
		emptyFolder($tmpDir);
		exit();
	}

/**
 * CSVファイルを書きだす
 *
 * @param string $configKeyName
 * @param string $path
 * @return boolean
 * @access protected
 */
	function _writeCsv($configKeyName, $plugin, $path, $exclude = array()) {
		
		$pluginTables = array();
		if($plugin != 'core') {
			$pluginPath = BcUtil::getSchemaPath($plugin);
			$Folder = new Folder($pluginPath);
			$files = $Folder->read(true, true, false);
			$pluginTables = $files[1];
			foreach($pluginTables as $key => $pluginTable) {
				if(preg_match('/^(.+)\.php$/', $pluginTable, $matches)) {
					$pluginTables[$key] = $matches[1];
				} else {
					unset($pluginTables[$key]);
				}
			}
		}
		
		$pluginKey = Inflector::underscore($plugin);
		$db = ConnectionManager::getDataSource($configKeyName);
		$db->cacheSources = false;
		$tables = $db->listSources();

		$result = true;
		foreach ($tables as $table) {
			if (preg_match("/^" . $db->config['prefix'] . "([^_].+)$/", $table, $matches) &&
				!preg_match("/^" . Configure::read('BcEnv.pluginDbPrefix') . "[^_].+$/", $matches[1])) {
				$table = $matches[1];

				if (in_array($table, $exclude)) {
					continue;
				}

				if ($pluginKey != 'core') {
					// プラグインの場合は対象プラグイン名が先頭にない場合スキップ
					//if (!preg_match("/^" . $pluginKey . "_([^_].+)$/", $table)) {
					if(!in_array($table, $pluginTables)) {
						// メールプラグインの場合、先頭に、「mail_」 がなくとも 末尾にmessagesがあれば対象とする
						if ($pluginKey != 'mail') {
							continue;
						} elseif (!preg_match("/messages$/", $table)) {
							continue;
						}
					}
				}

				if (!$db->writeCsv(array(
						'path' => $path . $table . '.csv',
						'encoding' => 'SJIS',
						'init' => false,
						'plugin' => ($plugin == 'core') ? null : $plugin
					))) {
					$result = false;
				}
			}
		}

		return $result;
	}

}
