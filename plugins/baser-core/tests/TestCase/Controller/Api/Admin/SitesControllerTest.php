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

namespace BaserCore\Test\TestCase\Controller\Api\Admin;

use ArrayObject;
use BaserCore\Test\Scenario\ContentFoldersScenario;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Test\Scenario\SitesScenario;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\Test\Scenario\UsersScenario;
use BaserCore\Test\Scenario\UsersUserGroupsScenario;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class SitesControllerTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * Access Token
     * @var string
     */
    public $accessToken = null;

    /**
     * Refresh Token
     * @var null
     */
    public $refreshToken = null;

    /**
     * set up
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
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
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
     * test View
     */
    public function testView(): void
    {
        $this->get('/baser/api/admin/baser-core/sites/view/2.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('smartphone', $result->site->name);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/api/admin/baser-core/sites/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('', $result->sites[0]->name);
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = [
            'name' => 'chinese',
            'display_name' => '中国語サイト',
            'title' => '中国語',
            'alias' => 'zh',
            'use_subdomain' => 0
        ];
        $this->post('/baser/api/admin/baser-core/sites/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();
        $sites = $this->getTableLocator()->get('Sites');
        $query = $sites->find()->where(['name' => $data['name']]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = [
            'name' => 'Test_test_Man'
        ];
        $this->post('/baser/api/admin/baser-core/sites/edit/1.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();
        $sites = $this->getTableLocator()->get('Sites');
        $query = $sites->find()->where(['name' => $data['name']]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/api/admin/baser-core/sites/delete/2.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
        $sites = $this->getTableLocator()->get('Sites');
        $query = $sites->find()->where(['id' => 2]);
        $this->assertEquals(0, $query->count());
    }

    /**
     * testGet_selectable_devices_and_lang
     *
     * @return void
     */
    public function testGet_selectable_devices_and_lang(): void
    {
        $this->get('/baser/api/admin/baser-core/sites/get_selectable_devices_and_lang/1/4.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();

        $devicesObj = new ArrayObject(json_decode($this->_response->getBody())->devices);
        $this->assertEquals(2, $devicesObj->count());

        $langsObj = new ArrayObject(json_decode($this->_response->getBody())->langs);
        $this->assertEquals(3, $langsObj->count());

        $sites = $this->getTableLocator()->get('Sites');
        $sites->delete($sites->get(2));
        $sites->delete($sites->get(3));

        $this->get('/baser/api/admin/baser-core/sites/get_selectable_devices_and_lang/1/4.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();

        $devicesObj = new ArrayObject(json_decode($this->_response->getBody())->devices);
        $this->assertEquals(3, $devicesObj->count());

        $langsObj = new ArrayObject(json_decode($this->_response->getBody())->langs);
        $this->assertEquals(4, $langsObj->count());
    }

}
