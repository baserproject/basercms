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
 * Class BcCacheDispatcher
 *
 * @package Baser.Routing.Filter
 */
class BcCacheDispatcher implements MiddlewareInterface
{

    /**
     * Default priority for all methods in this filter
     * This filter should run before the request gets parsed by router
     *
     * @var int
     */
    // CUSTOMIZE MODIFY 2017/01/07 ryuring
    // >>>
    // public $priority = 9;
    // ---
    public $priority = 5;
    // <<<

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
        if (Configure::read('Cache.check') !== true) {
            return $handler->handle($request);;
        }

        // CUSTOMIZE 2014/08/11 ryuring
        // $this->request->here で、URLを取得する際、URL末尾の 「index」の有無に関わらず
        // 同一ファイルを参照すべきだが、別々のURLを出力してしまう為、
        // 正規化された URLを取得するメソッドに変更
        // >>>
        //$path = $event->getData('request')->here();
        // ---
        $path = $event->getData('request')->normalizedHere();
        // <<<

        if ($path === '/') {
            // CUSTOMIZE 2017/01/07 ryuring
            // CakePHP 2.10.6 へのアップデートの際に変更となっていた事に気づいた
            // 仕様として必要かどうかは未確認
            // >>>
            // $path = 'home';
            // ---
            $path = 'index';
            // <<<
        }
        $prefix = Configure::read('Cache.viewPrefix');
        if ($prefix) {
            $path = $prefix . '_' . $path;
        }
        $path = strtolower(Inflector::slug($path));

        $filename = CACHE . 'views' . DS . $path . '.php';

        if (!file_exists($filename)) {
            $filename = CACHE . 'views' . DS . $path . '_index.php';
        }
        if (file_exists($filename)) {
            $controller = null;
            $view = new View($controller);
            $view->response = $event->getData('response');
            $result = $view->renderCache($filename, microtime(true));
            if ($result !== false) {
                $event->stopPropagation();
                $event->getData('response')->body($result);
                return $event->getData('response');
            }
        }
        return $handler->handle($request);
    }

}
