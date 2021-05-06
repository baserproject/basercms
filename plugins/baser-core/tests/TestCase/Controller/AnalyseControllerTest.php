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

namespace BaserCore\Test\TestCase\Controller;

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
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Controller = new AnalyseController($this->getRequest());
        $this->ref = new ReflectionClass($this->Controller);
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
     * プライベートメソッドを使用する
     * @param string $name メソッド名
     * @return ReflectionMethod $method
     */
    private function usePrivateMethod($name)
    {
        $method = $this->ref->getMethod($name);
        $method->setAccessible(true);
        return $method;
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
        $method = $this->usePrivateMethod('getList');
        $result = $method->invokeArgs($this->Controller, [$path]);
        $expected = [
            "file" => "content_folders.php",
            "path" => "/plugins/baser-core/config/Schema/content_folders.php",
            "class" => "",
            "method" => "",
            "checked" => false,
            "unitTest" => false,
            "noTodo" => false
        ];
        $this->assertContains($expected, $result);
    }

    /**
     * Test getAnnotations
     * @see BaserCore\Controller\AnalyseController @method index
     * @return void
     */
    public function testGetAnnotations()
    {
        $method = $this->usePrivateMethod('getAnnotations');
        $result = $method->invokeArgs($this->Controller, ["\BaserCore\Controller\AnalyseController", "index"]);
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
     * @see Cake\TestSuite\IntegrationTestTrait
     * @return void
     */
    public function testGetTraitMethod()
    {
        $method = $this->usePrivateMethod('getTraitMethod');
        // AnalyseControllerTestのIntegrationTestTraitでテスト
        $class = new ReflectionClass($this);
        $result = $method->invokeArgs($this->Controller, [$class]);
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
        $method = $this->usePrivateMethod('pathToClass');
        $result = $method->invokeArgs($this->Controller, [$path]);
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
