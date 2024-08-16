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

use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Test\Scenario\SitesScenario;
use Cake\Core\Plugin as CakePlugin;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use Cake\View\JsonView;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use ReflectionClass;
use BaserCore\Controller\AnalyseController;

/**
 * BaserCore\Controller\AnalyseController Test Case
 */
class AnalyseControllerTest extends BcTestCase
{
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(SitesScenario::class);
        $this->Controller = new AnalyseController($this->getRequest());
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
     * Test index
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser-core/analyse/index/bc-admin-third.json');
        $this->assertResponseOk();
        $this->assertHeader('Content-Type', 'application/json');
        // 解析のため、内部的に BcCustomContent を読み込むが他のテストに影響があるため削除
        CakePlugin::getCollection()->remove('BcCustomContent');
    }

    /**
     * Test getList
     *
     * @return void
     */
    public function testGetList()
    {
        $path = ROOT . DS . 'plugins' . DS . 'baser-core';
        $result = $this->execPrivateMethod($this->Controller, 'getList', [$path]);
        $expected = [
            "file" => "paths.php",
            "path" => "/plugins/baser-core/config/paths.php",
            'type' => 'config',
            "class" => "",
            "method" => "",
            "checked" => true,
            "unitTest" => true,
            "noTodo" => true,
            "doc" => false,
            "note" => "",
        ];
        $this->assertContains($expected, $result);
    }

    /**
     * Test getAnnotations
     * @return void
     * @see BaserCore\Controller\AnalyseController @method index
     */
    public function testGetAnnotations()
    {
        $result = $this->execPrivateMethod($this->Controller, 'getAnnotations', ["\BaserCore\Controller\AnalyseController", "index"]);
        $expected = [
            "checked" => true,
            "unitTest" => true,
            "noTodo" => true
        ];
        $this->assertEquals($result, $expected);
    }

    /**
     * Test getTraitMethod
     * 指定したclassのtraitが持つメソッドを取得するかのテスト
     * @return void
     * @see Cake\TestSuite\IntegrationTestTrait
     */
    public function testGetTraitMethod()
    {
        // AnalyseControllerTestのIntegrationTestTraitでテスト
        $class = new ReflectionClass($this);
        $result = $this->execPrivateMethod($this->Controller, 'getTraitMethod', [$class]);
        $expected = "assertResponseOk";
        $this->assertContains($expected, $result);
    }

    /**
     * Test pathToClass
     * パスをクラスに変換する
     * @param $path パス
     * @param $expected 期待値
     * @return void
     * @dataProvider pathToClassDataProvider
     */
    public function testPathToClass($path, $expected)
    {
        $result = $this->execPrivateMethod($this->Controller, 'pathToClass', [$path]);
        $this->assertEquals($result, $expected);
    }

    public static function pathToClassDataProvider()
    {
        return [
            // rootを取り除く
            [ROOT . DS . "plugins", ""],
            // kebab-case→PascalCase
            ["baser-core", "BaserCore"],
            ["bc-admin-third", "BcAdminThird"],
            ["bc-blog", "BcBlog"],
            ["bc-mail", "BcMail"],
            ["bc-uploader", "BcUploader"],
            // スラッシュ→バックスラッシュ
            ["test/", "test\\"],
            // .php削除
            ["test.php", "test"],
            // src 削除
            ['/src', ''],
            // tests→Test
            ['/tests', '\Test'],
            // 全体
            [ROOT . DS . "plugins/baser-core/src/tests/TestSample/test.php", "\BaserCore\Test\TestSample\\test"],
        ];
    }

    /**
     * Test viewClasses
     *
     */
    public function testViewClasses()
    {
        $result = $this->Controller->viewClasses();
        $this->assertIsArray($result);

        $this->assertEquals([JsonView::class], $result);
    }
}
