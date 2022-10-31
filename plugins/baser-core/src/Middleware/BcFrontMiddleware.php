<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Middleware;

use BaserCore\Utility\BcUtil;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;

/**
 * BcFrontMiddleware
 */
class BcFrontMiddleware implements MiddlewareInterface
{

    /**
     * Process
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @checked
     * @noTodo
     */
    public function process(
        ServerRequestInterface  $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        if(!BcUtil::isAdminSystem()) $request = $this->setCurrent($request);
        return $handler->handle($request);
    }

    /**
     * 現在のページのサイトとコンテンツを ServerRequest に設定する
     *
     * ### コンテンツ管理により管理されているページの場合
     * BcContentsRoute によって、$request の param に設定されているので、
     * そのデータを Attribute にセットしなおす。
     *
     * ###そうでないページの場合
     * URL より、サイトを判定し、そのサイトのトップページを
     * 現在のコンテンツとしてみなし、ServerRequest に設定する。
     *
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setCurrent(ServerRequest $request): ServerRequestInterface
    {
        $site = $request->getParam('Site');
        $content = $request->getParam('Content');
        if(!$site) {
            $sitesTable = TableRegistry::getTableLocator()->get('BaserCore.Sites');
            $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
            $sitePrefix = (string) $request->getParam('sitePrefix');
            $site = $sitesTable->findByName($sitePrefix)->first();
            $url = '/';
            if($sitePrefix) $url .= $sitePrefix . '/';
            $content = $contentsTable->findByUrl($url);
        }
        return $request
            ->withAttribute('currentContent', $content)
            ->withAttribute('currentSite', $site)
            ->withParam('Content', null)
            ->withParam('Site', null);
    }

}
