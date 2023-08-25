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
use BcCustomContent\Controller\Admin\CustomFieldsController;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomFieldsControllerTest
 *  * @property CustomFieldsController $CustomFieldsController
 */
class CustomFieldsControllerTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->getRequest('/baser/admin/bc-custom-content/custom_fields/');
        $this->loginAdmin($request);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
    /**
     * Test beforeAddEvent
     */
    public function testBeforeAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomFields.beforeAdd', function (Event $event) {
            $data = $event->getData('data');
            $data['title'] = 'beforeAdd';
            $event->setData('data', $data);
        });
        //Postデータを生成
        $data = [
            'title' => '求人分類',
            'name' => 'recruit_category',
            'type' => 'BcCcRelated',
            'status' => 1,
            'default_value' => '新卒採用',
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_fields/add', $data);
        //イベントに入るかどうか確認
        $customFields = $this->getTableLocator()->get('BcCustomContent.CustomFields');
        $query = $customFields->find()->where(['title' => 'beforeAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomFields.afterAdd', function (Event $event) {
            $data = $event->getData('data');
            $contentLinks = TableRegistry::getTableLocator()->get('BcCustomContent.CustomFields');
            $data->title = 'afterAdd';
            $contentLinks->save($data);
        });
        //Postデータを生成
        $data = [
            'title' => '求人分類',
            'name' => 'recruit_category_2',
            'type' => 'BcCcRelated',
            'status' => 1,
            'default_value' => '新卒採用',
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_fields/add', $data);
        //イベントに入るかどうか確認
        $customFields = $this->getTableLocator()->get('BcCustomContent.CustomFields');
        $query = $customFields->find()->where(['title' => 'afterAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeEditEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomFields.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['title'] = 'beforeEdit';
            $event->setData('data', $data);
        });
        //Postデータを生成
        $data = CustomFieldFactory::get(1);
        $data['title'] = 'test edit title';
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_fields/edit/1', $data);
        //イベントに入るかどうか確認
        $customFields = $this->getTableLocator()->get('BcCustomContent.CustomFields');
        $query = $customFields->find()->where(['title' => 'beforeEdit']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterEditEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomFields.afterEdit', function (Event $event) {
            $data = $event->getData('data');
            $contentLinks = TableRegistry::getTableLocator()->get('BcCustomContent.CustomFields');
            $data->title = 'afterEdit';
            $contentLinks->save($data);
        });
        //Postデータを生成
        $data = CustomFieldFactory::get(1);
        $data['title'] = 'test edit title';
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_fields/edit/1', $data);
        //イベントに入るかどうか確認
        $customFields = $this->getTableLocator()->get('BcCustomContent.CustomFields');
        $query = $customFields->find()->where(['title' => 'afterEdit']);
        $this->assertEquals(1, $query->count());
    }
}
