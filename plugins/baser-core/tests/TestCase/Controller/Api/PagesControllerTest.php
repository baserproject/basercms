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

namespace BaserCore\Test\TestCase\Controller\Api;

use BaserCore\Service\PagesService;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BaserCore\Controller\Api\PagesController Test Case
 */
class PagesControllerTest extends BcTestCase
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
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BcSearchIndex.SearchIndexes',
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
        $this->PagesService = new PagesService();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/api/baser-core/pages/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertMatchesRegularExpression('/<section class="mainHeadline">/', $result->pages[0]->contents);
    }

    /**
     * Test View
     */
    public function testView()
    {
        $this->get('/baser/api/baser-core/pages/view/2.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertMatchesRegularExpression('/<section class="mainHeadline">/', $result->pages->contents);
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->loginAdmin($this->getRequest());
        $data = [
            'page_template' => 'テストcreate',
            'content' => [
                "parent_id" => "1",
                "title" => "新しい フォルダー",
                "plugin" => 'BaserCore',
                "type" => "Page",
                "site_id" => "1",
                "alias_id" => "",
                "entity_id" => "",
            ]
        ];
        $this->post('/baser/api/baser-core/pages/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $Pages = $this->getTableLocator()->get('Pages');
        $query = $Pages->find()->where(['page_template' => $data['page_template']]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->delete('/baser/api/baser-core/pages/delete/2.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $data = $this->PagesService->getIndex(['contents' => '<section class="mainHeadline">'])->first();
        $data->content->name = "pageTestUpdate";
        $data->contents = "pageTestUpdate";
        $id = $data->id;
        $this->post("/baser/api/baser-core/pages/edit/${id}.json?token=". $this->accessToken, $data->toArray());
        $this->assertResponseSuccess();
        $query = $this->PagesService->getIndex(['contents' => $data->contents]);
        $this->assertEquals(1, $query->all()->count());
        $this->assertEquals("pageTestUpdate", $query->all()->first()->content->name);
    }

    /**
     * testCopy
     *
     * @return void
     */
    public function testCopy()
    {
        $data = [
            'id' =>4,
            'entity_id' =>2,
            'parent_id' =>1,
            'title' => 'hoge1',
            'site_id' =>1
        ];
        $this->post("/baser/api/baser-core/pages/copy/2.json?token=". $this->accessToken, $data);
        $this->assertResponseSuccess();
        $this->assertFalse($this->PagesService->getIndex(['title' => $data['title']])->all()->isEmpty());
    }
}
