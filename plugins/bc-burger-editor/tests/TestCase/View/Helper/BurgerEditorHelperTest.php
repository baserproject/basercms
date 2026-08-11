<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBurgerEditor\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcBurgerEditor\Lib\BurgerEditorUtil;
use BcBurgerEditor\View\Helper\BurgerEditorHelper;
use Cake\Routing\Router;
use Cake\View\View;

/**
 * BurgerEditorHelperTest
 */
class BurgerEditorHelperTest extends BcTestCase
{

    /**
     * @var BurgerEditorHelper
     */
    public $BurgerEditor;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        // 設定内の API URL 生成にプラグインのルートが必要となるため読み込む
        $this->loadPlugins(['BcBurgerEditor' => ['routes' => true]]);
        // テーマ判定がリクエストを参照するためセットする
        Router::setRequest($this->getRequest('/'));
        $this->BurgerEditor = new BurgerEditorHelper(new View());
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BurgerEditor);
        parent::tearDown();
    }

    /**
     * test getBgeConfig
     *
     * エディタへ渡す設定が組み立てられる
     */
    public function test_getBgeConfig()
    {
        $result = $this->BurgerEditor->getBgeConfig();

        $this->assertArrayHasKey('api', $result);
        $this->assertStringContainsString('img_list', $result['api']['imgList']);
        $this->assertStringContainsString('file_upload', $result['api']['fileUpload']);
        $this->assertArrayHasKey('utility', $result);
        $this->assertArrayHasKey('types', $result);
        // Addon のタイプが列挙される
        $this->assertArrayHasKey('image', $result['types']);
        $this->assertArrayHasKey('version', $result['types']['image']);
    }

    /**
     * test getBgeConfig
     *
     * 状態がインスタンスごとに独立している
     */
    public function test_getBgeConfig_isNotShared()
    {
        ob_start();
        $this->BurgerEditor->type('image');
        ob_end_clean();

        $other = new BurgerEditorHelper(new View());

        // 別インスタンスで読み込んだタイプは引き継がれない
        $this->assertSame(['image'], $this->getPrivateProperty($this->BurgerEditor, 'useType'));
        $this->assertSame([], $this->getPrivateProperty($other, 'useType'));
    }

    /**
     * test type
     *
     * タイプが出力され、利用済みとして記録される
     */
    public function test_type()
    {
        ob_start();
        $this->BurgerEditor->type('image');
        $result = ob_get_clean();

        $this->assertStringContainsString('data-bgt="image"', $result);
        $this->assertStringContainsString('bgt-image-container', $result);

        // 記録されたタイプが初期化処理の出力対象となる
        ob_start();
        $this->BurgerEditor->initArea();
        $initResult = ob_get_clean();
        $this->assertStringContainsString('Initimage', $initResult);
    }

    /**
     * test type
     *
     * 存在しないタイプは false を返す
     */
    public function test_type_withMissingType()
    {
        ob_start();
        $result = $this->BurgerEditor->type('not-exists-type');
        ob_end_clean();
        $this->assertFalse($result);
    }

    /**
     * test inputArea
     *
     * 読み込んだタイプの入力欄が出力される
     */
    public function test_inputArea()
    {
        ob_start();
        $this->BurgerEditor->type('image');
        ob_end_clean();

        ob_start();
        $this->BurgerEditor->inputArea();
        $result = ob_get_clean();

        $this->assertStringContainsString('Typeimage', $result);
    }

    /**
     * test defaultBlock
     *
     * ブロックが出力され、利用済みとして記録される
     */
    public function test_defaultBlock()
    {
        $blockPath = BurgerEditorUtil::getBlockPath('image2');

        ob_start();
        $this->BurgerEditor->defaultBlock([$blockPath]);
        $result = ob_get_clean();

        $this->assertStringContainsString('data-bgb="image2"', $result);
        $this->assertStringContainsString('class="bgb-image2"', $result);
        // ブロックが読み込んだタイプも記録される
        $this->assertStringContainsString('data-bgt="image"', $result);
    }

    /**
     * test panelArea
     *
     * 読み込んだブロックがパネルとして出力される
     */
    public function test_panelArea()
    {
        ob_start();
        $this->BurgerEditor->defaultBlock([BurgerEditorUtil::getBlockPath('image2')]);
        ob_end_clean();

        ob_start();
        $this->BurgerEditor->panelArea();
        $result = ob_get_clean();

        $this->assertStringContainsString('bg-block-selection', $result);
        $this->assertStringContainsString('data-bge-block="image2"', $result);
        // panel.svg はインラインで埋め込まれる
        $this->assertStringContainsString('<svg', $result);
    }

    /**
     * test panelArea
     *
     * 読み込んでいないブロックは出力されない
     */
    public function test_panelArea_withoutBlock()
    {
        ob_start();
        $this->BurgerEditor->type('image');
        ob_end_clean();

        ob_start();
        $this->BurgerEditor->panelArea();
        $result = ob_get_clean();

        $this->assertStringContainsString('bg-block-selection', $result);
        $this->assertStringNotContainsString('data-bge-block=', $result);
    }

    /**
     * test shouldLoadStyle
     *
     * 初期状態では読み込む
     */
    public function test_shouldLoadStyle_default()
    {
        $this->assertTrue($this->BurgerEditor->shouldLoadStyle());
    }

    /**
     * test preventLoadingStyle
     *
     * 呼び出すと読み込まなくなる
     */
    public function test_preventLoadingStyle()
    {
        $this->BurgerEditor->preventLoadingStyle();
        $this->assertFalse($this->BurgerEditor->shouldLoadStyle());
    }

}
