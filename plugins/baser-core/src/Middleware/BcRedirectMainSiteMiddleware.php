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
use Cake\Core\App;
use Cake\Http\Exception\MissingControllerException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Throwable;

/**
 * Class BcRedirectMainSiteMiddleware
 *
 * サブサイトにコンテンツが存在しない場合、同階層のメインサイトのコンテンツを確認し、
 * 存在していれば、メインサイトへリダイレクトをする。
 *
 * （例）
 * /s/service → /service
 *
 * コンテンツが存在しないことは、後続の処理で発生する 404 の例外で判定するため、
 * RoutingMiddleware より前に配置する
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
        try {
            return $handler->handle($request);
        } catch (NotFoundException|MissingRouteException|MissingControllerException $e) {
            $response = $this->getRedirectResponse($request);
            if ($response) return $response;
            throw $e;
        }
    }

    /**
     * メインサイトへのリダイレクトのレスポンスを取得する
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getRedirectResponse(ServerRequestInterface $request): ?ResponseInterface
    {
        if (!$request instanceof ServerRequest
            || !in_array($request->getMethod(), ['GET', 'HEAD'], true)
            || $request->is('admin')
            || !BcUtil::isInstalled()
        ) {
            return null;
        }
        // サブサイトの URL がコンテンツやコントローラーに該当する場合（記事が存在しないなどの 404）はリダイレクトしない
        if ($this->isRoutable($request)) return null;

        $path = $request->getUri()->getPath();
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->findByUrl($path);
        if (!$site || !$site->status || !$site->main_site_id || !$site->alias) return null;
        $mainSite = $site->getMain();
        if (!$mainSite) return null;
        $mainSiteUrl = '/' . preg_replace('/^\/' . preg_quote($site->alias, '/') . '\//', '', $path);
        if ($mainSiteUrl === $path) return null;
        // 別のホストへのリダイレクトにならないようにする（//example.com など）
        if (preg_match('#^/[/\\\\]#', $mainSiteUrl)) return null;
        if ($mainSite->alias) {
            $mainSiteUrl = '/' . $mainSite->alias . $mainSiteUrl;
        }
        if (!$this->isRoutable($request->withUri($request->getUri()->withPath($mainSiteUrl)))) return null;
        // クエリ文字列はそのまま引き継ぐ（ページ番号など）
        $query = $request->getUri()->getQuery();
        return (new Response(['status' => 302]))
            ->withLocation($request->getAttribute('base') . $mainSiteUrl . ($query !== ''? '?' . $query : ''));
    }

    /**
     * URL がコンテンツ、またはコントローラーに該当するか判定する
     *
     * @param ServerRequest $request
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isRoutable(ServerRequest $request): bool
    {
        $request = $request->withAttribute('params', []);
        $previous = Router::getRequest();
        try {
            Router::setRequest($request);
            $params = Router::parseRequest($request);
        } catch (Throwable) {
            return false;
        } finally {
            if ($previous) Router::setRequest($previous);
        }
        if (!empty($params['Content'])) return true;
        if (empty($params['controller'])) return false;
        $name = (!empty($params['plugin'])? $params['plugin'] . '.' : '') . $params['controller'];
        $type = 'Controller' . (!empty($params['prefix'])? '/' . $params['prefix'] : '');
        return App::className($name, $type, 'Controller') !== null;
    }

}
