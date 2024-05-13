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
        if(BcUtil::isAdminSystem() && BcUtil::isInstalled()) $request = $this->setCurrentSite($request);
        return $handler->handle($request);
    }

    /**
     * 現在の管理対象のサイトを設定する
     *
     * - 何もセットされていない場合はメインサイトを設定
     * - セッションがあればそのまま利用
     * - クエリパラメーターがあれば上書き
     *
     * @param ServerRequestInterface $request
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setCurrentSite($request): ServerRequestInterface
    {
        $session = $request->getSession();
        $defaultSiteId = 1;

        $queryCurrentSiteId = $request->getQuery('site_id');
        $sitesTable = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        if ($queryCurrentSiteId) {
            $query = $sitesTable->find()->where(['Sites.id' => $queryCurrentSiteId]);
            if(!$query->count()) {
                $request = $request->withQueryParams(array_merge(
                    $request->getQueryParams(),
                    ['site_id' => $defaultSiteId]
                ));
                $query = $sitesTable->find()->where(['Sites.id' => $defaultSiteId]);
            }
            $currentSite = $query->first();
        } elseif($session->check('BcApp.Admin.currentSite')) {
            $currentSite = $session->read('BcApp.Admin.currentSite');
        } else {
            $currentSite = $sitesTable->find()->where(['Sites.id' => $defaultSiteId])->first();
        }

        $session->write('BcApp.Admin.currentSite', $currentSite);
        return $request->withAttribute('currentSite', $currentSite);
    }

}
