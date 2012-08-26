<?php
/* SVN FILE: $Id$ */
/**
 * BcManagerコンポーネント
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.components
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class BcManagerComponent extends Object {
/**
 * データベースを初期化する
 * 
 * @param type $reset
 * @param type $dbConfig
 * @param type $nonDemoData
 * @return type
 * @access public 
 */
	function initDb($reset = true, $dbConfig = null, $nonDemoData = false) {
		
		if($reset) {
			$this->deleteTables();
			$this->deleteTables('plugin');
		}
		
		return $this->constructionDb($dbConfig, $nonDemoData);
		
	}
/**
 * データベースを構築する
 * 
 * @param array $dbConfig
 * @param boolean $nonDemoData
 * @return boolean
 * @access public
 */
	function constructionDb($dbConfig = null, $nonDemoData = false) {

		if(!$this->constructionTable(BASER_CONFIGS.'sql', 'baser', $dbConfig, $nonDemoData)) {
			return false;
		}

		$dbConfig['prefix'].=Configure::read('BcEnv.pluginDbPrefix');
		$corePlugins = Configure::read('BcApp.corePlugins');
		foreach($corePlugins as $corePlugin) {
			if(!$this->constructionTable(BASER_PLUGINS.$corePlugin.DS.'config'.DS.'sql', 'plugin', $dbConfig, $nonDemoData)) {
				return false;
			}
		}
		return true;

	}
/**
 * テーブルを構築する
 *
 * @param string	$path
 * @param string	$dbConfigKeyName
 * @param string	$dbConfig
 * @param string	$nonDemoData
 * @return boolean
 * @access public
 */
	function constructionTable($path, $dbConfigKeyName = 'baser', $dbConfig = null, $nonDemoData = false) {

		$db =& $this->_getDataSource($dbConfigKeyName, $dbConfig);
		$driver = preg_replace('/^bc_/', '', $db->config['driver']);
		
		if (!$db->connected && $driver != 'csv') {
			return false;
		} elseif($driver == 'csv') {
			// CSVの場合はフォルダを作成する
			$folder = new Folder($db->config['database'], true, 0777);
		} elseif($driver == 'sqlite3') {
			$db->connect();
			chmod($db->config['database'], 0666);
		}

		$folder = new Folder($path);
		$files = $folder->read(true, true, true);

		if(isset($files[1])) {

			// DB構築
			foreach($files[1] as $file) {

				if(!preg_match('/\.php$/',$file)) {
					continue;
				}
				if(!$db->createTableBySchema(array('path'=>$file))){
					return false;
				}
				
			}

			if($nonDemoData && $configKeyName == 'baser') {
				$nonDemoData = false;
				$folder = new Folder($path.DS.'non_demo');
				$files = $folder->read(true, true, true);
			}
			if(!$nonDemoData) {

				// CSVの場合ロックを解除しないとデータの投入に失敗する
				if($driver == 'csv') {
					$db->reconnect();
				}

				// 初期データ投入
				foreach($files[1] as $file) {
					if(!preg_match('/\.csv$/',$file)) {
						continue;
					}
					if(!$db->loadCsv(array('path'=>$file, 'encoding'=>'SJIS'))){
						return false;
					}
				}
			}
		}
		
		return true;

	}
/**
 * 全てのテーブルを削除する
 * 
 * @param array $dbConfig 
 * @return boolean
 * @access public
 */
	function deleteAllTables($dbConfig = null) {
		
		$result = true;
		if(!$this->deleteTables('baser', $dbConfig)) {
			$result = false;
		}
		$dbConfig['prefix'] .= Configure::read('BcEnv.pluginDbPrefix');
		if(!$this->deleteTables('plugin', $dbConfig)) {
			$result = false;
		}
		return $result;
		
	}
/**
 * テーブルを削除する
 * 
 * @param string $dbConfigKeyName
 * @param array $dbConfig
 * @return boolean
 * @access public
 * TODO 処理を DboSource に移動する
 */
	function deleteTables($dbConfigKeyName = 'baser', $dbConfig = null) {

		$db =& $this->_getDataSource($dbConfigKeyName, $dbConfig);
		$dbConfig = $db->config;
		
		/* 削除実行 */
		// TODO schemaを有効活用すればここはスッキリしそうだが見送り
		$dbType = preg_replace('/^bc_/', '', $dbConfig['driver']);
		switch ($dbType) {
			case 'mysql':
				$sources = $db->listSources();
				foreach($sources as $source) {
					if(preg_match("/^".$dbConfig['prefix']."([^_].+)$/", $source)) {
						$sql = 'DROP TABLE '.$source;
						$db->execute($sql);
					}
				}
				break;

			case 'postgres':
				$sources = $db->listSources();
				foreach($sources as $source) {
					if(preg_match("/^".$dbConfig['prefix']."([^_].+)$/", $source)) {
						$sql = 'DROP TABLE '.$source;
						$db->execute($sql);
					}
				}
				// シーケンスも削除
				$sql = "SELECT sequence_name FROM INFORMATION_SCHEMA.sequences WHERE sequence_schema = '{$dbConfig['schema']}';";
				$sequences = $db->query($sql);
				$sequences = Set::extract('/0/sequence_name',$sequences);
				foreach($sequences as $sequence) {
					if(preg_match("/^".$dbConfig['prefix']."([^_].+)$/", $sequence)) {
						$sql = 'DROP SEQUENCE '.$sequence;
						$db->execute($sql);
					}
				}
				break;

			case 'sqlite':
			case 'sqlite3':
				@unlink($dbConfig['database']);
				break;

			case 'csv':
				$folder = new Folder($dbConfig['database']);
				$files = $folder->read(true,true,true);
				foreach($files[1] as $file) {
					if(basename($file) != 'empty') {
						@unlink($file);
					}
				}
				break;

		}
		return true;

	}
/**
 * データソースを取得する
 * 
 * @param string $configKeyName
 * @param array $dbConfig
 * @return DataSource
 * @access public
 */
	function &_getDataSource($dbConfigKeyName = 'baser', $dbConfig = null) {
		
		if($dbConfig) {
			$db =& ConnectionManager::create($dbConfigKeyName, $dbConfig);
			if(!$db) {
				$db =& ConnectionManager::getDataSource($dbConfigKeyName);
			}
		} else {
			$db =& ConnectionManager::getDataSource($dbConfigKeyName);
		}
		
		return $db;
		
	}
/**
 * テーマを配置する
 *
 * @param string $theme
 * @return boolean
 * @access public
 */
	function deployTheme($theme = 'demo') {

		$targetPath = WWW_ROOT.'themed'.DS.$theme;
		$sourcePath = BASER_CONFIGS.'theme'.DS.$theme;
		$folder = new Folder();
		$folder->delete($targetPath);
		if($folder->copy(array('to'=>$targetPath,'from'=>$sourcePath,'mode'=>0777,'skip'=>array('_notes')))) {
			if($folder->create($targetPath.DS.'pages',0777)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}

	}
/**
 * 設定ファイルをリセットする
 * 
 * @return boolean 
 * @access public
 */
	function resetSetting() {

		$result = true;
		if(file_exists(CONFIGS.'database.php')) {
			if(!unlink(CONFIGS.'database.php')) {
				$result = false;
			}
		}
		if(file_exists(CONFIGS.'install.php')) {
			if(!unlink(CONFIGS.'install.php')) {
				$result = false;
			}
		}
		return $result;
		
	}
/**
 * テーマのページテンプレートを初期化する 
 * 
 * @return boolean
 * @access public
 */
	function resetThemePages() {
		
		$result = true;
		$themeFolder = new Folder(WWW_ROOT.'themed');
		$themeFiles = $themeFolder->read(true,true,true);
		foreach($themeFiles[0] as $theme){
			$pagesFolder = new Folder($theme.DS.'pages');
			$pathes = $pagesFolder->read(true,true,true);
			foreach($pathes[0] as $path){
				if(basename($path) != 'admin') {
					$folder = new Folder();
					if(!$folder->delete($path)) {
						$result = false;
					}
					$folder = null;
				}
			}
			foreach($pathes[1] as $path){
				if(basename($path) != 'empty') {
					if(!unlink($path)) {
						$result = false;
					}
				}
			}
			$pagesFolder = null;
		}
		$themeFolder = null;
		return $result;
		
	}
/**
 * baserCMSをリセットする
 * 
 * @param array $dbConfig 
 * @access public
 */
	function reset($dbConfig) {
		
		if(Configure::read('debug') != -1) {
			$this->log('baserCMSの初期化を行うには、debug を -1 に設定する必要があります。');
			return false;
		}

		$result = true;

		// スマートURLをオフに設定
		if($this->smartUrl()) {
			if(!$this->setSmartUrl(false)){
				$result = false;
				$this->log('スマートURLの設定を正常に初期化できませんでした。');
			}
		}
		
		if(BC_INSTALLED) {
			// 設定ファイルを初期化
			if(!$this->resetSetting()) {
				$result = false;
				$this->log('設定ファイルを正常に初期化できませんでした。');
			}
			// テーブルを全て削除
			if(!$this->deleteAllTables($dbConfig)) {
				$result = false;
				$this->log('データベースを正常に初期化できませんでした。');
			}
		}
		
		// テーマのページテンプレートを初期化
		if(!$this->resetThemePages()) {
			$result = false;
			$this->log('テーマのページテンプレートを初期化できませんでした。');
		}
		
		ClassRegistry::flush();
		clearAllCache();

		return $result;

	}
/**
 * スマートURLの設定を取得
 *
 * @return	boolean
 * @access	public
 */
	function smartUrl(){
		if (Configure::read('App.baseUrl')) {
			return false;
		} else {
			return true;
		}
	}
/**
 * スマートURLの設定を行う
 *
 * @param	boolean	$smartUrl
 * @return	boolean
 * @access	public
 */
	function setSmartUrl($smartUrl) {

		/* install.php の編集 */
		if($smartUrl) {
			if(!$this->setInstallSetting('App.baseUrl', "''")){
				return false;
			}
		} else {
			if(!$this->setInstallSetting('App.baseUrl', '$_SERVER[\'SCRIPT_NAME\']')){
				return false;
			}
		}

		if(BC_DEPLOY_PATTERN == 2 || BC_DEPLOY_PATTERN == 3) {
			$webrootRewriteBase = '/';
		} else {
			$webrootRewriteBase = '/'.APP_DIR.'/webroot';
		}

		/* /app/webroot/.htaccess の編集 */
		$this->_setSmartUrlToHtaccess(WWW_ROOT.'.htaccess', $smartUrl, 'webroot', $webrootRewriteBase);

		if(BC_DEPLOY_PATTERN == 1) {
			/* /.htaccess の編集 */
			$this->_setSmartUrlToHtaccess(ROOT.DS.'.htaccess', $smartUrl, 'root', '/');
		}

		return true;

	}
/**
 * .htaccess にスマートURLの設定を書きこむ
 *
 * @param	string	$path
 * @param	array	$rewriteSettings
 * @return	boolean
 * @access	protected
 */
	function _setSmartUrlToHtaccess($path, $smartUrl, $type, $rewriteBase = '/') {

		//======================================================================
		// WindowsのXAMPP環境では、何故か .htaccess を書き込みモード「w」で開けなかったの
		// で、追記モード「a」で開くことにした。そのため、実際の書き込み時は、 ftruncate で、
		// 内容をリセットし、ファイルポインタを先頭に戻している。
		//======================================================================

		$rewritePatterns = array(	"/\n[^\n#]*RewriteEngine.+/i",
									"/\n[^\n#]*RewriteBase.+/i",
									"/\n[^\n#]*RewriteCond.+/i",
									"/\n[^\n#]*RewriteRule.+/i");
		switch($type) {
			case 'root':
				$rewriteSettings = array(	'RewriteEngine on',
											'RewriteBase '.$this->getRewriteBase($rewriteBase),
											'RewriteRule ^$ '.APP_DIR.'/webroot/ [L]',
											'RewriteRule (.*) '.APP_DIR.'/webroot/$1 [L]');
				break;
			case 'webroot':
				$rewriteSettings = array(	'RewriteEngine on',
											'RewriteBase '.$this->getRewriteBase($rewriteBase),
											'RewriteCond %{REQUEST_FILENAME} !-d',
											'RewriteCond %{REQUEST_FILENAME} !-f',
											'RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]');
				break;
		}

		$file = new File($path);
		$file->open('a+');
		$data = $file->read();
		foreach ($rewritePatterns as $rewritePattern) {
			$data = preg_replace($rewritePattern, '', $data);
		}
		if($smartUrl) {
			$data .= "\n".implode("\n", $rewriteSettings);
		}
		ftruncate($file->handle,0);
		if(!$file->write($data)){
			$file->close();
			return false;
		}
		$file->close();

	}
/**
 * RewriteBase の設定を取得する
 *
 * @param	string	$base
 * @return	string
 */
	function getRewriteBase($url){

		$baseUrl = BC_BASE_URL;
		if(preg_match("/index\.php/", $baseUrl)){
			$baseUrl = str_replace('index.php/', '', $baseUrl);
		}
		$baseUrl = preg_replace("/\/$/",'',$baseUrl);
		if($url != '/' || !$baseUrl) {
			$url = $baseUrl.$url;
		}else{
			$url = $baseUrl;
		}

		return $url;

	}
/**
 * インストール設定を書き換える
 *
 * @param	string	$key
 * @param	string	$value
 * @return	boolean
 * @access	public
 */
	function setInstallSetting($key, $value) {
		
		/* install.php の編集 */
		$setting = "Configure::write('".$key."', ".$value.");\n";
		$key = str_replace('.', '\.', $key);
		$pattern = '/Configure\:\:write[\s]*\([\s]*\''.$key.'\'[\s]*,[\s]*([^\s]*)[\s]*\);\n/is';
		$file = new File(CONFIGS.'install.php');
		if(file_exists(CONFIGS.'install.php')) {
			$data = $file->read();
		}else {
			$data = "<?php\n?>";
		}
		if(preg_match($pattern, $data)) {
			$data = preg_replace($pattern, $setting, $data);
		} else {
			$data = preg_replace("/\n\?>/is", "\n".$setting.'?>', $data);
		}
		$return = $file->write($data);
		$file->close();
		return $return;

	}
	
}