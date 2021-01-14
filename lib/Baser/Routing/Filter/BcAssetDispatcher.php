<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Routing.Filter
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('AssetDispatcher', 'Routing/Filter');

/**
 * Class BcAssetDispatcher
 *
 * /app/View/webroot/ や、/baser/View/webroot/ 内のアセットファイルを
 * 読み込めるようにする為のフィルター
 *
 * （例）/css/style.css では、次のファイルを参照する事ができる
 *        /app/View/webroot/css/style.css
 *        /lib/Baser/View/webroot/css/style.css
 *
 * @package Baser.Routing.Filter
 */
class BcAssetDispatcher extends AssetDispatcher
{

	/**
	 * Default priority for all methods in this filter
	 * This filter should run before the request gets parsed by router
	 * @var int
	 */
	// CUSTOMIZE MODIFY 2016/07/17 ryuring
	// >>>
	//public $priority = 9;
	// ---
	public $priority = 4;
	// <<<

// CUSTOMIZE MODIFY 2016/07/17 ryuring
// 継承元を呼び出す前提でオーバーライド
// >>>
	/**
	 * Builds asset file path based off url
	 *
	 * @param string $url URL
	 * @return string|null Absolute path for asset file
	 */
	protected function _getAssetFile($url)
	{
		$path = parent::_getAssetFile($url);
		if (!empty($path)) {
			return $path;
		}
		$parts = explode('/', $url);
		$fileFragment = implode(DS, $parts);
		$path = BASER_WEBROOT;
		if (file_exists($path . $fileFragment)) {
			return $path . $fileFragment;
		}
		return null;
	}
// <<<
}
