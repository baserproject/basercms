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
 * Class BcRedirectMainSiteFilter
 *
 * サブサイトにコンテンツが存在しない場合、同階層のメインサイトのコンテンツを確認し、
 * 存在していれば、メインサイトへリダイレクトをする。
 *
 * （例）
 * /s/service → /service
 *
 * @package Baser.Routing.Filter
 */
class BcRedirectMainSiteFilter implements MiddlewareInterface
{

    /**
     * Trait
     */
    use \BaserCore\Utility\BcContainerTrait;

    /**
     * priority
     *
     * URLの存在確認が完了しているタイミングを前提としている為、
     * Dispacher::parseParams() より後に実行される必要がある
     *
     * @var int
     */
    public $priority = 10;

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
        $response = $event->getData('response');
        if (!empty($request->getParam('Content'))) {
            return $handler->handle($request);
        } else {
            if ($this->_existController($request)) {
                return $handler->handle($request);
            }
        }
        $sites = \Cake\ORM\TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->findByUrl($request->getPath());
        if (!$site || !$site->enabled) {
            return $handler->handle($request);
        }
        $mainSite = $site->getMain();
        if (!$mainSite) {
            return $handler->handle($request);
        }
        $mainSiteUrl = '/' . preg_replace('/^' . $site->alias . '\//', '', $request->url);
        if ($mainSite->alias) {
            $mainSiteUrl = '/' . $mainSite->alias . $mainSiteUrl;
        }
        if ($mainSiteUrl) {
            $request = new CakeRequest($mainSiteUrl);
            $params = Router::parse($request->url);
            $request->addParams($params);
            if ($this->_existController($request)) {
                $response->header('Location', $request->base . $mainSiteUrl);
                $response->statusCode(302);
                return $response;
            }
        }
        return $handler->handle($request);
    }

    /**
     * コントローラーが存在するか確認
     *
     * @param $request
     * @return bool
     */
    protected function _existController($request)
    {
        $pluginName = $pluginPath = $controller = null;
        if (!empty($request->getParam('plugin'))) {
            $pluginName = $controller = Inflector::camelize($request->getParam('plugin'));
            $pluginPath = $pluginName . '.';
        }
        if (!empty($request->getParam('controller'))) {
            $controller = Inflector::camelize($request->getParam('controller'));
        }
        if ($pluginPath . $controller) {
            $class = $controller . 'Controller';
            App::uses('AppController', 'Controller');
            App::uses($pluginName . 'AppController', $pluginPath . 'Controller');
            App::uses($class, $pluginPath . 'Controller');
            return class_exists($class);
        }
        return false;
    }
}
