<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.1.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBurgerEditor\Lib;

use Cake\Core\Configure;
use Cake\Core\Plugin;

class BurgerEditorUtil
{

	/**
	 * GoogleMapAPI Keyを取得
	 *
	 * @return string
	 */
	public static function getGoogleMapApiKey()
	{
		$googleMapsApiKey = \BaserCore\Utility\BcSiteConfig::get('google_maps_api_key');
		return $googleMapsApiKey;
	}

	/**
	 * 静的ファイルに対するサフィックスを取得する
	 *
	 * @param string filePath
	 * @return string
	 */
	public static function getSuffix($filePath)
	{
		if (!Configure::read('Bge.enableStaticFileSuffix')) {
			return '';
		}

		$modifiedTime = filemtime($filePath);
		if (!$modifiedTime) {
			return '';
		}
		$suffix = '?' . $modifiedTime;

		$suffixText = Configure::read('Bge.staticFileSuffix');
		if ($suffixText) {
			return $suffix .= '-' . $suffixText;
		}

		return $suffix;
	}

	/**
	 * Addon のパスを取得する
	 * @return string[]
	 */
	public static function getAddonPath()
	{
		$addonDir = [dirname(dirname(dirname(__FILE__))) . DS . 'Addon' . DS];
		$enableAddonPlugin = Configure::read('Bge.enableAddonPlugin');
		if ($enableAddonPlugin) {
			foreach($enableAddonPlugin as $plugin) {
				if (!Plugin::isLoaded($plugin)) {
					continue;
				}
				$plguinPath = Plugin::path($plugin);
				$pluginAddonPath = $plguinPath . 'BurgerAddon' . DS;
				if (is_dir($pluginAddonPath)) {
					$addonDir[] = $pluginAddonPath;
				}
			}
		}
		return $addonDir;
	}

	/**
	 * タイプのパスを取得する
	 * @param string $typeName
	 * @return bool|string
	 */
	public static function getTypePath($typeName)
	{
		$addonPath = self::getAddonPath();
		foreach($addonPath as $path) {
			$path = $path . 'type' . DS . $typeName . DS;
			if (is_dir($path)) {
				return $path;
			}
		}
		return false;
	}

	/**
	 * ブロックのパスを取得する
	 * @param string $typeName
	 * @return bool|string
	 */
	public static function getBlockPath($blockName)
	{
		$addonPath = self::getAddonPath();
		foreach($addonPath as $path) {
			$path = $path . 'block' . DS . $blockName . DS;
			if (is_dir($path)) {
				return $path;
			}
		}
		return false;
	}

		/**
	 * 拡張子取得
	 *
	 * @param string $filename
	 * @return boolean
	 */
	public static function getExtension($filename)
	{
		$nameAry = explode(".", $filename);
		if (!is_array($nameAry)) return false;
		return array_pop($nameAry);
	}

	/**
	 * getFileNameNoExtension
	 *
	 * @param $filename
	 * @return false|string
	 */
	public static function getFileNameNoExtension($filename)
	{
		$nameAry = explode(".", $filename);
		if (!is_array($nameAry)) return false;
		array_pop($nameAry);

		return implode('.', $nameAry);
	}

	/**
	 * マルチバイト対応 basename
	 */
	public static function mb_basename($str, $suffix = null)
	{
		$tmp = preg_split('/[\/\\\\]/', $str);
		$res = end($tmp);
		if (strlen($suffix)) {
			$suffix = preg_quote($suffix);
			$res = preg_replace("/({$suffix})$/u", "", $res);
		}
		return $res;
	}

	/**
	 * baserCMS標準のbase64UrlsafeEncodeが連続ドットのファイル名を禁止した特定サーバで
	 * 動作しないため独自定義
	 *
	 * @param string $str
	 * @return string
	 */
	public static function b64e($str)
	{
		$str = base64_encode($str);
		$ret = str_replace("..", "-D-", str_replace(['+', '/', '='], ['_', '-', '.'], $str));

		//末尾のドットをエンコード
		if (mb_substr($ret, -1) === ".") {
			return str_replace(".", "-d-", $ret);
		} else {
			return $ret;
		}
	}

	/**
	 * baserCMS標準のbase64UrlsafeEncodeが連続ドットのファイル名を禁止した特定サーバで
	 * 動作しないため独自定義のdecode版
	 * @param string $str
	 * @return string
	 */
	public static function b64d($str)
	{
		$str = str_replace("-d-", ".", $str);
		$str = str_replace(['_', '-', '.'], ['+', '/', '='], str_replace("-D-", "..", $str));
		return base64_decode($str);
	}
}
