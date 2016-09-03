<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Routing.Filter
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * This filter will check whether the response was previously cached in the file system
 * and served it back to the client if appropriate.
 *
 * @package Baser.Routing.Filter
 */
class BcAgentRedirectFilter extends DispatcherFilter {

	/**
	 * Default priority for all methods in this filter
	 * This filter should run before the request gets parsed by router
	 *
	 * @var int
	 */
	public $priority = 4;

	/**
	 * Checks whether the response was cached and set the body accordingly.
	 *
	 * @param CakeEvent $event containing the request and response object
	 * @return void
	 */
	public function beforeDispatch(CakeEvent $event) {

		$request = $event->data['request'];
		$response = $event->data['response'];

		// エージェントリダイレクト判定
		// キャッシュのチェック前に判定しないとリダイレクトできないのでBcRequestFilterではなくここで判定
		// $_SERVER['HTTP_USER_AGENT']からエージェントを取得
		$agent = BcAgent::findCurrent();
		if (!is_null($agent) && $agent->isEnabled()) {
			if (!$request->is('admin') && $agent->shouldRedirects($request) && $agent->existsRedirectUrl($request)) {
				$response->header('Location', $request->base . '/' . $agent->makeRedirectUrl($request));
				$response->statusCode(302);
				return $response;
			}
		}
		
	}

}
