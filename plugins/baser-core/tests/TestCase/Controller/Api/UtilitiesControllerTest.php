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

namespace BaserCore\Test\TestCase\Controller\Api;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

class UtilitiesControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/Sites',
    ];

    /**
     * Access Token
     * @var string
     */
    public $accessToken = null;

    /**
     * Refresh Token
     * @var null
     */
    public $refreshToken = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        Configure::clear();
        parent::tearDown();
    }

    /**
     * test clear_cache
     * @return void
     */
    public function test_clear_cache()
    {
        $this->post('/baser/api/baser-core/utilities/clear_cache.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('サーバーキャッシュを削除しました。', $result->message);
    }
}
