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

namespace BaserCore\Test\TestCase\Middleware;

use BaserCore\Middleware\BcRequestFilterMiddleware;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use ReflectionClass;

/**
 * Class BcRequestFilterMiddlewareTest
 * @property BcRequestFilterMiddleware $BcRequestFilterMiddleware
 */
class BcRequestFilterMiddlewareTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        if (preg_match('/^testIsInstall/', $this->getName())) {
            Configure::write('BcRequest.isInstalled', false);
        } else {
            Configure::write('BcRequest.isInstalled', true);
        }
        parent::setUp();
        $this->BcRequestFilterMiddleware = new BcRequestFilterMiddleware();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcRequestFilterMiddleware);
        parent::tearDown();
    }

    /**
     * Process
     */
    public function testProcess(): void
    {
        $this->_response = $this->BcRequestFilterMiddleware->process($this->getRequest(), $this->Application);
        $this->assertResponseOk();
        $url = '/img/test.png';
        $this->_response = $this->BcRequestFilterMiddleware->process($this->getRequest($url), $this->Application);
        $this->assertTrue(Configure::read('BcRequest.asset'));
    }

    /**
     * test redirectIfIsDeviceFile
     */
    public function testRedirectIfIsDeviceFile()
    {
        $this->_response = $this->BcRequestFilterMiddleware->redirectIfIsDeviceFile($this->getRequest(), $this->Application);
        $this->assertNull($this->_response);
        $url = '/s/files/test.png';
        $this->_response = $this->BcRequestFilterMiddleware->redirectIfIsDeviceFile($this->getRequest($url), $this->Application);
        $this->assertRedirect('/files/test.png');
    }

    /**
     * リクエスト検出器の設定を取得
     */
    public function testGetDetectorConfigs()
    {
        $this->assertTrue(is_callable($this->BcRequestFilterMiddleware->getDetectorConfigs()['admin']));
    }

    /**
     * リクエスト検出器を追加する
     */
    public function testAddDetectors()
    {
        $request = $this->getRequest('/baser/admin');
        $ref = new ReflectionClass($request);
        $detectors = $ref->getProperty('_detectors');
        $detectors->setAccessible(true);
        $ref2 = new ReflectionClass(BcUtil::class);
        $detectors2 = $ref2->getProperty('_detectors');
        $detectors2->setAccessible(true);
        $detectors->setValue($detectors2->getValue());
        $this->assertFalse($request->is('admin'));
        $request = $this->BcRequestFilterMiddleware->addDetectors($request);
        $this->assertTrue($request->is('admin'));
    }

    /**
     * 管理画面のURLかどうかを判定
     *
     * @param bool $expect 期待値
     * @param string $url URL文字列
     * @return void
     * @dataProvider isAdminDataProvider
     */
    public function testIsAdmin($expect, $url)
    {
        $this->assertEquals($expect, $this->BcRequestFilterMiddleware->isAdmin($this->getRequest($url)));
    }

    /**
     * isAdmin用データプロバイダ
     *
     * @return array
     */
    public function isAdminDataProvider()
    {
        return [
            [true, '/baser/admin'],
            [true, '/baser/admin/'],
            [true, '/baser/admin/users/login'],
            [false, '/'],
            [false, '/s/'],
            [false, '/news/index'],
            [false, '/service']
        ];
    }

    /**
     * アセットのURLかどうかを判定
     *
     * @param bool $expect 期待値
     * @param string $url URL文字列
     * @return void
     * @dataProvider isAssetDataProvider
     */
    public function testIsAsset($expect, $url)
    {
        $this->assertEquals($expect, $this->BcRequestFilterMiddleware->isAsset($this->getRequest($url)));
    }

    /**
     * isAsset用データプロバイダ
     *
     * @return array
     */
    public function isAssetDataProvider()
    {
        return [
            [false, '/'],
            [false, '/about'],
            [false, '/img/test.html'],
            [false, '/js/test.php'],
            [false, '/css/file.cgi'],
            [true, '/img/image.png'],
            [true, '/js/startup.js'],
            [true, '/css/main.css'],
            [false, '/theme/example_theme/img/test.html'],
            [false, '/theme/example_theme/js/test.php'],
            [false, '/theme/example_theme/css/file.cgi'],
            [true, '/theme/example_theme/img/image.png'],
            [true, '/theme/example_theme/js/startup.js'],
            [true, '/theme/example_theme/css/main.css']
        ];
    }

    /**
     * インストール用のURLかどうかを判定
     *
     * @param bool $expect 期待値
     * @param string $url URL文字列
     * @return void
     * @dataProvider isInstallDataProvider
     */
    public function testIsInstall($expect, $url)
    {
        $this->assertEquals($expect, $this->BcRequestFilterMiddleware->isInstall($this->getRequest($url)));
    }

    /**
     * isInstall用データプロバイダ
     *
     * @return array
     */
    public function isInstallDataProvider()
    {
        return [
            [true, '/install'],
            [true, '/install/'],
            [false, '/install/index'],
            [true, '/installations/step2'],
            [true, '/'],
            [false, '/service']
        ];
    }

    /**
     * メンテナンス用のURLかどうかを判定
     *
     * @param bool $expect 期待値
     * @param string $url URL文字列
     * @return void
     * @dataProvider isMaintenanceDataProvider
     */
    public function testIsMaintenance($expect, $url)
    {
        $this->assertEquals($expect, $this->BcRequestFilterMiddleware->isMaintenance($this->getRequest($url)));
    }

    /**
     * isMaintenance用データプロバイダ
     *
     * @return array
     */
    public function isMaintenanceDataProvider()
    {
        return [
            [true, '/maintenance'],
            [true, '/maintenance/'],
            [true, '/maintenance/index'],
            [false, '/'],
            [false, '/service'],
            [false, '/admin/']
        ];
    }

    /**
     * 固定ページ表示用のURLかどうかを判定
     * [注]ルーターによるURLパース後のみ
     *
     * @param bool $expect 期待値
     * @param string $url URL文字列
     * @return void
     * @dataProvider isPageDataProvider
     */
    public function testIsPage($expect, $url)
    {
        $this->assertEquals($expect, $this->BcRequestFilterMiddleware->isPage($this->getRequest($url)));
    }

    /**
     * isPage用データプロバイダ
     *
     * @return array
     */
    public function isPageDataProvider()
    {
        return [
            [false, '/admin/'],
            [false, '/news/index'],
            [true, '/'],
            [true, '/service/service1'],
            [false, '/sample'], // テストにて論理削除されているためfalse
            [false, '/recruit']
        ];
    }

    /**
     * baserCMSの基本処理を必要とするかどうか
     */
    public function testIsRequestView()
    {
        $url = '/';
        $this->assertTrue($this->BcRequestFilterMiddleware->isRequestView($this->getRequest($url)));
        $url = '/?requestview=true';
        $this->assertTrue($this->BcRequestFilterMiddleware->isRequestView($this->getRequest($url)));
        $url = '/?requestview=false';
        $this->assertFalse($this->BcRequestFilterMiddleware->isRequestView($this->getRequest($url)));
    }

}
