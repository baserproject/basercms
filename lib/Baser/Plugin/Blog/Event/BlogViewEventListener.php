<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * class BlogViewEventListener
 */
class BlogViewEventListener extends BcViewEventListener
{

	/**
	 * Events
	 * @var string[]
	 */
	public $events = ['leftOfToolbar'];

	/**
	 * leftOfToolbar
	 * @param CakeEvent $event
	 */
	public function leftOfToolbar(CakeEvent $event)
	{
		if(BcUtil::isAdminSystem()) return;
		$view = $event->subject();
		if (!empty($view->request->params['Content']['type']) && $view->request->params['Content']['type'] === 'BlogContent') {
			echo $view->element('admin/blog_posts/left_of_toolbar');
		}
	}

}
