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

use BaserCore\Service\DblogsService;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\DblogsScenario;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class DblogsControllerTest extends BcTestCase
{
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
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(DblogsScenario::class);
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
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/api/admin/baser-core/dblogs/index/1.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());

        $dblogService = new DblogsService();
        $dblogs = $dblogService->getIndex();
        $this->assertEquals($dblogs->count(), count($result->Dblogs));

        $this->get('/baser/api/admin/baser-core/dblogs/index/1.json?user_id=2&token=' . $this->accessToken);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(1, count($result->Dblogs));
        $this->assertEquals(2, $result->Dblogs[0]->user_id);
    }

    /**
     * test add
     * @return void
     */
    public function testAdd()
    {
        $this->get('/baser/api/admin/baser-core/dblogs/add.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $data = [
            'message' => null,
            'title' => 'ucmitzグループ',
            'use_move_contents' => '1',
        ];
        $this->post('/baser/api/admin/baser-core/dblogs/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertTrue(isset($result->errors->message));

        $data = [
            'message' => "message test",
            'controller' => "controller test",
            'action' => "add test"
        ];
        $this->post('/baser/api/admin/baser-core/dblogs/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();

        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ログを追加しました。', $result->message);
        $this->assertTrue(isset($result->dblog));
        $this->assertNull($result->errors);

        $dbLogs = $this->getTableLocator()->get('Dblogs');
        $query = $dbLogs->find()->where(['message' => $data['message']]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test delete_all
     */
    public function testDelete_all()
    {
        $this->get('/baser/api/admin/baser-core/dblogs/delete_all/dblogs.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $this->post('/baser/api/admin/baser-core/dblogs/delete_all/dblogs.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('最近の動きのログを削除しました。', $result->message);
    }
}
