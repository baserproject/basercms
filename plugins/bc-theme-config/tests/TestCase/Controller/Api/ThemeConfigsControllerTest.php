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

namespace BcThemeConfig\Test\TestCase\Controller\Api;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcThemeConfig\Test\Scenario\ThemeConfigsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class ThemeConfigsControllerTest
 */
class ThemeConfigsControllerTest extends BcTestCase
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
        'plugin.BcThemeConfig.Factory/ThemeConfigs',
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
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test view
     */
    public function test_view()
    {
        //データを生成
        $this->loadFixtureScenario(ThemeConfigsScenario::class);
        //APIをコル
        $this->get('/baser/api/bc-theme-config/theme_configs/view.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        //全て４件を取得できるか確認
        $this->assertCount(4, get_object_vars($result->themeConfig));
        //単位Objectの値を確認
        $this->assertEquals('2B7BB9', $result->themeConfig->color_hover);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        //アップローダープラグインを追加
        $data = [
            'name_add' => 'value_add'
        ];
        //APIを呼ぶ
        $this->post("/baser/api/bc-theme-config/theme_configs/edit.json?token=" . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('テーマ設定を保存しました。', $result->message);
        $this->assertEquals('value_add', $result->themeConfig->name_add);

        //アップローダープラグインを更新
        $data = [
            'name_add' => 'value_edit'
        ];
        //APIを呼ぶ
        $this->post("/baser/api/bc-theme-config/theme_configs/edit.json?token=" . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('テーマ設定を保存しました。', $result->message);
        $this->assertEquals('value_edit', $result->themeConfig->name_add);

        //アップローダープラグインを保存する時エラーを発生
        $data = [
            'test'
        ];
        //APIを呼ぶ
        $this->post("/baser/api/bc-theme-config/theme_configs/edit.json?token=" . $this->accessToken, $data);
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
