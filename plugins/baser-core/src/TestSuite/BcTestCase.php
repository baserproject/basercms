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
use BaserCore\Plugin;
use BaserCore\Service\BcDatabaseService;
use BaserCore\Utility\BcApiUtil;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcBlog\ServiceProvider\BcBlogServiceProvider;
use BcContentLink\ServiceProvider\BcContentLinkServiceProvider;
use BcCustomContent\ServiceProvider\BcCustomContentServiceProvider;
use BcInstaller\ServiceProvider\BcInstallerServiceProvider;
use BcMail\ServiceProvider\BcMailServiceProvider;
use BcSearchIndex\ServiceProvider\BcSearchIndexServiceProvider;
use BcThemeConfig\ServiceProvider\BcThemeConfigServiceProvider;
use BcThemeFile\ServiceProvider\BcThemeFileServiceProvider;
use BcUploader\ServiceProvider\BcUploaderServiceProvider;
use BcWidgetArea\ServiceProvider\BcWidgetAreaServiceProvider;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Filesystem\Folder;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
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
use CakephpTestSuiteLight\Fixture\TruncateDirtyTables;
use ReflectionClass;
use BaserCore\Utility\BcContainer;
use BaserCore\ServiceProvider\BcServiceProvider;

/**
 * Class BcTestCase
 */
class BcTestCase extends TestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;
    use BcContainerTrait;
    use TruncateDirtyTables;

    /**
     * @var Application
     */
    public $Application;

    /**
     * @var Plugin
     */
    public $BaserCore;

    /**
     * FixtureStrategy にて、TruncateStrategy を利用するかどうか
     * @var bool
     */
    private $fixtureTruncate = false;

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
     * FixtureStrategy にて、TruncateStrategy を利用するかどうかを設定
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setFixtureTruncate(): void
    {
        $this->fixtureTruncate = true;
    }

    /**
     * テーブルを空にする
     * @param string $tableName
     * @noTodo
     * @checked
     * @unitTest BcDatabaseService::truncate のラッパーメソッドのためスキップ
     */
    public static function truncateTable($tableName): void
    {
        // 静的メソッド setUpBeforeClass でも利用しているため、このメソッドも静的メソッドにしている
        // BcContainerTrait 経由で取得しようとしたが、タイミングの問題か見つからないとエラーが出るため直接初期化
        $dbService = new BcDatabaseService();
        $dbService->truncate($tableName);
    }

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
        // ユニットテストの全体テストでメソッド名を表示する際に利用
        if(filter_var(env('SHOW_TEST_METHOD', false), FILTER_VALIDATE_BOOLEAN)) {
            $this->classMethod();
        }
        if (!$this->autoFixtures) {
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
        $container->addServiceProvider(new BcSearchIndexServiceProvider());
        $container->addServiceProvider(new BcContentLinkServiceProvider());
        $container->addServiceProvider(new BcBlogServiceProvider());
        $container->addServiceProvider(new BcInstallerServiceProvider());
        $container->addServiceProvider(new BcMailServiceProvider());
        $container->addServiceProvider(new BcWidgetAreaServiceProvider());
        $container->addServiceProvider(new BcThemeFileServiceProvider());
        $container->addServiceProvider(new BcUploaderServiceProvider());
        $container->addServiceProvider(new BcCustomContentServiceProvider());
        $container->addServiceProvider(new BcThemeConfigServiceProvider());
        EventManager::instance(new EventManager());
    }

    /**
     * クラスメソッド名を取得する
     */
    public function classMethod()
    {
        $test = $this->providedTests[0];
        echo "\n" . $test->getTarget() . ' ';
        ob_end_flush();
        ob_start();
    }

    /**
     * Tear Down
     * @checked
     * @noTodo
     * @unitTest
     */
    public function tearDown(): void
    {
        if (!$this->autoFixtures) {
            $this->tearDownFixtureManager();
        }
        BcContainer::clear();
        $_FILES = [];
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
        $request = BcUtil::createRequest($url, $data, $method, $config);
        $request->getSession()->start();
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
        if (!$authentication) {
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
        if ($user) {
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
     * private・protectedプロパティの値を取得する
     *
     * @param object $class
     * @param string $property
     * @return mixed
     * @throws \ReflectionException
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function getPrivateProperty(object $class, string $property)
    {
        $ref = new ReflectionClass($class);
        $property = $ref->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($class);
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

    /**
     * アップロードするファイルをリクエストに設定する
     * IntegrationTestTrait を使ったテストで利用する
     * @param string $name
     * @param string $path
     * @param string $fileName
     * @param int $error
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setUploadFileToRequest($name, $path, $fileName = '', $error = UPLOAD_ERR_OK)
    {
        if (!file_exists($path)) return false;
        if (!$fileName) $fileName = basename($path);
        $size = filesize($path);
        $type = BcUtil::getContentType($fileName);
        $files = [
            $name => [
                'error' => $error,
                'name' => $fileName,
                'size' => $size,
                'tmp_name' => $path,
                'type' => $type
            ]
        ];
        $this->configRequest(['files' => $files]);
        $_FILES = $files;
        return true;
    }

    /**
     * テーブルを削除する
     * @param string $tableName
     * @checked
     * @noTodo
     * @unitTest
     */
    public function dropTable($tableName)
    {
        $connection = ConnectionManager::get('test');
        $schema = $connection->getDriver()->newTableSchema($tableName);
        $sql = $schema->dropSql($connection);
        $connection->execute($sql[0])->closeCursor();
    }

}
