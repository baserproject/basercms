<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Lib
 * @since           baserCMS v 3.0.7
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcAuthComponent', 'Controller/Component');
App::uses('Router', 'Routing');

/**
 * Class BcUtil
 *
 * @package Baser.Lib
 */
class BcUtil extends CakeObject
{

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
	public static function isAdminSystem($url = null)
	{
		if (!$url) {
			$request = Router::getRequest(true);
			if ($request) {
				$url = $request->url;
			} else {
				return false;
			}
		}
		$adminPrefix = Configure::read('Routing.prefixes.0');
		return (boolean)(preg_match('/^(|\/)' . $adminPrefix . '\//', $url) || preg_match('/^(|\/)' . $adminPrefix . '$/', $url));
	}

	/**
	 * 管理ユーザーかチェック
	 *
	 * @return boolean
	 */
	public static function isAdminUser()
	{
		$user = self::loginUser('admin');
		if (empty($user['UserGroup']['id'])) {
			return false;
		}
		return ($user['UserGroup']['id'] == Configure::read('BcApp.adminGroupId'));
	}

	/**
	 * ログインユーザーのデータを取得する
	 *
	 * @return array
	 */
	public static function loginUser($prefix = 'admin')
	{
		$Session = new CakeSession();
		$sessionKey = BcUtil::authSessionKey($prefix);
		$user = $Session->read('Auth.' . $sessionKey);
		if (!$user) {
			if (!empty($_SESSION['Auth'][$sessionKey])) {
				$user = $_SESSION['Auth'][$sessionKey];
			}
		}
		return $user;
	}

	/**
	 * 現在ログインしているユーザーのユーザーグループ情報を取得する
	 *
	 * @param string $prefix ログイン認証プレフィックス
	 * @return bool|mixed ユーザーグループ情報
	 */
	public static function loginUserGroup($prefix = 'admin')
	{
		$loginUser = self::loginUser($prefix);
		if (!empty($loginUser['UserGroup'])) {
			return $loginUser['UserGroup'];
		} else {
			return false;
		}
	}

	/**
	 * 認証用のキーを取得
	 *
	 * @param string $prefix
	 * @return mixed
	 */
	public static function authSessionKey($prefix = 'admin')
	{
		return Configure::read('BcAuthPrefix.' . $prefix . '.sessionKey');
	}

	/**
	 * ログインしているユーザーのセッションキーを取得
	 *
	 * @return string
	 */
	public static function getLoginUserSessionKey()
	{
		list(, $sessionKey) = explode('.', BcAuthComponent::$sessionKey);
		return $sessionKey;
	}

	/**
	 * ログインしているユーザー名を取得
	 *
	 * @return string
	 */
	public static function loginUserName()
	{
		$user = self::loginUser();
		if (!empty($user['name'])) {
			return $user['name'];
		} else {
			return '';
		}
	}

	/**
	 * 現在適用しているテーマ梱包プラグインのリストを取得する
	 *
	 * @return array プラグインリスト
	 */
	public static function getCurrentThemesPlugins()
	{
		return BcUtil::getThemesPlugins(Configure::read('BcSite.theme'));
	}

	/**
	 * テーマ梱包プラグインのリストを取得する
	 *
	 * @param string $theme テーマ名
	 * @return array プラグインリスト
	 */
	public static function getThemesPlugins($theme)
	{
		$path = BASER_THEMES . $theme . DS . 'Plugin';
		if (is_dir($path)) {
			$Folder = new Folder($path);
			$files = $Folder->read(true, true, false);
			if (!empty($files[0])) {
				return $files[0];
			}
		}
		return [];
	}

	/**
	 * スキーマ情報のパスを取得する
	 *
	 * @param string $plugin プラグイン名
	 * @return string Or false
	 */
	public static function getSchemaPath($plugin = null)
	{

		if (!$plugin) {
			$plugin = 'Core';
		} else {
			$plugin = Inflector::camelize($plugin);
		}

		if ($plugin == 'Core') {
			return BASER_CONFIGS . 'Schema';
		}

		$paths = App::path('Plugin');
		foreach($paths as $path) {
			$_path = $path . $plugin . DS . 'Config' . DS . 'Schema';
			if (is_dir($_path)) {
				return $_path;
			}
		}

		return false;

	}

	/**
	 * 初期データのパスを取得する
	 *
	 * 初期データのフォルダは アンダースコア区切り推奨
	 *
	 * @param string $plugin プラグイン名
	 * @param string $theme テーマ名
	 * @param string $pattern 初期データの類型
	 * @return string Or false
	 */
	public static function getDefaultDataPath($plugin = null, $theme = null, $pattern = null)
	{

		if (!$plugin) {
			$plugin = 'Core';
		} else {
			$plugin = Inflector::camelize($plugin);
		}

		if (!$theme) {
			$theme = 'core';
		}

		if (!$pattern) {
			$pattern = 'default';
		}

		if ($plugin == 'Core') {
			$paths = [BASER_CONFIGS . 'data' . DS . $pattern];
			if ($theme != 'core') {
				$paths = array_merge([
					BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . $pattern,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . Inflector::camelize($pattern),
					BASER_CONFIGS . 'theme' . DS . $theme . DS . 'Config' . DS . 'data' . DS . $pattern,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . 'default',
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . 'default',
				], $paths);
			}
		} else {
			$pluginPaths = App::path('Plugin');
			foreach($pluginPaths as $pluginPath) {
				$pluginPath .= $plugin;
				if (is_dir($pluginPath)) {
					break;
				}
				$pluginPath = null;
			}
			if (!$pluginPath) {
				return false;
			}
			$paths = [
				$pluginPath . DS . 'Config' . DS . 'data' . DS . $pattern,
				$pluginPath . DS . 'Config' . DS . 'Data' . DS . $pattern,
				$pluginPath . DS . 'Config' . DS . 'Data' . DS . Inflector::camelize($pattern),
				$pluginPath . DS . 'sql',
				$pluginPath . DS . 'Config' . DS . 'data' . DS . 'default',
				$pluginPath . DS . 'Config' . DS . 'Data' . DS . 'default',
			];
			if ($theme != 'core') {
				$paths = array_merge([
					BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern . DS . $plugin,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . $pattern . DS . $plugin,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . Inflector::camelize($pattern) . DS . $plugin,
					BASER_CONFIGS . 'theme' . DS . $theme . DS . 'Config' . DS . 'data' . DS . $pattern . DS . $plugin,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . 'default' . DS . $plugin,
					BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . 'default' . DS . $plugin,
				], $paths);
			}
		}

		foreach($paths as $path) {
			if (is_dir($path)) {
				return $path;
			}
		}
		return false;

	}

	/**
	 * シリアライズ
	 *
	 * @param mixed $value 対象文字列
	 * @return string
	 */
	public static function serialize($value)
	{
		return base64_encode(serialize($value));
	}

	/**
	 * アンシリアライズ
	 * base64_decode が前提
	 *
	 * @param mixed $value 対象文字列
	 * @return mixed
	 */
	public static function unserialize($value)
	{
		$_value = $value;
		// unserializeに失敗した場合noticをを発生させfalseが戻る
		$value = unserialize(base64_decode($value));
		// 下位互換の為、しばらくの間、失敗した場合の再変換を行う v.3.0.2
		if ($value === false) {
			$value = unserialize($_value);
			if($value === false) {
				return '';
			}
		}
		return $value;
	}

	/**
	 * URL用に文字列を変換する
	 *
	 * できるだけ可読性を高める為、不要な記号は除外する
	 *
	 * @param $value
	 * @return string
	 */
	public static function urlencode($value)
	{
		$value = str_replace([
			' ', '　', '	', '\\', '\'', '|', '`', '^', '"', ')', '(', '}', '{', ']', '[', ';',
			'/', '?', ':', '@', '&', '=', '+', '$', ',', '%', '<', '>', '#', '!'
		], '_', $value);
		$value = preg_replace('/\_{2,}/', '_', $value);
		$value = preg_replace('/(^_|_$)/', '', $value);
		return urlencode($value);
	}

	/**
	 * レイアウトテンプレートのリストを取得する
	 *
	 * @param string $path
	 * @param string $plugin
	 * @param string $theme
	 * @return array
	 */
	public static function getTemplateList($path, $plugin, $theme)
	{

		if ($plugin) {
			$templatesPathes = App::path('View', $plugin);
		} else {
			$templatesPathes = App::path('View');
			if ($theme) {
				array_unshift($templatesPathes, WWW_ROOT . 'theme' . DS . $theme . DS);
			}
		}
		$_templates = [];
		foreach($templatesPathes as $templatesPath) {
			$templatesPath .= $path . DS;
			$folder = new Folder($templatesPath);
			$files = $folder->read(true, true);
			$foler = null;
			if ($files[1]) {
				if ($_templates) {
					$_templates = array_merge($_templates, $files[1]);
				} else {
					$_templates = $files[1];
				}
			}
		}
		$templates = [];
		foreach($_templates as $template) {
			$ext = Configure::read('BcApp.templateExt');
			if ($template != 'installations' . $ext) {
				$template = basename($template, $ext);
				$templates[$template] = $template;
			}
		}
		return $templates;
	}

	/**
	 * 全てのテーマを取得する
	 * @return array
	 */
	public static function getAllThemeList()
	{
		$paths = [WWW_ROOT . 'theme', BASER_VIEWS . 'Themed'];
		$themes = [];
		foreach($paths as $path) {
			$folder = new Folder($path);
			$files = $folder->read(true, true);
			if ($files[0]) {
				foreach($files[0] as $theme) {
					if ($theme !== 'core' && $theme !== '_notes') {
						$themes[$theme] = $theme;
					}
				}
			}
		}
		return $themes;
	}

	/**
	 * テーマリストを取得する
	 *
	 * @return array
	 */
	public static function getThemeList()
	{
		$themes = self::getAllThemeList();
		foreach($themes as $key => $theme) {
			if (preg_match('/^admin\-/', $theme)) {
				unset($themes[$key]);
			}
		}
		return $themes;
	}

	/**
	 * テーマリストを取得する
	 *
	 * @return array
	 */
	public static function getAdminThemeList()
	{
		$themes = self::getAllThemeList();
		foreach($themes as $key => $theme) {
			if (!preg_match('/^admin\-/', $theme)) {
				unset($themes[$key]);
			}
		}
		return $themes;
	}

	/**
	 * サブドメインを取得する
	 *
	 * @return string
	 */
	public static function getSubDomain($host = null)
	{
		$currentDomain = BcUtil::getCurrentDomain();
		if (!$currentDomain && !$host) {
			return '';
		}
		if (!$host) {
			$host = $currentDomain;
		}
		if (strpos($host, '.') === false) {
			return '';
		}
		$mainHost = BcUtil::getMainDomain();
		if ($host == $mainHost) {
			return '';
		}
		if (strpos($host, $mainHost) === false) {
			return '';
		}
		$subDomain = str_replace($mainHost, '', $host);
		if ($subDomain) {
			return preg_replace('/\.$/', '', $subDomain);
		}
		return '';
	}

	/**
	 * 指定したURLのドメインを取得する
	 *
	 * @param $url URL
	 * @return string
	 */
	public static function getDomain($url)
	{
		$mainUrlInfo = parse_url($url);
		$host = $mainUrlInfo['host'];
		if (!empty($mainUrlInfo['port'])) {
			$host .= ':' . $mainUrlInfo['port'];
		}
		return $host;
	}

	/**
	 * メインとなるドメインを取得する
	 *
	 * @return string
	 */
	public static function getMainDomain()
	{
		$mainDomain = Configure::read('BcEnv.mainDomain');
		if ($mainDomain) {
			return $mainDomain;
		} else {
			return BcUtil::getDomain(Configure::read('BcEnv.siteUrl'));
		}
	}

	/**
	 * 現在のドメインを取得する
	 *
	 * @return string
	 */
	public static function getCurrentDomain()
	{
		return Configure::read('BcEnv.host');
	}

	/**
	 * 管理画面用のプレフィックスを取得する
	 *
	 * @return string
	 */
	public static function getAdminPrefix()
	{
		return Configure::read('BcAuthPrefix.admin.alias');
	}

}
