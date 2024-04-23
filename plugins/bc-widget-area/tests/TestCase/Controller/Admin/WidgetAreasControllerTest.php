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
use BcWidgetArea\Test\Scenario\WidgetAreasScenario;
use BcWidgetArea\Test\Factory\WidgetAreaFactory;
use Cake\Datasource\Exception\RecordNotFoundException;
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
    }

    /**
     * 新規登録
     */
    public function testAdmin_add()
    {
        $this->loginAdmin($this->getRequest('/'));
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        // データ生成
        $data = [
            'name' => 'test',
            'widgets' => serialize([
                [
                    1 => 'test 1',
                    2 => 'test 2'
                ]
            ])
        ];

        //正常系実行
        $this->post('/baser/admin/bc-widget-area/widget_areas/add', $data);
        $this->assertResponseCode(302);
        $this->assertFlashMessage('新しいウィジェットエリアを保存しました。');
        $this->assertRedirect('/baser/admin/bc-widget-area/widget_areas/edit/1');

        //データが空の場合、
        $data = [
            'name' => '',
            'widgets' => ''
        ];
        $this->post('/baser/admin/bc-widget-area/widget_areas/add', $data);
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals(['name' => ['_empty' => "ウィジェットエリア名を入力してください。"]], $vars['widgetArea']->getErrors());
        $this->assertResponseCode(200);
    }

    /**
     * 編集
     */
    public function testAdmin_edit()
    {
        $this->loginAdmin($this->getRequest('/'));
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        // データ生成
        $this->loadFixtureScenario(WidgetAreasScenario::class);
        //対象メソッドを呼ぶ
        $this->get('/baser/admin/bc-widget-area/widget_areas/edit/1');
        // ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $vars = $this->_controller->viewBuilder()->getVars()['widgetArea'];
        $this->assertEquals("標準サイドバー", $vars->name);
    }


    /**
     * 削除
     */
    public function testAdmin_delete()
    {
        $this->loginAdmin($this->getRequest('/'));
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        // データ生成
        $this->loadFixtureScenario(WidgetAreasScenario::class);
        //対象メソッドを呼ぶ
        $this->post('/baser/admin/bc-widget-area/widget_areas/delete/1');
        // ステータスを確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('ウィジェットエリア「標準サイドバー」を削除しました。');
        // データが削除できるか確認
        $this->expectException(RecordNotFoundException::class);
        WidgetAreaFactory::get(1);
    }

}
