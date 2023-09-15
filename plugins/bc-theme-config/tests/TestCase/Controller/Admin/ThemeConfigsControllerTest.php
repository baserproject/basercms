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

namespace BcThemeConfig\Test\TestCase\Controller\Admin;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcThemeConfig\Controller\Admin\ThemeConfigsController;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class ThemeConfigsControllerTest
 *
 * @property  ThemeConfigsController $ThemeConfigsController
 */
class ThemeConfigsControllerTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

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
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * [ADMIN] 設定編集
     */
    public function testIndex()
    {
        //準備
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(InitAppScenario::class);
        $data = [
            'name_add' => 'value_edit'
        ];
        $this->post("/baser/admin/bc-theme-config/theme_configs/index", $data);
        //ステータスを確認
        $this->assertResponseSuccess();
        $var = $this->_controller->viewBuilder()->getVars();
        $this->assertArrayHasKey('themeConfig', $var);
        $this->assertEquals('value_edit', $var['themeConfig']->name_add);
        $this->assertRedirect([
            'plugin' => 'BcThemeConfig',
            'prefix' => 'Admin',
            'controller' => 'ThemeConfigsController',
            'action' => 'index'
        ]);
    }

}
