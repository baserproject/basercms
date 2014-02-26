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
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright	  Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link		  http://cakephp.org CakePHP(tm) Project
 * @package		  Cake.Routing
 * @since		  CakePHP(tm) v 2.2
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('DispatcherFilter', 'Routing');

/**
 * Filters a request and tests whether it is a file in the webroot folder or not and
 * serves the file to the client if appropriate.
 *
 * @package Cake.Routing.Filter
 */
class BcAssetDispatcher extends DispatcherFilter {

/**
 * Default priority for all methods in this filter
 * This filter should run before the request gets parsed by router
 *
 * @var int
 */
	public $priority = 9;

/**
 * Checks if a requested asset exists and sends it to the browser
 *
 * @param CakeEvent $event containing the request and response object
 * @return CakeResponse if the client is requesting a recognized asset, null otherwise
 */
	public function beforeDispatch(CakeEvent $event) {
		$url = urldecode($event->data['request']->url);
		if (strpos($url, '..') !== false || strpos($url, '.') === false) {
			return;
		}

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

/**
 * Sends an asset file to the client
 *
 * @param CakeResponse $response The response object to use.
 * @param string $assetFile Path to the asset file in the file system
 * @param string $ext The extension of the file to determine its mime type
 * @return void
 */
	protected function _deliverAsset(CakeResponse $response, $assetFile, $ext) {
		ob_start();
		$compressionEnabled = Configure::read('Asset.compress') && $response->compress();
		if ($response->type($ext) == $ext) {
			$contentType = 'application/octet-stream';
			$agent = env('HTTP_USER_AGENT');
			if (preg_match('%Opera(/| )([0-9].[0-9]{1,2})%', $agent) || preg_match('/MSIE ([0-9].[0-9]{1,2})/', $agent)) {
				$contentType = 'application/octetstream';
			}
			$response->type($contentType);
		}
		if (!$compressionEnabled) {
			$response->header('Content-Length', filesize($assetFile));
		}
		$response->cache(filemtime($assetFile));
		$response->send();
		ob_clean();
		if ($ext === 'css' || $ext === 'js') {
			include $assetFile;
		} else {
			readfile($assetFile);
		}

		if ($compressionEnabled) {
			ob_end_flush();
		}
	}

}
