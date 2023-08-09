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

namespace BaserCore\Test\TestCase\Error;

use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SmallSetContentsScenario;
use Cake\Core\Configure;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcExceptionRendererTest
 */
class BcExceptionRendererTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test _getController
     */
    public function test_getController()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SmallSetContentsScenario::class);

        // デバッグモードが有効だとcakeのエラー画面が表示されるため一時的に無効にする
        $debug = Configure::read('debug');
        Configure::write('debug', false);
        PermissionFactory::make()->allowGuest('/baser/api/*')->persist();

        $this->get('/baser/admin/baser-core/users_test/');
        $this->assertResponseError();
        $this->assertResponseContains('bs-container');
        $this->assertResponseContains('Not Found');

        $this->post('/baser/admin/baser-core/users_test/');
        $this->assertResponseError();
        $this->assertResponseContains('bs-container');
        $this->assertResponseContains('Missing or incorrect CSRF cookie type.');

        $this->get('/baser/admin/baser-core/users_test/test.js');
        $this->assertResponseError();
        $this->assertResponseContains('bs-container');

        $this->get('/baser/api/admin/baser-core/users/index.json');
        $this->assertResponseCode(401);

        $this->post('/baser/api/admin/baser-core/users/add.json');
        $this->assertResponseCode(403);

        Configure::write('debug', $debug);
    }
}
