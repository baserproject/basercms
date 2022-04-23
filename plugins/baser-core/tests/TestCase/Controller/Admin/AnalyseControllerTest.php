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

use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use ReflectionClass;
use BaserCore\Controller\AnalyseController;

/**
 * BaserCore\Controller\AnalyseController Test Case
 */
class AnalyseControllerTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs'
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
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
        $this->get('/baser/baser-core/analyse/index/bc-admin-third.json');
        $this->assertResponseOk();
        $this->assertHeader('Content-Type', 'application/json');
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
            "class" => "",
            "method" => "",
            "checked" => true,
            "unitTest" => true,
            "noTodo" => true,
            "doc" => false,
            "note" => "",
            'type' => 'config'
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

    public function pathToClassDataProvider()
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
}
