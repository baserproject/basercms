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

namespace BaserCore\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class SiteConfigsControllerTest
 */
class SiteConfigsControllerTest extends BcTestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loginAdmin($this->getRequest());
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
     * [ADMIN] システム基本設定
     */
    public function testIndex()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $path = ROOT . DS . 'config' . DS . '.env';
        copy($path, $path . '.bak');
        $data = [
            'email' => 'hoge@basercms.net',
            'site_url' => 'http://localhost/',
            'mode' => false
        ];
        $this->post('/baser/admin/baser-core/site_configs/index', $data);
        $this->assertResponseSuccess();
        unlink($path);
        rename($path . '.bak', $path);
    }

}
