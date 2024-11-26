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
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcRedirectMainSiteMiddleware
 *
 * サブサイトにコンテンツが存在しない場合、同階層のメインサイトのコンテンツを確認し、
 * 存在していれば、メインサイトへリダイレクトをする。
 *
 * （例）
 * /s/service → /service
 *
 */
class BcRedirectMainSiteMiddleware implements MiddlewareInterface
{

    /**
     * Process
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function process(
        ServerRequestInterface  $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        if ($request->is('admin')
            || !BcUtil::isInstalled()
            || !$request->is('requestview')
        ) {
            return $handler->handle($request);
        }
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->findByUrl($request->getPath());
        if (!$site || !$site->status || $site->id === 1) {
            return $handler->handle($request);
        }
        $mainSite = $site->getMain();
        if (!$mainSite) {
            return $handler->handle($request);
        }
        $mainSiteUrl = '/' . preg_replace('/^\/' . $site->alias . '\//', '', $request->getPath());
        if ($mainSite->alias) {
            $mainSiteUrl = '/' . $mainSite->alias . $mainSiteUrl;
        }
        if ($mainSiteUrl) {
            $response = new Response([
                'status' => 302
            ]);
            return $response->withLocation($request->getAttribute('base') . $mainSiteUrl);
        }
        return $handler->handle($request);
    }

}
