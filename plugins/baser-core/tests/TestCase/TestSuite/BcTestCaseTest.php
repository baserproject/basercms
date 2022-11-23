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

namespace BaserCore\Test\TestCase\TestSuite;

use BaserCore\Database\Schema\BcSchema;
use BaserCore\Utility\BcContainer;
use BaserCore\View\Helper\BcFormHelper;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Session;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\AnalyseController;
use Cake\View\View;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Filesystem\File;

/**
 * BaserCore\TestSuite\BcTestCase
 *
 */
class BcTestCaseTest extends BcTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.LoginStores',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
    ];

    /**
     * Set Up
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test setup
     */
    public function testSetup()
    {
        $this->assertEquals('App\Application', get_class($this->Application));
        $plugins = $this->Application->getPlugins();
        $this->assertTrue($plugins->has('BaserCore'));
        $this->assertEquals('BaserCore\Plugin', get_class($this->BaserCore));
    }

    /**
     * test tearDown
     */
    public function testTearDown()
    {
        $this->tearDown();
        $this->assertNull(BcContainer::$container);
    }

    /**
     * Request を取得するテスト
     *
     * @return void
     */
    public function testGetRequest(): void
    {
        // デフォルトURL $url = '/'
        $urlList = ['' => '/*', '/about' => '/*', '/baser/admin/baser-core/users/login' => '/baser/admin/baser-core/{controller}/{action}/*'];
        foreach($urlList as $url => $route) {
            $request = $this->getRequest($url);
            $this->assertEquals($route, $request->getParam('_matchedRoute'));
        }
        // テストAttributeとsetRequest
        $request = $this->getRequest();
        $this->assertObjectHasAttribute('params', $request);
        $this->assertSame($request, Router::getRequest());
        // configを設定する場合
        $session = new Session();
        $session->write('test', 'testGetRequest');
        $request = $this->getRequest('/', [], 'GET', ['session' => $session]);
        $this->assertEquals('testGetRequest', $request->getSession()->read('test'));

    }

    /**
     * サンプル用のユーザーを取得するのテスト
     *
     * @return void
     */
    public function testGetUser(): void
    {
        // デフォルト引数が1かテスト
        $this->assertEquals($this->getUser()->id, "1");
        // サンプル用のデータを取得できてるかテスト
        $this->assertEquals($this->getUser(1)->email, "testuser1@example.com");
        $this->assertEquals($this->getUser(2)->email, "testuser2@example.com");
    }

    /**
     * 管理画面にログインするのテスト
     *
     * @return void
     */
    public function testLoginAdmin(): void
    {
        // デフォルト引数が1かテスト
        $this->assertEquals($this->loginAdmin($this->getRequest())->getAttribute('authentication')->getIdentity()->getOriginalData()->id, "1");
        // session書かれているかテスト
        $this->assertSession($this->loginAdmin($this->getRequest('/baser/admin'))->getAttribute('authentication')->getIdentity()->getOriginalData(), Configure::read('BcPrefixAuth.Admin.sessionKey'));
        $this->assertSession($this->loginAdmin($this->getRequest('/baser/admin'), 2)->getAttribute('authentication')->getIdentity()->getOriginalData(), Configure::read('BcPrefixAuth.Admin.sessionKey'));
    }

    /**
     * test apiLoginAdmin
     */
    public function testApiLoginAdmin(): void
    {
        $rs = $this->apiLoginAdmin(1);
        $this->assertNotEmpty($rs);
        $this->assertTrue(isset($rs["access_token"]));
        $this->assertTrue(isset($rs["refresh_token"]));
        $this->assertEmpty($this->apiLoginAdmin(100));
    }

    /**
     * test プライベートメソッド実行
     *
     * @return void
     */
    public function testExecPrivateMethod(): void
    {
        $sampleClass = new AnalyseController($this->getRequest());
        $samplePrivateMethod = 'pathToClass';
        $result = $this->execPrivateMethod($sampleClass, $samplePrivateMethod, [ROOT . DS . "plugins"]);
        $this->assertEquals("", $result);
    }

    /**
     * test attachEvent
     */
    public function testAttachEventAndResetEvent()
    {
        $this->attachEvent(['testEvent' => null]);
        $eventManager = EventManager::instance();
        $this->assertNotNull($eventManager->listeners('testEvent'));
        $this->resetEvent();
        $this->assertEmpty($eventManager->listeners('testEvent'));
    }

    /**
     * test tearDownAfterClass
     */
    public function testTearDownAfterClass()
    {
        touch(TMP . 'test');
        rename(LOGS . 'cli-debug.log', LOGS . 'cli-debug.bak.log');
        Log::write('debug', 'test');
        self::tearDownAfterClass();
        $this->assertEquals('0777', substr(sprintf('%o', fileperms(LOGS . 'cli-debug.log')), -4));
        $this->assertEquals('0777', substr(sprintf('%o', fileperms(TMP . 'test')), -4));
        unlink(LOGS . 'cli-debug.log');
        rename(LOGS . 'cli-debug.bak.log', LOGS . 'cli-debug.log');
        unlink(TMP . 'test');
    }

    /**
     * test entryEventToMock
     * @return void
     */
    public function testEntryEventToMock(){

        $form = new BcFormHelper(new View());
        $rs = self::entryEventToMock(self::EVENT_LAYER_HELPER, 'Form.afterEnd', function(Event $event){
            $event->setData('out', 'test');
        });

        $this->assertEquals('test', $form->end());
        $this->assertTrue(isset($rs->layer));
        $this->assertTrue(isset($rs->plugin));
    }
    /**
     * test tearDownFixtureManager and setUpFixtureManager
     * @return void
     */
    public function testSetUpFixtureManagerAndTearDownFixtureManager(){
        $contents = $this->getTableLocator()->get('BaserCore.Contents');
        $this->assertTrue((bool) $contents->find()->count());

        self::setUpFixtureManager();
        self::tearDownFixtureManager();

        $this->assertFalse((bool) $contents->find()->count());
        $this->assertTrue(isset($this->FixtureManager));
        $this->assertTrue(isset($this->FixtureInjector));
        $this->assertTrue(isset($this->fixtures));
        $this->assertEmpty(self::$fixtureManager);
    }

    /**
     * test setFixtureTruncate getFixtureStrategy
     * @return void
     */
    public function testSetFixtureTruncateGetFixtureStrategy()
    {
        $bcTestCase = new BcTestCase();
        $rs = $bcTestCase->getFixtureStrategy();
        $this->assertNotNull($rs);
        $this->assertEquals('Cake\TestSuite\Fixture\TransactionStrategy', get_class($rs));

        $bcTestCase->setFixtureTruncate();
        $rs = $bcTestCase->getFixtureStrategy();
        $this->assertNotNull($rs);
        $this->assertEquals('Cake\TestSuite\Fixture\TruncateStrategy', get_class($rs));
    }

    /**
     * test setUploadFileToRequest
     */
    public function testSetUploadFileToRequest()
    {
        $bcTestCase = new BcTestCase();
        $filename = 'testUpload.txt';
        $filePath = TMP . $filename;
        touch($filePath);
        $bcTestCase->setUploadFileToRequest($name = 'file', $filePath);
        $this->assertEquals($filename, $_FILES[$name]['name']);
        $this->assertEquals($filename, $bcTestCase->_request['files'][$name]['name']);
        unlink($filePath);
    }

    /**
     * test dropTable
     */
    public function testDropTable()
    {
        $table = 'table_for_test_drop_table';
        $columns = [
            'id' => ['type' => 'integer'],
            'contents' => ['type' => 'text'],
        ];
        $schema = new BcSchema($table, $columns);
        $schema->create();
        $this->dropTable($table);
        $tableList = TableRegistry::getTableLocator()
            ->get('BaserCore.App')
            ->getConnection()
            ->getSchemaCollection()
            ->listTables();
        $this->assertNotContains($table, $tableList);
    }

    /**
     * test getPrivateProperty
     */
    public function testGetPrivateProperty()
    {
        $className = 'DummyClass';
        $filePath = TMP . $className . '.php';
        $file = new File($filePath, true);
        // DummyClassファイルを作成する
        $file->write("<?php
class $className
{
    private \$privateVar;
    protected \$protectedVar;
    public function __construct(string \$privateVar = '', string \$protectedVar = '')
    {
        \$this->privateVar = \$privateVar;
        \$this->protectedVar = \$protectedVar;
    }
}");
        require_once $filePath;
        // private・protectedプロパティの初期値を設定する
        $dummyClass = new $className('private variable', 'protected variable');

        // privateプロパティ値をget
        $this->assertEquals('private variable', $this->getPrivateProperty($dummyClass, 'privateVar'));

        // protectedプロパティ値をget
        $this->assertEquals('protected variable', $this->getPrivateProperty($dummyClass, 'protectedVar'));

        // 作成したファイルを削除する
        $file->delete();
    }
}
