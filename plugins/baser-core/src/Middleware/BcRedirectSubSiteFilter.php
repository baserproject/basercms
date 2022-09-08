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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class BcRedirectSubSiteFilter
 *
 * ユーザーエージェントにより、関連するサブサイトにリダイレクトを行う
 *
 * @package Baser.Routing.Filter
 */
class BcRedirectSubSiteFilter implements MiddlewareInterface
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
     * Process
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface  $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        $request = $event->getData('request');
        if (Configure::read('BcRequest.isUpdater')) {
            return $handler->handle($request);
        }
        $response = $event->getData('response');
        if ($request->is('admin')) {
            return $handler->handle($request);
        }

        $sites = \Cake\ORM\TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $subSite = $sites->getSubByUrl($request->getPath());
        if (!is_null($subSite) && $subSite->shouldRedirects($request)) {
            $response->header('Location', $request->base . $subSite->makeUrl($request));
            $response->statusCode(302);
            return $response;
        }
        return $handler->handle($request);
    }

}
