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
 * Checks if a requested asset exists and sends it to the browser
 *
 * @param CakeEvent $event containing the request and response object
 * @return mixed The resulting response.
 * @throws NotFoundException When asset not found
 */
	public function beforeDispatch(CakeEvent $event) {
		$url = urldecode($event->data['request']->url);
		if (strpos($url, '..') !== false || strpos($url, '.') === false) {
			return;
		}

		// CUSTOMIZE DELETE 2014/07/02 ryuring
		// >>>
		/*if ($result = $this->_filterAsset($event)) {
			$event->stopPropagation();
			return $result;
		}*/
		// <<<

		$assetFile = $this->_getAssetFile($url);
		if ($assetFile === null || !file_exists($assetFile)) {
			return null;
		}
		$response = $event->data['response'];
		$event->stopPropagation();

		$response->modified(filemtime($assetFile));
		if ($response->checkNotModified($event->data['request'])) {
			return $response;
		}

		$pathSegments = explode('.', $url);
		$ext = array_pop($pathSegments);

		$this->_deliverAsset($response, $assetFile, $ext);
		return $response;
	}

/**
 * Builds asset file path based off url
 *
 * @param string $url
 * @return string Absolute path for asset file
 */
	protected function _getAssetFile($url) {
		$parts = explode('/', $url);
		// CUSTOMIZE MODIFY 2014/07/02 ryuring
		// >>>
		/*if ($parts[0] === 'theme') {
			$themeName = $parts[1];
			unset($parts[0], $parts[1]);
			$fileFragment = implode(DS, $parts);
			$path = App::themePath($themeName) . 'webroot' . DS;
			return $path . $fileFragment;
		}

		$plugin = Inflector::camelize($parts[0]);
		if ($plugin && CakePlugin::loaded($plugin)) {
			unset($parts[0]);
			$fileFragment = implode(DS, $parts);
			$pluginWebroot = CakePlugin::path($plugin) . 'webroot' . DS;
			return $pluginWebroot . $fileFragment;
		}*/
		// <<<
		$fileFragment = implode(DS, $parts);
		$path = APP . 'View' . DS . 'webroot' . DS;
		if (file_exists($path . $fileFragment)) {
			return $path . $fileFragment;
		} else {
			$path = BASER_WEBROOT;
			if (file_exists($path . $fileFragment)) {
				return $path . $fileFragment;
			}
		}
	}

}
