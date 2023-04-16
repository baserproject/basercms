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

namespace BcUploader\Test\TestCase\Controller\Api;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcUploader\Test\Factory\UploaderConfigFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class UploaderConfigsControllerTest
 */
class UploaderConfigsControllerTest extends BcTestCase
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
        'plugin.BcUploader.Factory/UploaderFiles',
        'plugin.BcUploader.Factory/UploaderCategories',
        'plugin.BcUploader.Factory/UploaderConfigs',
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
        parent::tearDown();
    }

    /**
     * test view
     * @return void
     */
    public function test_view()
    {
        //データを生成
        UploaderConfigFactory::make(['name' => 'name_1', 'value' => 'value_1'])->persist();
        UploaderConfigFactory::make(['name' => 'name_2', 'value' => 'value_2'])->persist();
        //APIを呼ぶ
        $this->get("/baser/api/bc-uploader/uploader_configs/view.json?token=" . $this->accessToken);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('value_1', $result->uploaderConfig->name_1);
        $this->assertEquals('value_2', $result->uploaderConfig->name_2);
    }

    /**
     * test edit
     * @return void
     */
    public function test_edit()
    {
        //アップローダープラグインを追加
        $data = [
            'name_add' => 'value_add'
        ];
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_configs/edit.json?token=" . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('アップローダープラグインを保存しました。', $result->message);
        $this->assertEquals('value_add', $result->uploaderConfig->name_add);

        //アップローダープラグインを更新
        $data = [
            'name_add' => 'value_edit'
        ];
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_configs/edit.json?token=" . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('アップローダープラグインを保存しました。', $result->message);
        $this->assertEquals('value_edit', $result->uploaderConfig->name_add);

        //アップローダープラグインを保存する時エラーを発生
        $data = [
            'test'
        ];
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_configs/edit.json?token=" . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseCode(500);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(
            'データベース処理中にエラーが発生しました。Cake\ORM\Entity::get(): Argument #1 ($field) must be of type string, int given, called in /var/www/html/vendor/cakephp/cakephp/src/Datasource/EntityTrait.php on line 557',
            $result->message
        );
    }
}
