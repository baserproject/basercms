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

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcHtmlHelper;

/**
 * Class BcHtmlHelperTest
 *
 * @property BcHtmlHelper $BcHtml
 */
class BcHtmlHelperTest extends BcTestCase
{

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcHtml = new BcHtmlHelper(new BcAdminAppView($this->getRequest('/')));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BcHtml);
        parent::tearDown();
    }

    /**
     * タグにラッピングされていないパンくずデータを取得する
     */
    public function testGetStripCrumbs()
    {
        // TODO 暫定措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<

        $expected = 'abc';
        $this->BcHtml->_crumbs = [$expected];
        $crumbs = $this->BcHtml->getCrumbs();
        $this->assertEquals('<a href="/b" c>a</a>', $crumbs);
        $this->assertEquals([$expected], $this->BcHtml->getStripCrumbs());
    }

    /**
     * testSetScript
     * @param string $variable 変数名（グローバル変数）
     * @param string $value 値
     * @param array $options
     *  - `block` : ビューブロックに出力するかどうか。（初期値 : false）
     *  - `declaration` : var 宣言を行うかどうか（初期値 : true）
     * @dataProvider setScriptDataProvider
     */
    public function testSetScript($value, $block, $declaration, $expected)
    {
        $result = $this->BcHtml->setScript('test', $value, [
            'block' => $block,
            'declaration' => $declaration
        ]);
        $this->assertEquals($expected, $result);
    }

    public function setScriptDataProvider()
    {
        return [
            ['</script>', false, true, '<script>var test = "<\/script>";</script>'],
            ['abc', true, true, ''],
            ['abc', false, false, '<script>test = "abc";</script>'],
        ];
    }

    /**
     * testDeclarationI18n
     */
    public function testDeclarationI18n()
    {
        $result = $this->BcHtml->declarationI18n();
        $this->assertEquals('<script>var bcI18n = [];</script>', $result);
    }

    public function testI18nScript()
    {
        $result = $this->BcHtml->i18nScript(['a' => 'b', 'c' => 'd'], ['block' => false]);
        $this->assertEquals("<script>bcI18n.a = \"b\";</script>\n<script>bcI18n.c = \"d\";</script>\n", $result);
    }

}
