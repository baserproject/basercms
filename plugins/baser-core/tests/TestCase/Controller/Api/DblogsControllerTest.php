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

use BaserCore\Service\DblogsService;
use BaserCore\TestSuite\BcTestCase;
use Cake\Http\ServerRequest;
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
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
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
     * Set Up
     *
     * @return void
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
        parent::tearDown();
    }

    /**
     * test index
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/api/baser-core/dblogs/index/1.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());

        $dblogService = new DblogsService();
        $dblogs = $dblogService->getIndex();
        $this->assertEquals($dblogs->count(), count($result->Dblogs));

        $this->get('/baser/api/baser-core/dblogs/index/1.json?user_id=2&token=' . $this->accessToken);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(1, count($result->Dblogs));
        $this->assertEquals(2, $result->Dblogs[0]->user_id);
    }

    /**
     * Test delete_all
     */
    public function testDelete_all()
    {
        $this->get('/baser/api/baser-core/dblogs/delete_all/dblogs.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $this->post('/baser/api/baser-core/dblogs/delete_all/dblogs.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('最近の動きのログを削除しました。', $result->message);

        $this->post('/baser/api/baser-core/dblogs/delete_all/test.json?token=' . $this->accessToken);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('最近の動きのログ削除に失敗しました。', $result->message);
    }
}
