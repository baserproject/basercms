<?php
/**
 * CacheHelper helps create full page view caching.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Helper
 * @since         CakePHP(tm) v 1.0.0.2277
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('CacheHelper', 'View/Helper');

/**
 * CacheHelper helps create full page view caching.
 *
 * When using CacheHelper you don't call any of its methods, they are all automatically
 * called by View, and use the $cacheAction settings set in the controller.
 *
 * @package       Cake.View.Helper
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/cache.html
 */
class BcCacheHelper extends CacheHelper {

/**
 * Write a cached version of the file
 *
 * @param string $content view content to write to a cache file.
 * @param string $timestamp Duration to set for cache file.
 * @param bool $useCallbacks Whether to include statements in cached file which
 *   run callbacks.
 * @return bool success of caching view.
 */
	protected function _writeFile($content, $timestamp, $useCallbacks = false) {
		$now = time();

		if (is_numeric($timestamp)) {
			$cacheTime = $now + $timestamp;
		} else {
			$cacheTime = strtotime($timestamp, $now);
		}
		
		// CUSTOMIZE 2014/08/11 ryuring
		// $this->request->here で、URLを取得する際、URL末尾の 「index」の有無に関わらず
		// 同一ファイルを参照すべきだが、別々のURLを出力してしまう為、
		// 正規化された URLを取得するメソッドに変更
		// >>>
		//$path = $this->request->here();
		// ---
		$path = $this->request->normalizedHere();
		// <<<
		
		if ($path === '/') {
			$path = 'home';
		}
		$prefix = Configure::read('Cache.viewPrefix');
		if ($prefix) {
			$path = $prefix . '_' . $path;
		}
		$cache = strtolower(Inflector::slug($path));

		if (empty($cache)) {
			return;
		}
		$cache = $cache . '.php';
		$file = '<!--cachetime:' . $cacheTime . '--><?php';

		if (empty($this->_View->plugin)) {
			$file .= "
			App::uses('{$this->_View->name}Controller', 'Controller');
			";
		} else {
			$file .= "
			App::uses('{$this->_View->plugin}AppController', '{$this->_View->plugin}.Controller');
			App::uses('{$this->_View->name}Controller', '{$this->_View->plugin}.Controller');
			";
		}

		$file .= '
				$request = unserialize(base64_decode(\'' . base64_encode(serialize($this->request)) . '\'));
				$response->type(\'' . $this->_View->response->type() . '\');
				$controller = new ' . $this->_View->name . 'Controller($request, $response);
				$controller->plugin = $this->plugin = \'' . $this->_View->plugin . '\';
				$controller->helpers = $this->helpers = unserialize(base64_decode(\'' . base64_encode(serialize($this->_View->helpers)) . '\'));
				$controller->layout = $this->layout = \'' . $this->_View->layout . '\';
				$controller->theme = $this->theme = \'' . $this->_View->theme . '\';
				$controller->viewVars = unserialize(base64_decode(\'' . base64_encode(serialize($this->_View->viewVars)) . '\'));
				Router::setRequestInfo($controller->request);
				$this->request = $request;';

		if ($useCallbacks) {
			$file .= '
				$controller->constructClasses();
				$controller->startupProcess();';
		}

		$file .= '
				$this->viewVars = $controller->viewVars;
				$this->loadHelpers();
				extract($this->viewVars, EXTR_SKIP);
		?>';
		$content = preg_replace("/(<\\?xml)/", "<?php echo '$1'; ?>", $content);
		$file .= $content;
		return cache('views' . DS . $cache, $file, $timestamp);
	}

}
