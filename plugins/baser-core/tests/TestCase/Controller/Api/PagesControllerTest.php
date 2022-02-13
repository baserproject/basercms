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

use Cake\Routing\Router;
use BaserCore\Service\PageService;
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
        $this->PageService = new PageService();
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
        $this->assertRegExp('/<section class="mainHeadline">/', $result->pages[0]->contents);
    }

    /**
     * Test View
     */
    public function testView()
    {
        $this->get('/baser/api/baser-core/pages/view/2.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertRegExp('/<section class="mainHeadline">/', $result->pages->contents);
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
                "site_id" => "0",
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
        $data = $this->PageService->getIndex(['contents' => '<section class="mainHeadline">'])->first();
        $data->content->name = "pageTestUpdate";
        $data->contents = "pageTestUpdate";
        $id = $data->id;
        $this->post("/baser/api/baser-core/pages/edit/${id}.json?token=". $this->accessToken, $data->toArray());
        $this->assertResponseSuccess();
        $query = $this->PageService->getIndex(['contents' => $data->contents]);
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
            'contentId' =>4,
            'entityId' =>2,
            'parentId' =>1,
            'title' => 'hoge1',
            'siteId' =>1
        ];
        $this->post("/baser/api/baser-core/pages/copy/2.json?token=". $this->accessToken, $data);
        $this->assertResponseSuccess();
        $this->assertFalse($this->PageService->getIndex(['title' => $data['title']])->isEmpty());
    }
}
