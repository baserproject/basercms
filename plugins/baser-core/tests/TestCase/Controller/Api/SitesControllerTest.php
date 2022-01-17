<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Controller\Api;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use ArrayObject;

class SitesControllerTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Sites'
    ];

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
        Configure::clear();
        parent::tearDown();
    }

    /**
     * test View
     */
    public function testView(): void
    {
        $this->get('/baser/api/baser-core/sites/view/2.json?token=' . $this->accessToken);
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
        $this->get('/baser/api/baser-core/sites/index.json?token=' . $this->accessToken);
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
            'alias' => 'zh'
        ];
        $this->post('/baser/api/baser-core/sites/add.json?token=' . $this->accessToken, $data);
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
        $this->post('/baser/api/baser-core/sites/edit/1.json?token=' . $this->accessToken, $data);
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
        $this->post('/baser/api/baser-core/sites/delete/2.json?token=' . $this->accessToken);
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
        $this->get('/baser/api/baser-core/sites/get_selectable_devices_and_lang/1/4.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();

        $devicesObj = new ArrayObject(json_decode($this->_response->getBody())->devices);
        $this->assertEquals(2, $devicesObj->count());

        $langsObj = new ArrayObject(json_decode($this->_response->getBody())->langs);
        $this->assertEquals(3, $langsObj->count());

        $sites = $this->getTableLocator()->get('Sites');
        $sites->delete($sites->get(2));
        $sites->delete($sites->get(3));

        $this->get('/baser/api/baser-core/sites/get_selectable_devices_and_lang/1/4.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();

        $devicesObj = new ArrayObject(json_decode($this->_response->getBody())->devices);
        $this->assertEquals(3, $devicesObj->count());

        $langsObj = new ArrayObject(json_decode($this->_response->getBody())->langs);
        $this->assertEquals(4, $langsObj->count());
    }

}
