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

namespace BcEditorTemplate\Test\TestCase\Controller\Admin;

use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcEditorTemplate\Test\Factory\EditorTemplateFactory;
use BcEditorTemplate\Test\Scenario\EditorTemplatesScenario;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class EditorTemplatesControllerTest
 */
class EditorTemplatesControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;
    use IntegrationTestTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        ConnectionManager::alias('test', 'default');
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->getRequest('/baser/admin/baser-core/uploader_categories/');
        $this->loginAdmin($request);
    }

    /**
     * Tear Down
     *
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->truncateTable('editor_templates');
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcEditorTemplate.EditorTemplates.beforeAdd', function (Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'beforeAdd';
            $event->setData('data', $data);
        });
        $data = [
            'name' => 'japan'
        ];
        $this->post('/baser/admin/bc-editor-template/editor_templates/add', $data);
        $editorTemplates = $this->getTableLocator()->get('BcEditorTemplate.EditorTemplates');
        $query = $editorTemplates->find()->where(['name' => 'beforeAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcEditorTemplate.EditorTemplates.afterAdd', function (Event $event) {
            $data = $event->getData('data');
            $editorTemplates = TableRegistry::getTableLocator()->get('BcEditorTemplate.EditorTemplates');
            $data->name = 'afterAdd';
            $editorTemplates->save($data);
        });
        $data = [
            'name' => 'japan2'
        ];
        $this->post('/baser/admin/bc-editor-template/editor_templates/add', $data);
        $editorTemplates = $this->getTableLocator()->get('BcEditorTemplate.EditorTemplates');
        $query = $editorTemplates->find()->where(['name' => 'afterAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test add
     */
    public function testAdmin_add()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //サービスクラス
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        //のバリューをリセットする
        $siteConfigsService->resetValue('editor');
        //set data
        $data = ['name' => 'test', 'description' => 'test description'];
        $this->post('/baser/admin/bc-editor-template/editor_templates/add', $data);
        //check status code
        $this->assertResponseCode(302);
        // データが登録されたか確認
        $editorTemplates = EditorTemplateFactory::get(1);
        // 登録データを確認
        $this->assertEquals($data['name'], $editorTemplates['name']);
        $this->assertEquals($data['description'], $editorTemplates['description']);
        //check message
        $this->assertFlashMessage('テンプレート「test」を追加しました');
        //check redirect
        $this->assertRedirect(['action' => 'index']);
        //check message error
        $this->post('/baser/admin/bc-editor-template/editor_templates/add', ['name' => '']);
        $this->assertFlashMessage('入力エラーです。内容を修正してください。');
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeEditEvent()
    {
        $this->loadFixtureScenario(EditorTemplatesScenario::class);
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcEditorTemplate.EditorTemplates.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'beforeAdd';
            $event->setData('data', $data);
        });
        $data = [
            'name' => 'japan'
        ];
        $this->post('/baser/admin/bc-editor-template/editor_templates/edit/11', $data);
        $editorTemplates = $this->getTableLocator()->get('BcEditorTemplate.EditorTemplates');
        $query = $editorTemplates->find()->where(['name' => 'beforeAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterEditEvent()
    {
        $this->loadFixtureScenario(EditorTemplatesScenario::class);
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcEditorTemplate.EditorTemplates.afterEdit', function (Event $event) {
            $data = $event->getData('data');
            $editorTemplates = TableRegistry::getTableLocator()->get('BcEditorTemplate.EditorTemplates');
            $data->name = 'afterAdd';
            $editorTemplates->save($data);
        });
        $data = [
            'name' => 'japan2'
        ];
        $this->post('/baser/admin/bc-editor-template/editor_templates/edit/11', $data);
        $editorTemplates = $this->getTableLocator()->get('BcEditorTemplate.EditorTemplates');
        $query = $editorTemplates->find()->where(['name' => 'afterAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test delete
     */
    public function testAdmin_delete()
    {
        //delete
        $this->loadFixtureScenario(EditorTemplatesScenario::class);
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $editorTemplates = $this->getTableLocator()->get('BcEditorTemplate.EditorTemplates');
        //check before delete
        $query = $editorTemplates->find()->where(['name' => '画像（左）とテキスト']);
        $this->assertEquals(1, $query->count());
        $this->post('/baser/admin/bc-editor-template/editor_templates/delete/11');
        // ステータスを確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('テンプレート「画像（左）とテキスト」を削除しました。');
        //check after delete
        $query = $editorTemplates->find()->where(['name' => '画像（左）とテキスト']);
        $this->assertEquals(0, $query->count());
        /**
         * check RecordNotFoundException
         */
        $this->expectException(RecordNotFoundException::class);
        EditorTemplateFactory::get(11);
    }
}
