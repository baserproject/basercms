<?php
/* SVN FILE: $Id$ */
/**
 * テーマコントローラー
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
class ThemesController extends AppController {
/**
 * コントローラー名
 * @var string
 * @access	public
 */
	var $name = 'Themes';
/**
 * モデル
 * @var array
 * @access public
 */
	var $uses = array('Theme','Page');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('BcAuth','Cookie','BcAuthConfigure', 'BcManager');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array(BC_FORM_HELPER);
/**
 * パンくずナビ
 * @var array
 * @access public
 */
	var $crumbs = array(
		array('name' => 'テーマ管理', 'url' => array('controller' => 'themes', 'action' => 'index'))
	);
/**
 * テーマ一覧
 *
 * @return void
 * @access public
 */
	function admin_index(){

		$this->pageTitle = 'テーマ一覧';
		$path = WWW_ROOT.'themed';
		$folder = new Folder($path);
		$files = $folder->read(true,true);
		$datas = array();
		$currentTheme = array();
		foreach($files[0] as $themename){
			if($themename != 'core' && $themename != '_notes'){
				if($themename == $this->siteConfigs['theme']) {
					$currentTheme = $this->_loadThemeInfo($themename);
				} else {
					$datas[] = $this->_loadThemeInfo($themename);
				}
			}
		}
		
		$this->set('datas',$datas);
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
	function admin_load_default_data_pattern() {
		
		if (empty($this->data['Theme']['default_data_pattern'])) {
			$this->setMessage('不正な操作です。', true);
			$this->redirect('index');
		}
		
		$excludes = array('plugins', 'dblogs');
		
		$user = $this->BcAuth->user();
		$User = ClassRegistry::init('User');
		$user = $User->find('first', array('conditions' => array('User.id' => $user['User']['id']), 'recursive' => -1));
		
		/* データを削除する */
		$this->BcManager->resetAllTables(null, $excludes);
		
		$dbDataPattern = $this->data['Theme']['default_data_pattern'];
		list($theme, $pattern) = explode('.', $dbDataPattern);
		$result = true;
		
		/* コアデータ */
		if (!$this->BcManager->loadDefaultDataPattern('baser', null, $pattern, $theme, 'core', $excludes)) {
			$result = false;
			$this->log($dbDataPattern." の初期データのロードに失敗しました。");
		}
		
		/* コアプラグインデータ */
		$corePlugins = Configure::read('BcApp.corePlugins');
		foreach ($corePlugins as $corePlugin) {
			if (!$this->BcManager->loadDefaultDataPattern('plugin', null, $pattern, $theme, $corePlugin, $excludes)) {
				$result = false;
				$this->log($dbDataPattern." のプラグインの初期データのロードに失敗しました。");
			}
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
		
		// システムデータの初期化
		if (!$this->BcManager->initSystemData()) {
			$result = false;
			$this->log('システムデータの初期化に失敗しました。');
		}
		
		// ユーザーデータの初期化
		$UserGroup = ClassRegistry::init('UserGroup');
		$user['User']['user_group_id'] = $UserGroup->field('id', array('UserGroup.name' => 'admins'));
		$User->create($user);
		if (!$User->save()) {
			$result = false;
			$this->log('ユーザーデータの初期化に失敗しました。手動でユーザー情報を新しく登録してください。');
		}
		
		// システム基本設定の更新
		$siteConfigs = array('SiteConfig' => array(
			'email'					=> $this->siteConfigs['email'],
			'google_analytics_id'	=> $this->siteConfigs['google_analytics_id'],
			'first_access'			=> null,
			'version'				=> $this->siteConfigs['version']
		));
		$SiteConfig = ClassRegistry::init('SiteConfig');
		$SiteConfig->saveKeyValue($siteConfigs);
		
		// メール受信テーブルの作成
		$PluginContent = ClassRegistry::init('PluginContent');
		$pluginContents = $PluginContent->find('all', array('conditions' => array('PluginContent.plugin' => 'mail')));
		$Message = ClassRegistry::init('Mail.Message');
		foreach($pluginContents as $pluginContent) {
			if($Message->createTable($pluginContent['PluginContent']['name'])) {
				if(!$Message->construction($pluginContent['PluginContent']['content_id'])) {
					$result = false;
					$this->log('メールプラグインのメール受信用テーブルの生成に失敗しました。');
				}
			} else {
				$result = false;
				$this->log('メールプラグインのメール受信用テーブルの生成に失敗しました。');
			}
		}
		
		clearAllCache();
		
		if(!$this->Page->createAllPageTemplate()){
			$result = false;
			$this->log(
					'初期データの読み込み中にページテンプレートの生成に失敗しました。' .
					'「pages」フォルダに書き込み権限が付与されていない可能性があります。' .
					'権限設定後、テーマの適用をやり直すか、表示できないページについて固定ページ管理より更新処理を行ってください。'
			);
		}
		
		if($result) {
			$this->setMessage('初期データの読み込みが完了しました。');
		} else {
			$this->setMessage('初期データの読み込みが完了しましたが、いくつかの処理に失敗しています。ログを確認してください。', true);
		}
		
		$this->redirect('index');

	}
/**
 * テーマ情報を読み込む
 * 
 * @param string $theme 
 * @return array
 * @access protected
 */
	function _loadThemeInfo($themename) {
		
		$path = WWW_ROOT.'themed';
		$title = $description = $author = $url = $screenshot = '';
		$theme = array();
		if(file_exists($path.DS.$themename.DS.'config.php')){
			include $path.DS.$themename.DS.'config.php';
		}
		if(file_exists($path.DS.$themename.DS.'screenshot.png')){
			$theme['screenshot'] = true;
		}else{
			$theme['screenshot'] = false;
		}
		if(is_writable($path.DS.$themename.DS.'pages'.DS)){
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
	function admin_edit($theme){
		
		if(!$theme){
			$this->notFound();
		}
		$themePath = WWW_ROOT.'themed'.DS.$theme.DS;
		$title = $description = $author = $url = '';
		include $themePath.'config.php';
		
		if(!$this->data){
			$this->data['Theme']['name'] = $theme;
			$this->data['Theme']['title'] = $title;
			$this->data['Theme']['description'] = $description;
			$this->data['Theme']['author'] = $author;
			$this->data['Theme']['url'] = $url;
		}else{
			$this->data['Theme']['old_name'] = $theme;
			$this->Theme->set($this->data);
			if($this->Theme->save()){
				$this->setMessage('テーマ「'.$this->data['Theme']['name'].'」を更新しました。');
				$this->redirect(array('action' => 'index'));
			}else{
				$this->setMessage('テーマ情報の変更に失敗しました。入力内容を確認してください。', true);
			}
		}

		if(is_writable($themePath)){
			$folderDisabled = '';
		}else{
			$folderDisabled = 'disabled';
			$this->data['Theme']['name'] = $theme;
		}

		if(is_writable($themePath.'config.php')){
			$configDisabled = '';
		}else{
			$configDisabled = 'disabled';
			$this->data['Theme']['title'] = $title;
			$this->data['Theme']['description'] = $description;
			$this->data['Theme']['author'] = $author;
			$this->data['Theme']['url'] = $url;
		}

		$this->pageTitle = 'テーマ情報編集';
		$this->subMenuElements = array('themes');
		$this->set('theme',$theme);
		$this->set('configDisabled',$configDisabled);
		$this->set('folderDisabled',$folderDisabled);
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
	function admin_ajax_copy($theme){

		if(!$theme){
			$this->ajaxError(500, '無効な処理です。');
		}
		$result = $this->_copy($theme);
		if($result) {
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
	function _copy($theme) {
		
		$basePath = WWW_ROOT.'themed'.DS;
		$newTheme = $theme.'_copy';
		while(true){
			if(!is_dir($basePath.$newTheme)){
				break;
			}
			$newTheme .= '_copy';
		}
		$folder = new Folder();
		if($folder->copy(array('from'=>$basePath.$theme,'to'=>$basePath.$newTheme,'mode'=>0777,'skip'=>array('_notes')))) {
			$this->Theme->saveDblog('テーマ「'.$theme.'」をコピーしました。');
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
	function admin_ajax_delete($theme){

		if(!$theme){
			$this->ajaxError(500, '無効な処理です。');
		}
		if($this->_del($theme)) {
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
	function _del($theme) {
		
		$path = WWW_ROOT.'themed'.DS.$theme;
		$folder = new Folder();
		if($folder->delete($path)) {
			$siteConfig = array('SiteConfig'=>$this->siteConfigs);
			if($theme == $siteConfig['SiteConfig']['theme']){
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
	function admin_del($theme){

		if(!$theme){
			$this->notFound();
		}
		$siteConfig = array('SiteConfig'=>$this->siteConfigs);
		$path = WWW_ROOT.'themed'.DS.$theme;
		$folder = new Folder();
		$folder->delete($path);
		if($theme == $siteConfig['SiteConfig']['theme']){
			$siteConfig['SiteConfig']['theme'] = '';
			$SiteConfig = ClassRegistry::getObject('SiteConfig');
			$SiteConfig->saveKeyValue($siteConfig);
		}
		clearViewCache();
		$this->setMessage('テーマ「'.$theme.'」を削除しました。');
		$this->redirect(array('action' => 'index'));

	}
/**
 * テーマを適用する
 *
 * @param string $theme
 * @return void
 * @access public
 */
	function admin_apply($theme){
		
		if(!$theme){
			$this->notFound();
		}
		$siteConfig['SiteConfig']['theme'] = $theme;
		$SiteConfig = ClassRegistry::getObject('SiteConfig');
		$SiteConfig->saveKeyValue($siteConfig);
		clearViewCache();
		if(!$this->Page->createAllPageTemplate()){
				$this->setMessage(
						'テーマ変更中にページテンプレートの生成に失敗しました。<br />' .
						'「pages」フォルダに書き込み権限が付与されていない可能性があります。<br />' .
						'権限設定後、テーマの適用をやり直すか、表示できないページについて固定ページ管理より更新処理を行ってください。'
				, true);
		} else {
			$this->setMessage('テーマ「'.$theme.'」を適用しました。');
		}
		$this->redirect(array('action' => 'index'));
		
	}
/**
 * 初期データセットをダウンロードする 
 */
	function admin_download_default_data_pattern() {
		
		/* コアのCSVを生成 */
		$tmpDir = TMP . 'csv' . DS;
		$Folder = new Folder();
		$Folder->create($tmpDir);
		emptyFolder($tmpDir);
		clearAllCache();
		
		$excludes = array('plugins', 'dblogs', 'users', 'favorites');
		$this->_writeCsv('baser', 'core', $tmpDir, $excludes);
		
		/* コアプラグインのCSVを生成 */
		$corePlugins = Configure::read('BcApp.corePlugins');
		foreach($corePlugins as $corePlugin) {
			$Folder->create($tmpDir . $corePlugin);
			emptyFolder($tmpDir . $corePlugin);
			$this->_writeCsv('plugin', $corePlugin, $tmpDir . $corePlugin . DS);
		}
		
		/* site_configsの編集 (email / google_analytics_id / version) */
		$targets = array('email', 'google_analytics_id', 'version');
		$path = $tmpDir . 'site_configs.csv';
		$fp = fopen($path, 'a+');
		$records = array();
		while(($record = fgetcsvReg($fp, 10240)) !== false) {
			if(in_array($record[1], $targets)) {
				$record[2] = '';
			}
			if($record[1] == 'first_access') {
				$record[2] = '1';
			}
			$records[] = '"'.implode('","', $record).'"';
		}
		ftruncate($fp, 0);
		fwrite($fp, implode("\n", $records));
		
		/* ZIPに固めてダウンロード */
		$fileName = 'default';
		App::import('Vendor','Simplezip');
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

		$db =& ConnectionManager::getDataSource($configKeyName);
		$db->cacheSources = false;
		$tables = $db->listSources();

		foreach($tables as $table) {
			if(preg_match("/^".$db->config['prefix']."([^_].+)$/", $table, $matches) &&
					!preg_match("/^".Configure::read('BcEnv.pluginDbPrefix')."[^_].+$/", $matches[1])) {
				$table = $matches[1];
				
				if(in_array($table, $exclude)) {
					continue;
				}
				
				if($plugin != 'core') {
					// プラグインの場合は対象プラグイン名が先頭にない場合スキップ
					if (!preg_match("/^".$plugin."_([^_].+)$/", $table)) {
						// メールプラグインの場合、先頭に、「mail_」 がなくとも 末尾にmessagesがあれば対象とする
						if ($plugin != 'mail') {
							continue;
						} elseif (!preg_match("/messages$/", $table)) {
							continue;
						}
					}
				}
				
				if(!$db->writeCsv(array('path' => $path . $table . '.csv', 'encoding'=>'SJIS', 'init' => true))) {
					return false;
				}
			}
		}
		
		return true;

	}
	
}
