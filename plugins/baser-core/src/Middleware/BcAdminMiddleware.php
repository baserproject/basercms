<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Middleware;

use BaserCore\Utility\BcUtil;
use Cake\ORM\TableRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcAdminMiddleware
 */
class BcAdminMiddleware implements MiddlewareInterface
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
        if (BcUtil::isAdminSystem()) {
            $request = $this->setCurrentSite($request);
        }
        return $handler->handle($request);
    }

    /**
     * 現在の管理対象のサイトを設定する
     * @param ServerRequestInterface $request
     * @checked
     * @noTodo
     * @unitTest
     */
    private function setCurrentSite($request): ServerRequestInterface
    {
        $session = $request->getSession();
        $currentSiteId = 1;
        $queryCurrentSiteId = $request->getQuery('site_id');
        if (!$session->check('BcApp.Admin.currentSite') || $queryCurrentSiteId) {
            if ($queryCurrentSiteId) {
                $currentSiteId = $queryCurrentSiteId;
            }
            $currentSite = TableRegistry::getTableLocator()->get('BaserCore.Sites')->get($currentSiteId);
            $session->write('BcApp.Admin.currentSite', $currentSite);
        }
        return $request->withAttribute('currentSite', $session->read('BcApp.Admin.currentSite'));
    }
}
