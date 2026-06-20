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
use BaserCore\Utility\BcFolder;
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
use Couchbase\LookupGetSpec;
use ReflectionClass;
use BaserCore\Utility\BcContainer;
use BaserCore\ServiceProvider\BcServiceProvider;

/**
 * Class BcTestCase
 *
 * テストクラスでは setUp() で各サービス／テーブルをプロパティに代入する慣習があり、
 * PHP 8.2 の動的プロパティ非推奨を回避するため、基底クラスに付与する。
 * この属性は子クラスにも継承されるため、全テストクラスに適用される。
 */
#[\AllowDynamicProperties]
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
        parent::setUp();
        // phpunit 実行時に、phpunit の実行パスが入ってしまうため調整
        // BcUtil::baseUrl() / BcUtil::docRoot() BcUtil::siteUrl() に影響あり
        $_SERVER['SCRIPT_NAME'] = DS . 'webroot' . DS . 'index.php';
        $_SERVER['SCRIPT_FILENAME'] = ROOT . DS . 'webroot' . DS . 'index.php';
        $this->Application = new Application(CONFIG);
        $this->Application->bootstrap();
        // テスト用アプリのプラグイン読み込み・bootstrap・ルート接続をまとめて行う
        $this->loadTestAppPlugins();
        // コンテナを build して BcContainer に確定させる（Application.buildContainer イベント経由）
        $this->Application->getContainer();
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
        // 直前のテスト（コントローラ系で BcFrontAppController / BaserCorePlugin が I18n::setLocale した等）による
        // ロケール汚染をリセットし、各テストを既定ロケール（App.defaultLocale）で開始する。
        // I18n のロケールは静的でテスト間に残存するため、bootstrap/pluginBootstrap 完了後の末尾でリセットする。
        \Cake\I18n\I18n::setLocale(Configure::read('App.defaultLocale'));
    }

    /**
     * テスト用アプリのプラグイン読み込み・bootstrap・ルート接続を行う
     *
     * 【各 app へのプラグイン読み込み】
     * CakePHP 5.2 では Application を new するたびにグローバルの Plugin コレクションがリセットされ、
     * テスト用DB（plugins テーブルが空）からはコンテンツプラグインが読み込まれない。
     * tests/bootstrap.php で退避したプラグイン一覧（BcApp.testAppPluginsToLoad）を、次の 2 つの app に読み込ませる。
     *  - $this->appPluginsToLoad: 統合テストがリクエストごとに生成する app（IntegrationTestTrait::createApp()）向け。
     *    これが無いと $this->get()/$this->post() の app にプラグインが無く、ルートが欠落して 404 等になる。
     *  - $this->Application: setUp 自身が bootstrap する app 向け。これが無いと Plugin::isLoaded('BcXxx') が false となり、
     *    ヘルパテスト等のテンプレート解決（pluginSplit）が失敗する。
     *
     * 【スナップショット bootstrap】
     * CakePHP 5.2 では BaseApplication::pluginBootstrap() が PluginCollection の live なイテレータを走査するが、
     * BaserCorePlugin::bootstrap() がイテレーション中に addPlugin()/remove()（テーマ追加・DebugKit 削除等）で
     * コレクションを変更するため、直後に並ぶプラグイン（例: BcBlog）の bootstrap() がスキップされ
     * setting.php が読み込まれない。そこで live イテレーションを使わず、未 bootstrap が無くなるまで
     * スナップショット単位で bootstrap して取りこぼしを防ぐ。
     *
     * @return void
     */
    protected function loadTestAppPlugins(): void
    {
        // --- 各 app へのプラグイン読み込み ---
        $plugins = (array)Configure::read('BcApp.testAppPluginsToLoad', []);
        // 統合テストの per-request app（createApp()）向け
        $this->appPluginsToLoad = $plugins;
        // setUp 自身の app 向け
        foreach ($plugins as $plugin) {
            if (!$this->Application->getPlugins()->has($plugin)) {
                $this->Application->addPlugin($plugin);
            }
        }
        // --- 未 bootstrap のプラグインが無くなるまでスナップショット単位で bootstrap する ---
        $booted = [];
        do {
            $pending = [];
            foreach ($this->Application->getPlugins() as $plugin) {
                if ($plugin->isEnabled('bootstrap') && !in_array($plugin->getName(), $booted, true)) {
                    $pending[] = $plugin;
                }
            }
            foreach ($pending as $plugin) {
                $booted[] = $plugin->getName();
                $plugin->bootstrap($this->Application);
            }
        } while ($pending);
        // --- プラグインルートの接続・CakephpFixtureFactories の追加・BaserCore の保持 ---
        $builder = Router::createRouteBuilder('/');
        $this->Application->pluginRoutes($builder);
        if (!$this->Application->getPlugins()->has('CakephpFixtureFactories')) {
            $this->Application->addPlugin('CakephpFixtureFactories');
        }
        $this->BaserCore = $this->Application->getPlugins()->get('BaserCore');
    }

    /**
     * クラスメソッド名を取得する
     * @checked
     * @noTodo
     * @unitTest テストができないのでスキップ
     */
    public function classMethod()
    {
        $test = $this->provides()[0];
        $contents = ob_get_contents();
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        echo "\n" . $test->getTarget() . ' ';
        if($contents) {
            echo $contents;
        }
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
        /* @var ServerRequest $request */
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
        // CakePHP 5 ではイベントリスナーからの戻り値が非推奨のため、
        // コールバックの戻り値は $event->setResult() に変換する。
        $listener->method($methodName)
            ->willReturnCallback(function ($event) use ($callback) {
                $result = $callback($event);
                if ($result !== null) {
                    $event->setResult($result);
                }
            });
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
        return $property->getValue($class);
    }

    /**
     * イベントを設定する
     *
     * @param $events
     * @checked
     * @noTodo
     * @unitTest
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
     * @noTodo
     * @unitTest
     */
    public function resetEvent()
    {
        $EventManager = EventManager::instance();
        $reflectionClass = new ReflectionClass(get_class($EventManager));
        $property = $reflectionClass->getProperty('_listeners');
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
        $folder = new BcFolder(LOGS);
        $folder->chmod( 0777);
        $folder = new BcFolder(TMP);
        $folder->chmod(0777);
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
