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

use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BaserCore\Controller\Api\UserGroupsController Test Case
 */
class UserGroupsControllerTest extends BcTestCase
{
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
        'plugin.BaserCore.LoginStores'
    ];

    /**
     * Token
     * @var string
     */
    public $token = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        Configure::config('baser', new PhpConfig());
        Configure::load('BaserCore.setting', 'baser');
        $this->token = Configure::read('BcApp.apiToken');
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/api/baser-core/user_groups/index.json?token=' . $this->token);
        $this->assertResponseOk();
        $result = json_decode((string) $this->_response->getBody());
        $this->assertEquals('admins', $result->userGroups[0]->name);
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
            'name' => 'ucmitzGroup',
            'title' => 'ucmitzグループ',
            'use_move_contents' => '1',
        ];
        $this->post('/baser/api/baser-core/user_groups/add.json?token=' . $this->token, $data);
        $this->assertResponseSuccess();
        $UserGroups = $this->getTableLocator()->get('UserGroups');
        $query = $UserGroups->find()->where(['name' => $data['name']]);
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
        $this->post('/baser/api/baser-core/user_groups/edit/1.json?token=' . $this->token, $data);
        $this->assertResponseSuccess();
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
        $this->post('/baser/admin/baser-core/UserGroups/delete/1.json?token=' . $this->token);
        $this->assertResponseSuccess();
    }

    /**
     * Test View
     */
    public function testView()
    {
        $this->get('/baser/api/baser-core/user_groups/view/1.json?token=' . $this->token);
        $this->assertResponseOk();
        $result = json_decode((string) $this->_response->getBody());
        $this->assertEquals('admins', $result->userGroups->name);
    }

}
