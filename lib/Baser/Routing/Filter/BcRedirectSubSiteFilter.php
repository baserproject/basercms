<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Routing.Filter
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class BcRedirectSubSiteFilter
 *
 * ユーザーエージェントにより、関連するサブサイトにリダイレクトを行う
 *
 * @package Baser.Routing.Filter
 */
class BcRedirectSubSiteFilter extends DispatcherFilter
{

	/**
	 * 優先順位
	 *
	 * 先にキャッシュを読まれると意味がない為
	 * BcCacheDispatcherより先に呼び出される必要がある
	 *
	 * @var int
	 */
	public $priority = 4;

	/**
	 * Before Dispatch
	 *
	 * @param CakeEvent $event containing the request and response object
	 * @return void
	 */
	public function beforeDispatch(CakeEvent $event)
	{

		$request = $event->data['request'];
		if (Configure::read('BcRequest.isUpdater')) {
			return;
		}
		$response = $event->data['response'];
		if ($request->is('admin')) {
			return;
		}
		$subSite = BcSite::findCurrentSub();
		if (!is_null($subSite) && $subSite->shouldRedirects($request)) {
			$response->header('Location', $request->base . $subSite->makeUrl($request));
			$response->statusCode(302);
			return $response;
		}
	}

}
