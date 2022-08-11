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

namespace BaserCore\TestSuite;

use App\Application;
use Authentication\Authenticator\Result;
use BaserCore\Middleware\BcAdminMiddleware;
use BaserCore\Middleware\BcRequestFilterMiddleware;
use BaserCore\Plugin;
use BaserCore\Utility\BcApiUtil;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Filesystem\Folder;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Http\ServerRequestFactory;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\Fixture\FixtureInjector;
use Cake\TestSuite\Fixture\FixtureManager;
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
     * イベントレイヤー
     * entryEventToMock() の引数として利用
     * @var string
     */
    const EVENT_LAYER_CONTROLLER = 'Controller';
    const EVENT_LAYER_VIEW = 'View';
    const EVENT_LAYER_MODEL = 'Model';
    const EVENT_LAYER_HELPER = 'Helper';

    /**
     * detectors
     *
     * ServerRequest::_detectors を初期化する際、
     * 一番初期の状況を保管しておくために利用
     * @var array
     */
    public static $_detectors;

    /**
     * FixtureManager
     * 古いフィクスチャーの後方互換用
     * @var FixtureManager
     * @deprecated 5.1.0
     * @see setUpFixtureManager
     */
    public $FixtureManager;

    /**
     * FixtureInjector
     * 古いフィクスチャーの後方互換用
     * @var FixtureInjector
     * @deprecated 5.1.0
     * @see setUpFixtureManager
     */
    public $FixtureInjector;

    /**
     * setup FixtureManager
     *
     * CakePHP4系より、FixtureManagerが非推奨となったが、$this->autoFixtures = false を利用した動的フィクスチャーを
     * 利用するために FixtureManager が必要となる。phpunit.xml.dist からは、FixtureManager の定義を除外し、
     * 基本的に利用しない方針だが、動的フィクスチャーが必要なテストの場合にだけ利用する。
     * 動的フィクスチャーを FixtureFactory に移管後、廃止とする
     * @deprecated 5.1.0
     */
    public function setUpFixtureManager()
    {
        $this->FixtureManager = new FixtureManager();
        $this->FixtureInjector = new FixtureInjector($this->FixtureManager);
        $this->FixtureInjector->startTest($this);
    }

    /**
     * tear down FixtureManager
     * @deprecated 5.1.0
     * @see setUpFixtureManager
     * @checked
     * @unitTest
     * @noTodo
     */
    public function tearDownFixtureManager()
    {
        $this->FixtureInjector->endTest($this, 0);
        $fixtures = $this->FixtureManager->loaded();
        foreach($fixtures as $fixture) {
            $fixture->truncate(ConnectionManager::get($fixture->connection()));
        }
        self::$fixtureManager = null;
    }

    /**
     * Set Up
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setUp(): void
    {
        if(!$this->autoFixtures) {
            $this->setUpFixtureManager();
        }
        parent::setUp();
        $this->Application = new Application(CONFIG);
        $this->Application->bootstrap();
        $this->Application->getContainer();
        $builder = Router::createRouteBuilder('/');
        $this->Application->pluginBootstrap();
        $this->Application->pluginRoutes($builder);
        $this->Application->addPlugin('CakephpFixtureFactories');
        $this->BaserCore = $this->Application->getPlugins()->get('BaserCore');
        $container = BcContainer::get();
        $container->addServiceProvider(new BcServiceProvider());
        EventManager::instance(new EventManager());
    }

    /**
     * Tear Down
     * @checked
     * @noTodo
     * @unitTest
     */
    public function tearDown(): void
    {
        if(!$this->autoFixtures) {
            $this->tearDownFixtureManager();
        }
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
    public function getRequest($url = '/', $data = [], $method = 'GET', $config = [])
    {
        $config = array_merge([
            'ajax' => false
        ], $config);
        $isAjax = (!empty($config['ajax']))? true : false;
        unset($config['ajax']);
        if(preg_match('/^http/', $url)) {
            $parseUrl = parse_url($url);
            Configure::write('BcEnv.host', $parseUrl['host']);
            $defaultConfig = [
                'uri' => ServerRequestFactory::createUri([
                    'HTTP_HOST' => $parseUrl['host'],
                    'REQUEST_URI' => $url,
                    'REQUEST_METHOD' => $method,
                    'HTTPS' => (preg_match('/^https/', $url))? 'on' : ''
            ])];
        } else {
            $defaultConfig = [
                'url' => $url,
                'environment' => [
                    'REQUEST_METHOD' => $method
            ]];
        }
        $defaultConfig = array_merge($defaultConfig, $config);
        $request = new ServerRequest($defaultConfig);

        // ServerRequest::_detectors を初期化
        // static プロパティで値が残ってしまうため
        $ref = new ReflectionClass($request);
        $detectors = $ref->getProperty('_detectors');
        $detectors->setAccessible(true);
        if(!self::$_detectors) {
            self::$_detectors = $detectors->getValue();
        }
        $detectors->setValue(self::$_detectors);
        $request->getSession()->start();
        try {
            Router::setRequest($request);
            $params = Router::parseRequest($request);
        } catch (\Exception $e) {
            return $request;
        }

        $request = $request->withAttribute('params', $params);
        if($request->getParam('prefix') === 'Admin') {
            $request = $this->execPrivateMethod(new BcAdminMiddleware(), 'setCurrentSite', [$request]);
        }
        if ($data) {
            $request = $request->withParsedBody($data);
        }
        $authentication = $this->BaserCore->getAuthenticationService($request);
        $request = $request->withAttribute('authentication', $authentication);
        $request = $request->withEnv('HTTPS', (preg_match('/^https/', $url))? 'on' : '');
        if($isAjax) {
            $request = $request->withEnv('HTTP_X_REQUESTED_WITH', 'XMLHttpRequest');
        }
        $bcRequestFilter = new BcRequestFilterMiddleware();
        $request = $bcRequestFilter->addDetectors($request);
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
     * @checked
     * @unitTest
     * @noTodo
     */
    protected function apiLoginAdmin($id = 1)
    {
        $user = $this->getUser($id);
        if($user) {
            return BcApiUtil::createAccessToken($id);
        } else {
            return [];
        }
    }

    /**
     * モックにコントローラーのイベントを登録する
     * @param $eventName
     * @param $callback
     * @return EventListenerInterface|\PHPUnit\Framework\MockObject\MockObject
     * @checked
     * @unitTest
     * @noTodo
     */
    protected function entryEventToMock($layer, $eventName, $callback)
    {
        $aryEventName = explode('.', $eventName);
        $methodName = Inflector::variable(implode('_', $aryEventName));
        // モック作成
        $listener = $this->getMockBuilder('\BaserCore\Event\Bc' . $layer . 'EventListener')
            ->onlyMethods(['implementedEvents'])
            ->addMethods([$methodName])
            ->getMock();
        // イベント定義
        $listener->method('implementedEvents')
            ->willReturn([$layer . '.' . $eventName => ['callable' => $methodName]]);
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


	/**
	 * イベントを設定する
	 *
	 * @param $events
     * @checked
     * @unitTest
     * @noTodo
	 */
	public function attachEvent($events)
	{
		$EventManager = EventManager::instance();
		$event = new BcEventListenerMock($events);
		$EventManager->on($event);
		return $event;
	}

	/**
	 * イベントをリセットする
     * @checked
     * @unitTest
     * @noTodo
	 */
	public function resetEvent()
	{
		$EventManager = EventManager::instance();
		$reflectionClass = new ReflectionClass(get_class($EventManager));
		$property = $reflectionClass->getProperty('_listeners');
		$property->setAccessible(true);
		$property->setValue($EventManager, []);
	}

    /**
     * tear down after class
     * テスト時に生成されたログや一時ファイルに書き込み権限を与える
     * ブラウザでアクセスした際にエラーとなるため
     * @checked
     * @unitTest
     * @noTodo
     */
	public static function tearDownAfterClass(): void
    {
        $folder = new Folder();
        $folder->chmod(LOGS, 0777);
        $folder->chmod(TMP, 0777);
    }

}
