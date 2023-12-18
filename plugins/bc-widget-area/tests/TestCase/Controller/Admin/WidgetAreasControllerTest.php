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

namespace BcWidgetArea\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcWidgetArea\Controller\Admin\WidgetAreasController;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class WidgetAreasControllerTest
 *
 */
class WidgetAreasControllerTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Test subject
     *
     * @var WidgetAreasController
     */
    public $WidgetAreasController;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadFixtureScenario(InitAppScenario::class);
        $this->WidgetAreasController = new WidgetAreasController($this->getRequest());;
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->WidgetAreasController);
        parent::tearDown();
    }

    /**
     * 一覧
     */
    public function testAdmin_index()
    {
        $this->loginAdmin($this->getRequest('/'));
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->get('/baser/admin/bc-widget-area/widget_areas/index');
        $this->assertResponseOk();
        $pageTitle = $this->_controller->viewBuilder()->getVars()['pageTitle'];
        $this->assertEquals('ウィジェットエリア一覧', $pageTitle);
    }

    /**
     * 新規登録
     */
    public function testAdmin_add()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 編集
     */
    public function testAdmin_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }


    /**
     * 削除
     */
    public function testAdmin_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
