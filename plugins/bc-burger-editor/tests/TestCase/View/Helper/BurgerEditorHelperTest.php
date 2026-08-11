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
use BcBurgerEditor\View\Helper\BurgerEditorHelper;
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
