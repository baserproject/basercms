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

namespace BaserCore\Test\TestCase\Controller;

use BaserCore\Controller\UsersController;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BaserCore\Controller\UsersController Test Case
 */
class UsersControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use IntegrationTestTrait;
    use BcContainerTrait;
    use ScenarioAwareTrait;

    /**
     * UsersController
     * @var UsersController
     */
    public $UsersController;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        Configure::write('BcPrefixAuth.Front.disabled', false);
        $this->UsersController = new UsersController($this->getRequest('/'));
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UsersController);
        parent::tearDown();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertEquals($this->UsersController->Authentication->getUnauthenticatedActions(), ['login']);
        $this->assertNotEmpty($this->UsersController->Authentication->getConfig('logoutRedirect'));
    }

}
