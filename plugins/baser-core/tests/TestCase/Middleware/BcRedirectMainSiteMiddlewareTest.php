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

use BaserCore\Middleware\BcRedirectMainSiteMiddleware;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\PageFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcRedirectMainSiteMiddlewareTest
 * @property BcRedirectMainSiteMiddleware $BcRedirectMainSiteMiddleware
 */
class BcRedirectMainSiteMiddlewareTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcRedirectMainSiteMiddleware = new BcRedirectMainSiteMiddleware();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcRedirectMainSiteMiddleware);
        parent::tearDown();
    }

    /**
     * test Process
     * リダイレクトを確認
     */
    public function test_process(): void
    {
        SiteFactory::make(['id' => 1, 'status' => true])->persist();
        SiteFactory::make(['id' => 2, 'alias' => 's', 'auto_redirect' => true])->persist();
        PageFactory::make(['id' => 1])->persist();
        ContentFactory::make([
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

        $request = $this->getRequest('/s/about')->withParam('plugin', 'BaserCore')->withParam('controller', 'Pages')->withParam('action', 'view');
        $this->_response = $this->BcRedirectMainSiteMiddleware->process($request, $this->Application);
        $this->assertResponseCode(302);

        $request = $this->getRequest('/about')->withParam('plugin', 'BaserCore')->withParam('controller', 'Pages')->withParam('action', 'view');
        $this->_response = $this->BcRedirectMainSiteMiddleware->process($request, $this->Application);
        $this->assertResponseCode(200);
    }
}
