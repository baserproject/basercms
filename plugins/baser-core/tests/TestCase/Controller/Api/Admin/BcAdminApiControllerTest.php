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

namespace BaserCore\Test\TestCase\Controller\Api\Admin;

use Cake\Event\Event;
use BaserCore\Error\BcBadRequestException;
use BaserCore\Error\BcException;
use Cake\Core\Configure;
use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use Authentication\Identity;
use BaserCore\Controller\Api\Admin\BcAdminApiController;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\LoginStoresScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BaserCore\Controller\ApiControllerTest Test Case
 */
class BcAdminApiControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use IntegrationTestTrait;
    use BcContainerTrait;
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(LoginStoresScenario::class);

    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testInitialize()
    {
        $controller = new BcAdminApiController($this->getRequest());
        $this->assertNotNull($controller->Authentication);
        $this->assertFalse($controller->FormProtection->getConfig('validate'));
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testBeforeFilter()
    {
        $token = $this->apiLoginAdmin(1);
        $sessionKey = \Cake\Core\Configure::read('BcPrefixAuth.Api/Admin.sessionKey');

        // トークンタイプチェック（USE_CORE_ADMIN_API はテスト既定で true）
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['refresh_token']);
        $this->assertResponseCode(401);
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['access_token']);
        $this->assertResponseOk();

        // GHSA-wgvx-x5g3-9v29: APIを無効化（USE_CORE_ADMIN_API=false）した際の挙動
        $_SERVER['USE_CORE_ADMIN_API'] = 'false';

        // (1) 外部クライアント想定：有効 JWT・セッション無し → 403（本丸：Referer 偽装では通らない）
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['access_token']);
        $this->assertResponseCode(403);

        // (2) Referer のみ設定・未認証 → 403（Referer なりすまし無効化＝CWE-290 是正の核心）
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['HTTP_REFERER'] = 'https://localhost';
        $this->get('/baser/api/admin/baser-core/users/index.json');
        $this->assertResponseCode(403);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['HTTP_REFERER']);

        // (3) 管理画面 SPA 想定：セッション認証（JWT 無し）→ 200
        $this->session([$sessionKey => $this->getUser(1)]);
        $this->get('/baser/api/admin/baser-core/users/index.json');
        $this->assertResponseCode(200);

        // 初期化
        $_SERVER['USE_CORE_ADMIN_API'] = 'true';

        // ユーザーの有効チェック
        $users = $this->getTableLocator()->get('BaserCore.Users');
        $user = $users->get(1);
        $user->status = false;
        $users->save($user);
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['access_token']);
        $this->assertResponseCode(401);
    }

    /**
     * test isAdminApiEnabled
     */
    public function test_isAdminApiEnabled()
    {
        $controller = new BcAdminApiController($this->getRequest());
        $controller->loadComponent('Authentication.Authentication');

        // USE_CORE_ADMIN_API = 'true';
        $_SERVER['USE_CORE_ADMIN_API'] = 'true';

        // - 認証済
        $controller->setRequest($controller->getRequest()->withAttribute('identity', new Identity([])));
        $this->assertTrue($controller->isAdminApiEnabled());

        // - 未認証
        $controller->setRequest($controller->getRequest()->withAttribute('identity', null));
        $this->assertFalse($controller->isAdminApiEnabled());

        // USE_CORE_ADMIN_API = 'false';
        $_SERVER['USE_CORE_ADMIN_API'] = 'false';
        $this->assertFalse($controller->isAdminApiEnabled());

        $_SERVER['USE_CORE_ADMIN_API'] = 'true';
    }

    /**
     * test isAvailableUser
     */
    public function testIsAvailableUser()
    {
        UserFactory::make(['id' => 2])->persist();
        $request = $this->getRequest('/baser/admin/baser-core/themes/');

        $controller = new BcAdminApiController($this->loginAdmin($request));
        $this->assertTrue($controller->isAvailableUser());

        $controller = new BcAdminApiController($this->loginAdmin($request, 2));
        $this->assertFalse($controller->isAvailableUser());
    }

    /**
     * test beforeFilter - 管理画面 API が無効な場合は認証不要のアクション（ログイン等）も 403 になること
     *
     * 公開すると USE_CORE_ADMIN_API=false でも API ログインが到達可能になり、
     * 試行回数制限の無い認証オラクルとして利用できてしまう
     */
    public function testBeforeFilter_deniesUnauthenticatedActionsWhenApiDisabled()
    {
        $original = env('USE_CORE_ADMIN_API');
        putenv('USE_CORE_ADMIN_API=false');
        $_ENV['USE_CORE_ADMIN_API'] = 'false';
        $_SERVER['USE_CORE_ADMIN_API'] = 'false';
        try {
            $request = $this->getRequest('/baser/api/admin/baser-core/users/login.json')
                ->withParam('action', 'login');
            $controller = new BcAdminApiController($request);
            $controller->Authentication->allowUnauthenticated(['login']);
            $this->expectException('Cake\\Http\\Exception\\ForbiddenException');
            $controller->beforeFilter(new Event('beforeFilter'));
        } finally {
            putenv('USE_CORE_ADMIN_API=' . $original);
            $_ENV['USE_CORE_ADMIN_API'] = $original;
            $_SERVER['USE_CORE_ADMIN_API'] = $original;
        }
    }

    /**
     * test handleThrowable
     *
     * 例外の種類ごとに、応答のステータスコードとメッセージが決まること
     * PHP・CakePHP・DB が出す例外の内部情報は、デバッグモードでない場合に応答へ含めないこと
     */
    public function test_handleThrowable()
    {
        $controller = new BcAdminApiController($this->getRequest());

        // 存在しないデータは 404
        $message = $this->execPrivateMethod($controller, 'handleThrowable', [new RecordNotFoundException('Record not found in table `contents`.')]);
        $this->assertEquals(404, $controller->getResponse()->getStatusCode());
        $this->assertEquals('データが見つかりません。', $message);
        $message = $this->execPrivateMethod($controller, 'handleThrowable', [new InvalidPrimaryKeyException('Record not found in table `contents` with primary key `[NULL]`.')]);
        $this->assertEquals(404, $controller->getResponse()->getStatusCode());
        $this->assertEquals('データが見つかりません。', $message);

        // 入力の誤りは 400 とし、メッセージを返す
        $message = $this->execPrivateMethod($controller, 'handleThrowable', [new BcBadRequestException('ファイル名が不正です。')]);
        $this->assertEquals(400, $controller->getResponse()->getStatusCode());
        $this->assertEquals('ファイル名が不正です。', $message);

        // HttpException はそのステータスコード
        $message = $this->execPrivateMethod($controller, 'handleThrowable', [new ForbiddenException('権限がありません。')]);
        $this->assertEquals(403, $controller->getResponse()->getStatusCode());
        $this->assertEquals('権限がありません。', $message);

        // BcException は 500 とし、baserCMS が書いたメッセージを返す
        $message = $this->execPrivateMethod($controller, 'handleThrowable', [new BcException('同じ名前のファイルが既に存在します。')]);
        $this->assertEquals(500, $controller->getResponse()->getStatusCode());
        $this->assertEquals('データベース処理中にエラーが発生しました。同じ名前のファイルが既に存在します。', $message);

        // それ以外は 500 とし、デバッグモードでない場合は例外メッセージを返さない
        $debug = Configure::read('debug');
        try {
            Configure::write('debug', false);
            $message = $this->execPrivateMethod($controller, 'handleThrowable', [new \TypeError('Argument #1 must be of type int, called in /var/www/html/src/Foo.php on line 1')]);
            $this->assertEquals(500, $controller->getResponse()->getStatusCode());
            $this->assertEquals('データベース処理中にエラーが発生しました。', $message);

            // 前置きの文言を指定できること
            $message = $this->execPrivateMethod($controller, 'handleThrowable', [new \InvalidArgumentException('Cannot convert value'), '処理中にエラーが発生しました。']);
            $this->assertEquals('処理中にエラーが発生しました。', $message);

            // デバッグモードの場合は例外メッセージを付ける
            Configure::write('debug', true);
            $message = $this->execPrivateMethod($controller, 'handleThrowable', [new \InvalidArgumentException('Cannot convert value')]);
            $this->assertEquals('データベース処理中にエラーが発生しました。Cannot convert value', $message);
        } finally {
            Configure::write('debug', $debug);
        }
    }

    /**
     * test validateBatchRequest
     *
     * 一括処理の処理名・対象が不正な場合は 400 とメッセージを設定して false を返すこと
     */
    public function test_validateBatchRequest()
    {
        $allowMethod = ['publish' => '公開', 'delete' => '削除'];
        $cases = [
            // [送信値, ID を対象とするか, 期待する結果, 期待するメッセージ]
            [['batch' => 'delete', 'batch_targets' => [1, '2']], true, true, null],
            [['batch' => 'add', 'batch_targets' => [1]], true, false, '一括処理の処理内容が不正です。'],
            [['batch' => ['delete'], 'batch_targets' => [1]], true, false, '一括処理の処理内容が不正です。'],
            [['batch_targets' => [1]], true, false, '一括処理の処理内容が不正です。'],
            [['batch' => 'delete'], true, false, '一括処理の対象を指定してください。'],
            [['batch' => 'delete', 'batch_targets' => '1'], true, false, '一括処理の対象を指定してください。'],
            [['batch' => 'delete', 'batch_targets' => []], true, false, '一括処理の対象を指定してください。'],
            [['batch' => 'delete', 'batch_targets' => [1, "1'"]], true, false, '一括処理の対象に不正な値が含まれています。'],
            [['batch' => 'delete', 'batch_targets' => [['1']]], true, false, '一括処理の対象に不正な値が含まれています。'],
            // ID 以外（パスなど）を対象とする場合は、数字以外も受け付ける
            [['batch' => 'delete', 'batch_targets' => ['/path/to/file']], false, true, null],
            [['batch' => 'delete', 'batch_targets' => ['']], false, false, '一括処理の対象に不正な値が含まれています。'],
        ];
        foreach($cases as [$data, $integerTargets, $expected, $expectedMessage]) {
            $controller = new BcAdminApiController($this->getRequest('/', $data, 'post'));
            $result = $this->execPrivateMethod($controller, 'validateBatchRequest', [$allowMethod, $integerTargets]);
            $this->assertSame($expected, $result, json_encode($data));
            if ($expected) continue;
            $this->assertEquals(400, $controller->getResponse()->getStatusCode());
            $this->assertEquals($expectedMessage, $controller->viewBuilder()->getVar('message'));
        }
    }
}
