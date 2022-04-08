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

use BaserCore\TestSuite\BcTestCase;
use Cake\Routing\Router;

/**
 * Class RouteCollectionTest
 */
class RouteCollectionTest extends BcTestCase
{

    /**
     * フィクスチャ
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Routing\Route\BcContentsRoute\SiteBcContentsRoute',
        'plugin.BaserCore.Routing\Route\BcContentsRoute\ContentBcContentsRoute',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
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

}
