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
use Cake\Core\Configure;
use Cake\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcRedirectSubSiteFilter
 *
 * ユーザーエージェントにより、関連するサブサイトにリダイレクトを行う
 *
 */
class BcRedirectSubSiteFilter implements MiddlewareInterface
{

    /**
     * Process
     *
     * ブラウザのユーザーエージェント、もしくは言語設定により、適切なサブサイトを決定し、そのサイトにリダレクトする。
     *
     * - リダイレクト先のサイトが非公開の場合はリダイレクトしない。
     * - リダイレクト先のサイトのオートリダイレクト設定が必要。
     * - クエリーパラメーターに、{$site->name}_auto_redirect=off と設定されている場合はリダイレクトしない。
     * - アップデーターや管理画面へのアクセスの場合には無視する。
     *
     * 例えば、サブサイトに英語言語設定とオートリダイレクト設定がされており、エイリアスが en と設定されている場合
     * /about → /en/about にリダイレクトします。
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @noTodo
     * @checked
     * @unitTest
     */
    public function process(
        ServerRequestInterface  $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        if (Configure::read('BcRequest.isUpdater')) {
            return $handler->handle($request);
        }
        if ($request->is('admin') || !BcUtil::isInstalled()) {
            return $handler->handle($request);
        }
        $sites = \Cake\ORM\TableRegistry::getTableLocator()->get('BaserCore.Sites');
        /* @var \BaserCore\Model\Entity\Site $subSite */
        $subSite = $sites->getSubByUrl($request->getPath());
        if (!is_null($subSite) && $subSite->shouldRedirects($request)) {
            $response = new Response([
                'status' => 302
            ]);
            return $response->withLocation($request->getAttribute('base') . $subSite->makeUrl($request));
        }
        return $handler->handle($request);
    }

}
