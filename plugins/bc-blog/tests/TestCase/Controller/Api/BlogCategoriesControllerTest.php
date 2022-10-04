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

namespace BcBlog\Test\TestCase\Controller\Api;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\Api\BlogCategoriesController;
use BcBlog\Test\Factory\BlogCategoryFactory;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class BlogCategoriesControllerTest
 * @package BcBlog\Test\TestCase\Controller\Api
 * @property BlogCategoriesController $BlogCategoriesController
 */
class BlogCategoriesControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
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
        $this->setFixtureTruncate();
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
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
     * test index
     * @return void
     */
    public function test_index()
    {
        BlogCategoryFactory::make(['blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['blog_content_id' => 2])->persist();

        $this->get('/baser/api/bc-blog/blog_categories/index/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->blogCategories);
    }

    /**
     * test view
     * @return void
     */
    public function test_view()
    {
        BlogCategoryFactory::make(['id' => 1, 'name' => 'Blog Category 1'])->persist();
        BlogCategoryFactory::make(['id' => 2, 'name' => 'Blog Category 2'])->persist();

        $this->get('/baser/api/bc-blog/blog_categories/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('Blog Category 1', $result->blogCategory->name);
        $this->assertEquals(1, $result->blogCategory->id);
    }

    /**
     * test list
     * @return void
     */
    public function test_list()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test add
     * @return void
     */
    public function test_add()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test edit
     * @return void
     */
    public function test_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test batch
     * @return void
     */
    public function test_batch()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
