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

use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\DblogsScenario;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class DblogsControllerTest extends BcTestCase
{
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(DblogsScenario::class);
        $this->loginAdmin($this->getRequest('/baser/admin'));
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test index
     */
    public function testIndex()
    {
        $this->get('/baser/admin/baser-core/dblogs/');
        $this->assertResponseOk();
    }

    /**
     * Test index pagination
     */
    public function testIndex_pagination()
    {
        $this->get('/baser/admin/baser-core/dblogs/?limit=1&page=2');
        $this->assertResponseOk();
        $this->get('/baser/admin/baser-core/dblogs/?limit=1&page=100');
        $this->assertRedirect(['action' => 'index']);
    }

    /**
     * Test delete_all
     */
    public function testDelete_all()
    {
        $this->get('/baser/admin/baser-core/dblogs/delete_all');
        $this->assertResponseError();

        $this->post('/baser/admin/baser-core/dblogs/delete_all');
        $this->assertResponseError();

        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/admin/baser-core/dblogs/delete_all');
        $this->assertResponseCode(302);
    }
}
