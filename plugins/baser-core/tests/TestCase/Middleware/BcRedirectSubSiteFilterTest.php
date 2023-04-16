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

use BaserCore\Middleware\BcRedirectSubSiteFilter;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\PageFactory;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\MultiSiteScenario;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcRedirectSubSiteFilterTest
 * @property BcRedirectSubSiteFilter $BcRedirectSubSiteFilter
 */
class BcRedirectSubSiteFilterTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Pages',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcRedirectSubSiteFilter = new BcRedirectSubSiteFilter();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcRedirectSubSiteFilter);
        parent::tearDown();
    }

    /**
     * test Process
     * リダイレクトを確認
     */
    public function test_process(): void
    {
        SiteFactory::make([
            'id' => 1,
            'name' => '',
            'title' => 'baserCMS inc.',
            'status' => true
        ])->persist();
        SiteFactory::make([
            'id' => 2,
            'name' => '',
            'title' => 'baserCMS inc. sub',
            'status' => true,
            'main_site_id' => 1,
            'device' => 'smartphone',
            'auto_redirect' => true
        ])->persist();
        PageFactory::make(['id' => 1])->persist();
        ContentFactory::make([
            'id' => 1,
            'url' => '/about',
            'name' => 'about',
            'plugin' => 'BaserCore',
            'type' => 'Page',
            'site_id' => 1,
            'parent_id' => null,
            'lft' => 1,
            'rght' => 2,
            'entity_id' => 1,
            'site_root' => 2,
            'status' => true
        ])->persist();
        SiteConfigFactory::make([
            'name' => 'use_site_device_setting',
            'value' => 'iPhone'
        ])->persist();

        $_SERVER['HTTP_USER_AGENT'] = 'iPhone';
        $request = $this->getRequest('/about')->withParam('plugin', 'BaserCore')->withParam('controller', 'Pages')->withParam('action', 'view');
        $this->_response = $this->BcRedirectSubSiteFilter->process($request, $this->Application);
        $this->assertResponseCode(302);
    }

    /**
     * test Process
     * 「クエリーパラメーターに、{$site->name}_auto_redirect=off と設定されている場合はリダイレクトしない」を確認
     */
    public function test_process_auto_redirect_off(): void
    {
        //データ生成
        $this->loadFixtureScenario(MultiSiteScenario::class);
        PageFactory::make(['id' => 1, 'page_template' => 'default'])->persist();
        ContentFactory::make([
            'id' => 6,
            'url' => '/about',
            'name' => 'about',
            'plugin' => 'BaserCore',
            'type' => 'Page',
            'site_id' => 1,
            'parent_id' => null,
            'lft' => 1,
            'rght' => 2,
            'entity_id' => 1,
            'site_root' => 2,
            'status' => true
        ])->persist();

        $request = $this->getRequest('/about?smartphone_auto_redirect=off')
            ->withParam('plugin', 'BaserCore')
            ->withParam('controller', 'Pages')
            ->withParam('action', 'view');
        $this->_response = $this->BcRedirectSubSiteFilter->process($request, $this->Application);
        //リダイレクトしない確認
        $this->assertResponseSuccess();
    }

    /**
     * test Process
     * 「リダイレクト先のサイトが非公開の場合はリダイレクトしない」を確認
     */
    public function test_process_site_private(): void
    {
        //データ生成
        $this->loadFixtureScenario(InitAppScenario::class);

        //サイトが非公開を設定する
        $SitesService = $this->getService(SitesServiceInterface::class);
        $SitesService->unpublish(1);

        $request = $this->loginAdmin($this->getRequest('/baser/admin/?site_id=1'));
        $this->_response = $this->BcRedirectSubSiteFilter->process($request, $this->Application);
        //リダイレクトしない確認
        $this->assertResponseSuccess();
    }

    /**
     * test Process
     * 「管理画面へのアクセスの場合には無視する。」を確認
     */
    public function test_process_admin(): void
    {
        //データ生成
        $this->loadFixtureScenario(InitAppScenario::class);

        //管理画面へのアクセスを確認
        $request = $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->_response = $this->BcRedirectSubSiteFilter->process($request, $this->Application);
        //リダイレクトしない確認
        $this->assertResponseSuccess();
    }
}
