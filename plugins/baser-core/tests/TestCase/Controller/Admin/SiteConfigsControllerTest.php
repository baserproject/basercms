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

use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class SiteConfigsControllerTest
 */
class SiteConfigsControllerTest extends BcTestCase
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
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Sites',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
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
     * [ADMIN] システム基本設定
     */
    public function testIndex()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $path = ROOT . DS . 'config' . DS . '.env';
        copy($path, $path . '.bak');
        $data = [
            'email' => 'hoge@basercms.net',
            'site_url' => 'http://localhost/',
            'mode' => false
        ];
        $this->post('/baser/admin/baser-core/site_configs/index', $data);
        $this->assertResponseSuccess();
        unlink($path);
        rename($path . '.bak', $path);
    }

}
