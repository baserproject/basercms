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

namespace BaserCore\Test\TestCase\Controller\Admin;

use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;

class DblogsControllerTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Dblogs',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $request = $this->getRequest();
        $request = $this->loginAdmin($request);
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
     * Test index
     */
    public function testIndex()
    {
        $this->get('/baser/admin/baser-core/dblogs/');
        $this->assertResponseOk();
    }

    /**
     * Test index pagination
     */
    public function testIndex_pagination()
    {
        $this->get('/baser/admin/baser-core/dblogs/?limit=1&page=2');
        $this->assertResponseOk();
        $this->get('/baser/admin/baser-core/dblogs/?limit=1&page=100');
        $this->assertResponseError();
    }

    /**
     * Test delete_all
     */
    public function testDelete_all()
    {
        $this->get('/baser/admin/baser-core/dblogs/delete_all');
        $this->assertResponseError();

        $this->post('/baser/admin/baser-core/dblogs/delete_all');
        $this->assertResponseError();

        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/admin/baser-core/dblogs/delete_all');
        $this->assertResponseCode(302);
    }
}
