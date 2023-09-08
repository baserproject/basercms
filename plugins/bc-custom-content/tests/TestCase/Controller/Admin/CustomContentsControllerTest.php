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

namespace BcCustomContent\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Controller\Admin\CustomContentsController;
use Cake\Http\ServerRequest;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomContentsControllerTest
 */
class CustomContentsControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomContentsController
     */
    public $CustomContentsController;

    /**
     * Test subject
     *
     * @var ServerRequest
     */
    public $request;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->request = $this->loginAdmin($this->getRequest('/baser/admin/bc-custom-content/custom_contents/'));
        $this->CustomContentsController = new CustomContentsController($this->request);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->CustomContentsController, $this->request);
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertNotEmpty($this->CustomContentsController->BcAdminContents);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test beforeEdit
     */
    public function test_beforeEdit()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test afterEdit
     */
    public function test_afterEdit()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test index
     */
    public function test_index()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

}
