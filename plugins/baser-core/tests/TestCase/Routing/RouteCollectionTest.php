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
     * @dataProvider extensionsProvider
     */
    public function testSetExtensions(array $initial, array $new, bool $merge, array $expected)
    {
        // Set initial extensions
        $this->RouteCollection->setExtensions($initial);

        // SetExtensions with new data
        $this->RouteCollection->setExtensions($new, $merge);
        $expected = array_unique($expected);
        $actual = array_values($this->RouteCollection->getExtensions());

        $this->assertEquals($expected, $actual);
    }

    public static function extensionsProvider()
    {
        return [
            ['initial' => ['jpg', 'png'], 'new' => ['png', 'gif'], 'merge' => true, 'expected' => ['jpg', 'png', 'gif']],
            ['initial' => ['jpg', 'png'], 'new' => ['gif'],'merge' => false, 'expected' => ['gif']],
            ['initial' => [], 'new' => ['gif', 'jpg'], 'merge' => true, 'expected' => ['gif', 'jpg']],
            ['initial' => ['jpg'], 'new' => [], 'merge' => true, 'expected' => ['jpg']],
        ];
    }

}
