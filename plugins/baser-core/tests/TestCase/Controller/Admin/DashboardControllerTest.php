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
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BaserCore\Controller\DashboardController Test Case
 */
class DashboardControllerTest extends BcTestCase
{
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $Users = $this->getTableLocator()->get('BaserCore.Users');
        $this->session(['AuthAdmin' => $Users->get(1)]);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/admin/');
        $this->assertResponseOk();
        $this->assertResponseContains('baserCMSニュース');
        $this->assertResponseContains('コンテンツ情報');
        $this->assertResponseContains('最近の動き');
    }
}
