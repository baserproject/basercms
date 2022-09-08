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
 * Class BcAssetDispatcher
 *
 * /app/View/webroot/ や、/baser/View/webroot/ 内のアセットファイルを
 * 読み込めるようにする為のフィルター
 *
 * （例）/css/style.css では、次のファイルを参照する事ができる
 *        /app/View/webroot/css/style.css
 *        /lib/Baser/View/webroot/css/style.css
 *
 * @package Baser.Routing.Filter
 */
class BcAssetDispatcher implements MiddlewareInterface
{

    /**
     * Default priority for all methods in this filter
     * This filter should run before the request gets parsed by router
     * @var int
     */
    // CUSTOMIZE MODIFY 2016/07/17 ryuring
    // >>>
    //public $priority = 9;
    // ---
    public $priority = 4;
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
        return $handler->handle($request);
    }

// CUSTOMIZE MODIFY 2016/07/17 ryuring
// 継承元を呼び出す前提でオーバーライド
// >>>
    /**
     * Builds asset file path based off url
     *
     * @param string $url URL
     * @return string|null Absolute path for asset file
     * TODO ucmitz 一旦、スキップ
     */
//    protected function _getAssetFile($url)
//    {
//        $path = parent::_getAssetFile($url);
//        if (!empty($path)) {
//            return $path;
//        }
//        $parts = explode('/', $url);
//        $fileFragment = implode(DS, $parts);
//        $path = BASER_WEBROOT;
//        if (file_exists($path . $fileFragment)) {
//            return $path . $fileFragment;
//        }
//        return null;
//    }
// <<<
}
