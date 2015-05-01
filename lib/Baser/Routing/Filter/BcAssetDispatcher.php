<?php
/**
 * BcAssetDispatcher
 * 
 * /app/View/webroot/ や、/baser/View/webroot/ 内のアセットファイルを
 * 読み込めるようにする為のフィルター
 * 
 * （例）/css/style.css では、次のファイルを参照する事ができる
 * 		/app/View/webroot/css/style.css
 * 		/lib/Baser/View/webroot/css/style.css
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Routing.Filter
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('AssetDispatcher', 'Routing/Filter');

/**
 * BcAssetDispatcher class
 *
 * @package Baser.Routing.Filter
 */
class BcAssetDispatcher extends AssetDispatcher {

/**
 * Default priority for all methods in this filter
 * This filter should run before the request gets parsed by router
 * @var int
 */
	public $priority = 4;

/**
 * Builds asset file path based off url
 *
 * @param string $url URL
 * @return string|null Absolute path for asset file
 */
	protected function _getAssetFile($url) {
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

}
