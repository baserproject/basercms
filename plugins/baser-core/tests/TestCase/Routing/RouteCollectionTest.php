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

namespace BaserCore\Test\TestCase\Routing;

use BaserCore\Test\Scenario\ContentBcContentsRouteScenario;
use BaserCore\Test\Scenario\SiteBcContentsRouteScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\Routing\RouteCollection;
use Cake\Routing\Router;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Class RouteCollectionTest
 */
class RouteCollectionTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->RouteCollection = new RouteCollection();
    }

    /**
     * tear down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test Match
     */
    public function testMatch()
    {
        $this->loadFixtureScenario(ContentBcContentsRouteScenario::class);
        $this->loadFixtureScenario(SiteBcContentsRouteScenario::class);
        $this->getRequest();
        $this->assertEquals('/', Router::url([
            'plugin' => 'BaserCore',
            'controller' => 'ContentFolders',
            'action' => 'view',
            'entityId' => 1
        ]));
        $this->assertEquals('/service/', Router::url([
            'plugin' => 'BaserCore',
            'controller' => 'ContentFolders',
            'action' => 'view',
            'entityId' => 4
        ]));
    }

    /**
     * test hasMiddlewareGroup
     */
    public function testHasMiddlewareGroup()
    {
        $middleware = $this->createMock(MiddlewareInterface::class);
        $this->RouteCollection->registerMiddleware('middleware1', $middleware);
        $this->RouteCollection->registerMiddleware('middleware2', $middleware);

        //add middleware group
        $this->RouteCollection->middlewareGroup('group1', ['middleware1', 'middleware2']);

        // check if middleware group exists
        $this->assertTrue($this->RouteCollection->hasMiddlewareGroup('group1'));
        // check if middleware group does not exist
        $this->assertFalse($this->RouteCollection->hasMiddlewareGroup('nonexistent_group'));
    }

}
