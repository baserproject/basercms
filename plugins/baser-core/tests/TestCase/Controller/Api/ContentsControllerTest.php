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

use BaserCore\Test\Scenario\ContentFoldersScenario;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\PagesScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use Cake\Core\Configure;
use BaserCore\Service\ContentsService;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * ContentsControllerTest
 * @property ContentsService $ContentsService
 */
class ContentsControllerTest extends \BaserCore\TestSuite\BcTestCase
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
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(ContentFoldersScenario::class);
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(PagesScenario::class);
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
        $this->ContentsService = new ContentsService();
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
     * testView
     *
     * @return void
     */
    public function testView(): void
    {
        $this->get('/baser/api/baser-core/contents/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('baserCMSサンプル', $result->content->title);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        // indexテスト
        $this->get('/baser/api/baser-core/contents.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('', $result->contents[0]->name);
        // treeテスト
        $this->get('/baser/api/baser-core/contents/index.json?site_id=1&list_type=tree&token=' . $this->accessToken);
        $this->assertResponseOk();
        // tableテスト
        $this->get('/baser/api/baser-core/contents/index.json?site_id=1&folder_id=6&name=サービス&type=Page&token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(3, count($result->contents));
    }


    /**
     * test get_prev
     */
    public function test_get_prev()
    {
        //正常系実行
        $this->get('/baser/api/baser-core/contents/get_prev/9.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(6, $result->content->id);
        //異常系実行
        $this->get('/baser/api/baser-core/contents/get_prev/99.json?token=' . $this->accessToken);
        $this->assertResponseError();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNull($result->content);
        $this->assertEquals('データが見つかりません', $result->message);
    }

    /**
     * test get_next
     */
    public function test_get_next()
    {
        //正常系実行
        $this->get('/baser/api/baser-core/contents/get_next/4.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(10, $result->content->id);
        //異常系実行
        $this->get('/baser/api/baser-core/contents/get_next/99.json?token=' . $this->accessToken);
        $this->assertResponseError();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNull($result->content);
        $this->assertEquals('データが見つかりません', $result->message);

    }

    /**
     * test get_global_navi
     */
    public function test_get_global_navi()
    {
        //正常系実行
        $this->get('/baser/api/baser-core/contents/get_global_navi/4.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(13, $result->contents);
        $this->assertEquals(1, $result->contents[0]->site_id);
        $this->assertFalse($result->contents[10]->exclude_menu);
        //異常系実行
        $this->get('/baser/api/baser-core/contents/get_global_navi/99.json?token=' . $this->accessToken);
        $this->assertResponseError();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません', $result->message);

    }

    /**
     * test get_crumbs
     */
    public function test_get_crumbs()
    {
        //正常系実行
        $this->get('/baser/api/baser-core/contents/get_crumbs/11.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(3, $result->contents);
        $this->assertEquals(1, $result->contents[0]->id);
        $this->assertEquals(6, $result->contents[1]->id);
        //異常系実行
        $this->get('/baser/api/baser-core/contents/get_crumbs/99.json?token=' . $this->accessToken);
        $this->assertResponseError();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません', $result->message);

    }

    /**
     * test get_local_navi
     */
    public function test_get_local_navi()
    {
        //正常系実行
        $this->get('/baser/api/baser-core/contents/get_local_navi/25.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(3, $result->contents);
        $this->assertEquals(24, $result->contents[0]->parent_id);
        //異常系実行
        $this->get('/baser/api/baser-core/contents/get_local_navi/99.json?token=' . $this->accessToken);
        $this->assertResponseError();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません', $result->message);

    }


}
