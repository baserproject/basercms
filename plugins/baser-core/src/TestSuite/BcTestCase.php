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

namespace BaserCore\TestSuite;
use App\Application;
use BaserCore\Plugin;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;

/**
 * Class BcTestCase
 * @package BaserCore\TestSuite
 */
class BcTestCase extends TestCase {

    /**
     * Set Up
     */
    public function setUp(): void
    {
        parent::setUp();
        $plugin = new Plugin();
        $plugin->bootstrap(new Application(''));
    }

    /**
     * Request を取得する
     *
     * @param string $url
     * @return ServerRequest
     */
    public function getRequest($url = '/') {
        $request = new ServerRequest(['url' => $url]);
        Router::setRequest($request);
        return $request;
    }

    /**
     * Setup Before Class
     */
    public static function setupBeforeClass():void
    {
        include \Cake\Core\Plugin::path('baser-core') . DS . 'config' . DS . 'paths.php';
    }

}
