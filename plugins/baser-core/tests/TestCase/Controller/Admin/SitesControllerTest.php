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

use BaserCore\Controller\Admin\SitesController;
use BaserCore\Service\Admin\SitesAdminServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Test\Scenario\ContentFoldersScenario;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Test\Scenario\SitesScenario;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\Test\Scenario\UsersScenario;
use BaserCore\Test\Scenario\UsersUserGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class SitesControllerTest
 */
class SitesControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use IntegrationTestTrait;
    use BcContainerTrait;
    use ScenarioAwareTrait;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(SitesScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(ContentFoldersScenario::class);
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(UserGroupsScenario::class);
        $this->loadFixtureScenario(UsersUserGroupsScenario::class);
        $this->loadFixtureScenario(UsersScenario::class);
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
     * サイト一覧
     */
    public function testIndex()
    {
        $this->get('/baser/admin/baser-core/sites/');
        $this->assertResponseOk();
        // イベントテスト
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BaserCore.Sites.searchIndex', function(Event $event) {
            $request = $event->getData('request');
            return $request->withQueryParams(['num' => 1]);
        });
        // アクション実行（requestの変化を判定するため $this->get() ではなくクラスを直接利用）
        $sitesController = new SitesController($this->getRequest('/baser/admin/baser-core/sites/'));
        $sitesController->index($this->getService(SitesAdminServiceInterface::class), $this->getService(SiteConfigsServiceInterface::class));
        $this->assertEquals(1, $sitesController->getRequest()->getQuery('num'));
    }

    /**
     * サイト追加
     */
    public function testAdd()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $sites = $this->getTableLocator()->get('BaserCore.Sites');
        $data = [
            'name' => 'test',
            'display_name' => 'test',
            'alias' => 'test',
            'title' => 'test',
            'status' => true,
            'use_subdomain' => 0
        ];
        $this->post('/baser/admin/baser-core/sites/add', $data);
        $this->assertResponseSuccess();
        $query = $sites->find()->where(['name' => $data['name']]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BaserCore.Sites.beforeAdd', function(Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'etc';
            $event->setData('data', $data);
        });
        $data = [
            'name' => 'test2',
            'display_name' => 'test',
            'alias' => 'test',
            'title' => 'test',
            'status' => true,
            'use_subdomain' => 0
        ];
        $this->post('/baser/admin/baser-core/sites/add', $data);
        $sites = $this->getTableLocator()->get('BaserCore.Sites');
        $query = $sites->find()->where(['name' => 'etc']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test afterAddEvent
     */
    public function testAfterAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BaserCore.Sites.afterAdd', function(Event $event) {
            $site = $event->getData('data');
            $sites = $this->getTableLocator()->get('Sites');
            $site->name = 'etc';
            $sites->save($site);
        });
        $data = [
            'name' => 'test2',
            'display_name' => 'test',
            'alias' => 'test',
            'title' => 'test',
            'status' => true,
            'use_subdomain' => 0
        ];
        $this->post('/baser/admin/baser-core/sites/add', $data);
        $sites = $this->getTableLocator()->get('BaserCore.Sites');
        $query = $sites->find()->where(['name' => 'etc']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * サイト情報編集
     */
    public function testEdit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = [
            'display_name' => 'Test_test_Man'
        ];
        $this->post('/baser/admin/baser-core/sites/edit/1', $data);
        $this->assertResponseSuccess();

        // イベントテスト
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BaserCore.Sites.afterEdit', function(Event $event) {
            $site = $event->getData('data');
            $sites = $this->getTableLocator()->get('BaserCore.Sites');
            $site->display_name = 'etc';
            $sites->save($site);
        });
        $data = [
            'id' => 1,
            'name' => 'Test_test_Man2',
            'password_1' => 'Lorem ipsum dolor sit amet',
            'password_2' => 'Lorem ipsum dolor sit amet',
            'real_name_1' => 'Lorem ipsum dolor sit amet',
            'real_name_2' => 'Lorem ipsum dolor sit amet',
            'email' => 'test2@example.com',
            'nickname' => 'Lorem ipsum dolor sit amet',
            'use_subdomain' => 0
        ];
        $this->post('/baser/admin/baser-core/sites/edit/1', $data);
        $sites = $this->getTableLocator()->get('BaserCore.Sites');
        $query = $sites->find()->where(['display_name' => 'etc']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * 公開状態にする
     */
    public function testPublish()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $sites = $this->getTableLocator()->get('Sites');
        $siteId = 3;
        $site = $sites->get($siteId);
        $site->status = false;
        $sites->save($site);

        // getは更新不可
        $this->get('/baser/admin/baser-core/sites/publish/' . $siteId);
        $this->assertRedirect('/baser/admin/baser-core/sites/index');
        $site = $sites->find()->where(['id' => $siteId])->all()->last();
        $this->assertFalse($site->status);

        // postは更新可
        $this->post('/baser/admin/baser-core/sites/publish/' . $siteId);
        $this->assertRedirect('/baser/admin/baser-core/sites/index');
        $site = $sites->find()->where(['id' => $siteId])->all()->last();
        $this->assertTrue($site->status);
    }

    /**
     * 非公開状態にする
     */
    public function testUnpublish()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $sites = $this->getTableLocator()->get('Sites');
        $siteId = 3;
        $site = $sites->get($siteId);
        $site->status = true;
        $sites->save($site);

        // getは更新不可
        $this->get('/baser/admin/baser-core/sites/publish/' . $siteId);
        $this->assertRedirect('/baser/admin/baser-core/sites/index');
        $site = $sites->find()->where(['id' => $siteId])->all()->last();
        $this->assertTrue($site->status);

        // postは更新可
        $this->post('/baser/admin/baser-core/sites/unpublish/' . $siteId);
        $this->assertRedirect('/baser/admin/baser-core/sites/index');
        $site = $sites->find()->where(['id' => $siteId])->all()->last();
        $this->assertFalse($site->status);
    }

    /**
     * 削除する
     */
    public function testDelete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/admin/baser-core/sites/delete/2');
        $this->assertResponseSuccess();
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'sites',
            'action' => 'index'
        ]);
    }

}
