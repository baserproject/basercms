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

App::uses('DispatcherFilter', 'Routing');

/**
 * Class BcCacheDispatcher
 *
 * @package Baser.Routing.Filter
 */
class BcCacheDispatcher extends DispatcherFilter
{

	/**
	 * Default priority for all methods in this filter
	 * This filter should run before the request gets parsed by router
	 *
	 * @var int
	 */
	// CUSTOMIZE MODIFY 2017/01/07 ryuring
	// >>>
	// public $priority = 9;
	// ---
	public $priority = 5;
	// <<<

	/**
	 * Checks whether the response was cached and set the body accordingly.
	 *
	 * @param CakeEvent $event containing the request and response object
	 * @return CakeResponse with cached content if found, null otherwise
	 */
	public function beforeDispatch(CakeEvent $event)
	{
		if (Configure::read('Cache.check') !== true) {
			return null;
		}

		// CUSTOMIZE 2014/08/11 ryuring
		// $this->request->here で、URLを取得する際、URL末尾の 「index」の有無に関わらず
		// 同一ファイルを参照すべきだが、別々のURLを出力してしまう為、
		// 正規化された URLを取得するメソッドに変更
		// >>>
		//$path = $event->data['request']->here();
		// ---
		$path = $event->data['request']->normalizedHere();
		// <<<

		if ($path === '/') {
			// CUSTOMIZE 2017/01/07 ryuring
			// CakePHP 2.10.6 へのアップデートの際に変更となっていた事に気づいた
			// 仕様として必要かどうかは未確認
			// >>>
			// $path = 'home';
			// ---
			$path = 'index';
			// <<<
		}
		$prefix = Configure::read('Cache.viewPrefix');
		if ($prefix) {
			$path = $prefix . '_' . $path;
		}
		$path = strtolower(Inflector::slug($path));

		$filename = CACHE . 'views' . DS . $path . '.php';

		if (!file_exists($filename)) {
			$filename = CACHE . 'views' . DS . $path . '_index.php';
		}
		if (file_exists($filename)) {
			$controller = null;
			$view = new View($controller);
			$view->response = $event->data['response'];
			$result = $view->renderCache($filename, microtime(true));
			if ($result !== false) {
				$event->stopPropagation();
				$event->data['response']->body($result);
				return $event->data['response'];
			}
		}
	}

}
