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
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     * @checked
     * @noTodo
     */
    public function setCurrent(ServerRequest $request): ServerRequestInterface
    {
        return $request
            ->withAttribute('currentContent', $request->getParam('Content'))
            ->withAttribute('currentSite', $request->getParam('Site'))
            ->withParam('Content', '')
            ->withParam('Site', '');
    }

}
