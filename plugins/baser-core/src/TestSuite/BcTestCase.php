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
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * Class BcTestCase
 * @package BaserCore\TestSuite
 */
class BcTestCase extends TestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;

    /**
     * Set Up
     */
    public function setUp(): void
    {
        parent::setUp();
        $application = new Application(CONFIG);
        $application->bootstrap();
        $builder = Router::createRouteBuilder('/');
        $application->routes($builder);
        $plugin = new Plugin();
        $plugin->bootstrap($application);
        $plugin->routes($builder);
    }

    /**
     * Request を取得する
     *
     * @param string $url
     * @return ServerRequest
     */
    public function getRequest($url = '/')
    {
        $request = new ServerRequest(['url' => $url]);
        $params = Router::parseRequest($request);
        $request = $request->withAttribute('params', $params);
        Router::setRequest($request);
        return $request;
    }

    /**
     * サンプル用のユーザーを取得する
     *
     * @param string $group
     */
    protected function getUser($id = 1)
    {
        $userTable = TableRegistry::getTableLocator()->get('BaserCore.Users');
        $user = $userTable->find()
                    ->where(['Users.id' => $id])
                    ->contain(['UserGroups'])
                    ->first();
        return $user;
    }

    /**
     * 管理画面にログインする
     *
     * @param string $group
     */
    protected function loginAdmin($id = 1)
    {
        $sessionKey = Configure::read('BcPrefixAuth.Admin.sessionKey');
        $user = $this->getUser($id);
        $this->session([$sessionKey => $user]);
        // IntegrationTestTrait が提供するsession だけでは、テスト中に取得できないテストがあったため
        // request から取得する session でも書き込むようにした
        $session = $this->getRequest()->getSession();
        $session->write($sessionKey, $user);
    }

}
