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

use BaserCore\Middleware\BcFrontMiddleware;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class BcFrontMiddlewareTest
 * @property BcFrontMiddleware $BcFrontMiddleware
 */
class BcFrontMiddlewareTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Contents',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->BcFrontMiddleware = new BcFrontMiddleware();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcFrontMiddleware);
        parent::tearDown();
    }

    /**
     * Test setCurrent
     */
    public function test_setCurrent(): void
    {
        ContentFactory::make([
            'id' => 1,
            'entity_id' => 1,
            'url' => '/',
            'site_id' => 1,
            'status' => true,
        ])->persist();
        SiteFactory::make([
            'id' => 1,
            'name' => '',
            'title' => 'baserCMS inc.',
            'status' => true,
        ])->persist();

        $request = $this->getRequest()->withQueryParams([
            'Site' => SiteFactory::get(1),
            'Content' => ContentFactory::get(1)
        ]);
        $request = $this->BcFrontMiddleware->setCurrent($request);
        $this->assertNotEmpty($request->getAttribute('currentContent'));
        $this->assertNotEmpty($request->getAttribute('currentSite'));

        $request = $this->getRequest();
        $request = $this->BcFrontMiddleware->setCurrent($request);
        $this->assertNotEmpty($request->getAttribute('currentContent'));
        $this->assertNotEmpty($request->getAttribute('currentSite'));
    }

}
