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
use Authentication\AuthenticationService;
use Authentication\Authenticator\Result;
use BaserCore\Event\BcControllerEventListener;
use BaserCore\Plugin;
use BaserCore\Service\Api\UserApiService;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Http\BaseApplication;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\ServerRequestFactory;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Utility\Inflector;
use ReflectionClass;
use BaserCore\Utility\BcContainer;
use BaserCore\ServiceProvider\BcServiceProvider;

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
     * @var Application
     */
    public $Application;

    /**
     * @var Plugin
     */
    public $BaserCore;

    /**
     * Set Up
     * @checked
     * @noTodo
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Application = new Application(CONFIG);
        $this->Application->bootstrap();
        $this->Application->getContainer();
        $builder = Router::createRouteBuilder('/');
        $this->Application->pluginBootstrap();
        $this->Application->pluginRoutes($builder);
        $this->BaserCore = $this->Application->getPlugins()->get('BaserCore');
        $container = BcContainer::get();
        $container->addServiceProvider(new BcServiceProvider());
    }

    /**
     * Tear Down
     * @checked
     * @noTodo
     */
    public function tearDown(): void
    {
        BcContainer::clear();
        parent::tearDown();
    }

    /**
     * Request を取得する
     *
     * @param string $url
     * @return ServerRequest
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getRequest($url = '/', $data = [], $method = 'GET')
    {
        if(preg_match('/^http/', $url)) {
            $parseUrl = parse_url($url);
            Configure::write('BcEnv.host', $parseUrl['host']);
            $request = new ServerRequest([
                'uri' => ServerRequestFactory::createUri([
                    'HTTP_HOST' => $parseUrl['host'],
                    'REQUEST_URI' => $url,
                    'REQUEST_METHOD' => $method
            ])]);
        } else {
            $request = new ServerRequest([
                'url' => $url,
                'environment' => [
                    'REQUEST_METHOD' => $method
                ]]
            );
        }

        try {
            Router::setRequest($request);
            $params = Router::parseRequest($request);
        } catch (\Exception $e) {
            return $request;
        }

        $request = $request->withAttribute('params', $params);
        if ($data) {
            $request = $request->withParsedBody($data);
        }
        $authentication = $this->BaserCore->getAuthenticationService($request);
        $request = $request->withAttribute('authentication', $authentication);
        Router::setRequest($request);
        return $request;
    }

    /**
     * サンプル用のユーザーを取得する
     *
     * @param string $group
     * @checked
     * @unitTest
     * @noTodo
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
     * @return ServerRequest
     * @checked
     * @unitTest
     * @noTodo
     */
    protected function loginAdmin(ServerRequest $request, $id = 1)
    {
        $sessionKey = Configure::read('BcPrefixAuth.Admin.sessionKey');
        $user = $this->getUser($id);
        $this->session([$sessionKey => $user]);
        $authentication = $request->getAttribute('authentication');
        if(!$authentication) {
            $authentication = $this->BaserCore->getAuthenticationService($request);
            $request = $request->withAttribute('authentication', $authentication);
        }
        $reflectionClass = new ReflectionClass($authentication);
        $result = $reflectionClass->getProperty('_result');
        $result->setAccessible(true);
        $result->setValue($authentication, new Result($user, Result::SUCCESS));
        $request = $authentication->persistIdentity($request, new Response, $user)['request'];
        return $request;
    }

    /**
     * Api Login
     * @param int $id
     * @return array
     */
    protected function apiLoginAdmin($id = 1)
    {
        $userApi = new UserApiService();
        $user = $this->getUser($id);
        if($user) {
            return $userApi->getAccessToken(new Result($this->getUser($id), Result::SUCCESS));
        } else {
            return [];
        }
    }

    /**
     * モックにコントローラーのイベントを登録する
     * @param $eventName
     * @param $callback
     * @return BcControllerEventListener|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function entryControllerEventToMock($eventName, $callback)
    {
        $aryEventName = explode('.', $eventName);
        $methodName = Inflector::variable(implode('_', $aryEventName));
        // モック作成
        $listener = $this->getMockBuilder(BcControllerEventListener::class)
            ->onlyMethods(['implementedEvents'])
            ->addMethods([$methodName])
            ->getMock();
        // イベント定義
        $listener->method('implementedEvents')
            ->willReturn([$eventName => ['callable' => $methodName]]);
        // コールバック定義
        $listener->method($methodName)
            ->willReturn($this->returnCallback($callback));
        EventManager::instance()->on($listener);
        return $listener;
    }

    /**
     * private・protectedメソッドを実行する
     * @param object $class 対象クラス
     * @param string $method 対象メソッド
     * @param array $args 対象メソッドに必要な引数
     * @return mixed $value
     * @checked
     * @unitTest
     * @noTodo
     */
    protected function execPrivateMethod(object $class, string $method, array $args = [])
    {
        $ref = new ReflectionClass($class);
        $method = $ref->getMethod($method);
        $method->setAccessible(true);
        $value = $method->invokeArgs($class, $args);
        return $value;
    }

}
