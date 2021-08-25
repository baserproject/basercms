<?php
// TODO : コード確認要
use BaserCore\Service\BcFrontServiceInterface;

return;
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
     * Trait
     */
    use \BaserCore\Utility\BcContainerTrait;

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
     * @param \Cake\Event\Event $event containing the request and response object
     * @return void
     */
    public function beforeDispatch(\Cake\Event\Event $event)
    {

        $request = $event->getData('request');
        if (Configure::read('BcRequest.isUpdater')) {
            return;
        }
        $response = $event->getData('response');
        if ($request->is('admin')) {
            return;
        }
        $siteFront = $this->getService(BcFrontServiceInterface::class);
        $subSite = $siteFront->findCurrentSub();
        if (!is_null($subSite) && $subSite->shouldRedirects($request)) {
            $response->header('Location', $request->base . $subSite->makeUrl($request));
            $response->statusCode(302);
            return $response;
        }
    }

}
