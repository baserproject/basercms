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

namespace BcBlog\Test\TestCase\Controller\Api\Admin;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SmallSetContentsScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\Api\BlogContentsController;
use BcBlog\Test\Factory\BlogContentFactory;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogContentsControllerTest
 * @property BlogContentsController $BlogContentsController
 */
class BlogContentsControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

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
        parent::tearDown();
    }

    /**
     * test index
     */
    public function test_index()
    {
        //準備
        BlogContentFactory::make(['id' => 1, 'description' => 'description test 1'])->persist();
        BlogContentFactory::make(['id' => 2, 'description' => 'description test 2'])->persist();

        ContentFactory::make(['id' => 100, 'type' => 'BlogContent', 'entity_id' => 1, 'alias_id' => NULL, 'title' => 'title 1',])->persist();
        ContentFactory::make(['id' => 200, 'type' => 'BlogContent', 'entity_id' => 2, 'alias_id' => NULL, 'title' => 'title 2',])->persist();
        //正常系実行
        $this->get('/baser/api/admin/bc-blog/blog_contents/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->blogContents);
        $this->assertEquals('description test 1', $result->blogContents[0]->description);
        $this->assertEquals('description test 2', $result->blogContents[1]->description);
    }


    /**
     * test list
     */
    public function test_list()
    {
        BlogContentFactory::make(['id' => 13, 'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。'])->persist();
        BlogContentFactory::make(['id' => 14, 'description' => 'ディスクリプション'])->persist();

        ContentFactory::make(['id' => 13, 'type' => 'BlogContent', 'entity_id' => 13, 'alias_id' => NULL, 'title' => 'baserCMS inc',])->persist();
        ContentFactory::make(['id' => 14, 'type' => 'BlogContent', 'entity_id' => 14, 'alias_id' => NULL, 'title' => 'ディスクリプション タイトル',])->persist();

        $this->get('/baser/api/admin/bc-blog/blog_contents/list.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('baserCMS inc', $result->blogContents->{13});
        $this->assertEquals('ディスクリプション タイトル', $result->blogContents->{14});
    }

    /**
     * test add
     */
    public function test_add()
    {
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $data = [
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
            'content' => [
                "title" => "新しい ブログ",
                'site_id' => 1,
                'parent_id' => 1
            ]
        ];
        $this->post('/baser/api/admin/bc-blog/blog_contents/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ブログ「新しい ブログ」を追加しました。', $result->message);
        $this->assertEquals('baserCMS inc. [デモ] の最新の情報をお届けします。', $result->blogContent->description);

        $data = [
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
        ];
        $this->post('/baser/api/admin/bc-blog/blog_contents/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('関連するコンテンツがありません', $result->errors->content->_required);
    }

    /**
     * test view
     */
    public function test_view()
    {
        //準備
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 99])->persist();
        BlogContentFactory::make(['id' => 99, 'description' => 'Nghiem'])->persist();
        //正常系実行
        $this->get('/baser/api/admin/bc-blog/blog_contents/view/99.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(99, $result->blogContent->id);
        //異常系実行
        $this->get('/baser/api/admin/bc-blog/blog_contents/view/999.json?token=' . $this->accessToken);
        $this->assertResponseCode(404);
    }


    /**
     * test edit
     */
    public function test_edit()
    {
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 100, 'lft' => 1, 'rght' => 2])->persist();
        BlogContentFactory::make(['id' => 100, 'description' => '新しい'])->persist();
        //実行成功
        $data = [
            'id' => 100,
            'description' => '更新した!',
            'content' => [
                "title" => "更新 ブログ",
            ]
        ];
        $this->post('/baser/api/admin/bc-blog/blog_contents/edit/100.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ブログ「更新 ブログ」を更新しました。', $result->message);
        $this->assertEquals('更新した!', $result->blogContent->description);
        //実行失敗
        $data = ['id' => 100, 'description' => '更新した!'];
        $this->post('/baser/api/admin/bc-blog/blog_contents/edit/100.json?token=' . $this->accessToken, $data);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('関連するコンテンツがありません', $result->errors->content->_required);
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        ContentFactory::make([
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'entity_id' => 101,
            'lft' => 1,
            'rght' => 2
        ])->persist();
        BlogContentFactory::make(['id' => 101, 'description' => 'abc'])->persist();

        $this->post('/baser/api/admin/bc-blog/blog_contents/delete/101.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ブログコンテンツ「abc」を削除しました。', $result->message);
    }

    /**
     * test copy
     * @return void
     */
    public function test_copy()
    {
        BlogContentFactory::make([
            'id' => 2,
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
            'template' => 'default',
            'list_count' => '10',
            'list_direction' => 'DESC',
            'feed_count' => '10',
            'tag_use' => '1',
            'comment_use' => '1',
            'comment_approve' => '0',
            'widget_area' => '2',
            'eye_catch_size' => '',
            'use_content' => '1'
        ])->persist();
        ContentFactory::make([
            'id' => 1,
            'title' => '',
            'plugin' => 'BaserCore',
            'type' => 'ContentFolder',
            'entity_id' => 1,
            'url' => '/',
            'site_id' => 1,
            'alias_id' => null,
            'main_site_content_id' => null,
            'parent_id' => null,
            'lft' => 1,
            'rght' => 4,
            'level' => 1
        ])->persist();
        ContentFactory::make([
            'id' => 2,
            'title' => 'news',
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'entity_id' => 2,
            'url' => '/test',
            'site_id' => 1,
            'alias_id' => null,
            'main_site_content_id' => null,
            'parent_id' => null,
            'lft' => 1,
            'rght' => 2,
            'level' => 1,

        ])->persist();
        SiteConfigFactory::make([
            'name' => 'contents_sort_last_modified',
            'value' => ''
        ])->persist();
        $data = [
            'entity_id' => 2,
            'parent_id' => 2,
            'site_id' => 1,
            'title' => 'news',
        ];
        $this->post('/baser/api/admin/bc-blog/blog_contents/copy.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ブログのコピー「news」を追加しました。', $result->message);
        $this->assertEquals('baserCMS inc. [デモ] の最新の情報をお届けします。', $result->blogContent->description);

        BlogContentFactory::make([
            'id' => 10,
        ])->persist();
        ContentFactory::make([
            'id' => 10,
            'title' => 'news',
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'entity_id' => 10,
            'url' => '/test',
            'site_id' => 1,
            'alias_id' => null,
            'main_site_content_id' => null,
            'parent_id' => null,
            'lft' => 3,
            'rght' => 4,
            'level' => 1,

        ])->persist();

        $data = [
            'entity_id' => 10,
            'parent_id' => 1,
            'site_id' => 1,
            'title' => 'news',
        ];
        $this->post('/baser/api/admin/bc-blog/blog_contents/copy.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ブログのコピー「news」を追加しました。', $result->message);
    }

}
