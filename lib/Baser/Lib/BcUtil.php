<?php

class BcUtil extends Object {

/**
 * 管理システムかチェック
 * 
 * 《注意》by ryuring
 * 処理の内容にCakeRequest や、Router::parse() を使おうとしたが、
 * Router::parse() を利用すると、Routing情報が書き換えられてしまうので利用できない。
 * Router::reload() や、Router::setRequestInfo() で調整しようとしたがうまくいかなかった。
 * 
 * @return boolean
 */
	public static function isAdminSystem() {
		$url = Configure::read('BcRequest.pureUrl');
		$adminPrefix = Configure::read('Routing.prefixes.0');
		return (boolean)(preg_match('/^' . $adminPrefix . '\//', $url) || preg_match('/^' . $adminPrefix . '$/', $url));
	}

/**
 * 管理ユーザーかチェック
 * 
 * @return boolean
 */
	public static function isAdminUser() {
		$user = self::loginUser();
		if (empty($user['UserGroup']['name'])) {
			return false;
		}
		return ($user['UserGroup']['name'] == 'admins');
	}

/**
 * ログインユーザーのデータを取得する
 * 
 * @return array
 */
	public static function loginUser() {
		$Session = new CakeSession();
		$sessionKey = BcUtil::getLoginUserSessionKey();
		$user = $Session->read('Auth.' . $sessionKey);
		if (!$user) {
			if (!empty($_SESSION['Auth'][$sessionKey])) {
				$user = $_SESSION['Auth'][$sessionKey];
			}
		}
		return $user;
	}
/**
 * ログインしているユーザーのセッションキーを取得
 * 
 * @return string
 */
	public static function getLoginUserSessionKey() {
		list($dummy, $sessionKey) = explode('.', BcAuthComponent::$sessionKey);
		if (empty($sessionKey)) {
			$sessionKey = 'User';
		}
		return $sessionKey;
	}
/**
 * ログインしているユーザー名を取得
 * 
 * @return string
 */
	public static function loginUserName() {
		$user = self::loginUser();
		if (!empty($user['name'])) {
			return $user['name'];
		} else {
			return '';
		}
	}

/**
 * テーマ梱包プラグインのリストを取得する
 * 
 * @return array
 */
	public static function getCurrentThemesPlugins() {
		$theme = Configure::read('BcSite.theme');
		$path = BASER_THEMES . $theme . DS . 'Plugin';
		if(is_dir($path)) {
			$Folder = new Folder($path);
			$files = $Folder->read(true, true, false);
			if(!empty($files[0])) {
				return $files[0];
			}
		}
		return array();
	}
	
/**
 * スキーマ情報のパスを取得する
 * 
 * @param string $plugin
 * @return string Or false
 */
	public static function getSchemaPath($plugin = null) {
		
		if(!$plugin) {
			$plugin = 'Core';
		} else {
			$plugin = Inflector::camelize($plugin);
		}
		
		if($plugin == 'Core') {
			return BASER_CONFIGS . 'Schema';
		}
		
		$paths = App::path('Plugin');
		// @deprecated since 3.0.2
		// sql ディレクトリは非推奨
		$folders = array('Schema', 'sql');
		foreach ($paths as $path) {
			foreach($folders as $folder) {
				$_path = $path . $plugin . DS . 'Config' . DS . $folder;
				if (is_dir($_path)) {
					return $_path;
				}
			}
		}
		
		return false;
		
	}
	
/**
 * 初期データのパスを取得する
 * 
 * 初期データのフォルダは アンダースコア区切り推奨
 * 
 * @param string $plugin
 * @return string Or false
 */
	public static function getDefaultDataPath($plugin = null, $theme = null, $pattern = null) {
		
		if(!$plugin) {
			$plugin = 'Core';
		} else {
			$plugin = Inflector::camelize($plugin);
		}
		
		if(!$theme) {
			$theme = 'core';
		}
		
		if(!$pattern) {
			$pattern = 'default';
		}
		
		if($plugin == 'Core') {
			$paths = array(BASER_CONFIGS . 'data' . DS . $pattern);
			if($theme != 'core') {
				$paths = array_merge(array(
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . Inflector::camelize($pattern),
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . $pattern,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . 'default',
					BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . 'default',
					BASER_CONFIGS . 'theme' . DS . $theme . DS . 'Config' . DS . 'data' . DS . $pattern,
				), $paths);
			}
		} else {
			$pluginPaths = App::path('Plugin');
			foreach($pluginPaths as $pluginPath) {
				$pluginPath .= $plugin;
				if(is_dir($pluginPath)) {
					break;
				}
				$pluginPath = null;
			}
			if(!$pluginPath) {
				return false;
			}
			$paths = array(
				$pluginPath . DS . 'Config' . DS . 'Data' . DS . Inflector::camelize($pattern),
				$pluginPath . DS . 'Config' . DS . 'Data' . DS . $pattern,
				$pluginPath . DS . 'Config' . DS . 'data' . DS . $pattern,
				$pluginPath . DS . 'Config' . DS . 'Data' . DS . 'default',
				$pluginPath . DS . 'Config' . DS . 'data' . DS . 'default',
				$pluginPath . DS . 'sql',
			);
			if($theme != 'core') {
				$paths = array_merge(array(
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . Inflector::camelize($pattern) . DS . $plugin,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . $pattern . DS . $plugin,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern . DS . $plugin,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . 'default' . DS . $plugin,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . 'default' . DS . $plugin,
					BASER_CONFIGS . 'theme' . DS . $theme . DS . 'config' . DS . 'data' . DS . $pattern . DS . $plugin,
				), $paths);
			}
		}
		
		foreach ($paths as $path) {
			if (is_dir($path)) {
				return $path;
			}
		}
		return false;
		
	}
	
/**
 * シリアライズ
 * 
 * @param mixed $value
 * @return string
 */
	public static function serialize($value) {
		return base64_encode(serialize($value));
	}

/**
 * アンシリアライズ
 * base64_decode が前提
 * 
 * @param string $value
 * @return mixed
 */
	public static function unserialize($value) {
		$_value = $value;
		$value = @unserialize(base64_decode($value));
		// 下位互換の為、しばらくの間、失敗した場合の再変換を行う v.3.0.2
		if($value === false) {
			$value = unserialize($_value);
		}
		return $value;
	}

}
