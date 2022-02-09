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

use Cake\Routing\Router;
use BaserCore\View\BcAdminAppView;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\Helper\BcCkeditorHelper;

/**
 * text helper library.
 *
 * @package Baser.Test.Case.View.Helper
 * @property BcCkeditor $BcCkeditor
 */
class BcCkeditorHelperTest extends BcTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
    ];
    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcCkeditor = new BcCkeditorHelper(new BcAdminAppView($this->getRequest('/')));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BcCkeditor);
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertnotEmpty($this->BcCkeditor->style);
    }
    /**
     * CKEditorのテキストエリアを出力する
     *
     * @param string $fieldName エディタのid, nameなどの名前を指定
     * @param array $options
     * @param boolean $expected 期待値
     */
    public function testEditor()
    {
        $fieldName = 'Page.test';
        $result = $this->BcCkeditor->editor($fieldName, []);
        $tagList = ['/<span class="bca-textarea"/', '/<textarea name="Page\[test\]"/', '/<input type="hidden" id="DraftModeTest"/'];
        foreach ($tagList as $requiredTag) {
            $this->assertRegExp($requiredTag, $result);
        }
    }

    /**
     * testBuildTmpScript
     *
     * @return void
     */
    public function testBuildTmpScript()
    {
        $options = [
            'editor' => 'BcCkeditor',
            'style' => 'width:99%;height:540px',
            'editorUseDraft' => true,
            'editorDraftField' => 'draft',
            'editorWidth' => 'auto',
            'editorHeight' => '480px',
            'editorEnterBr' => '0',
            'editorDisableDraft' => false,
            'editorStylesSet' => 'default',
            'editorStyles' => [
                'default' => [
                    0 => [
                        'name' => '青見出し(h3)',
                        'element' => 'h3',
                        'styles' => ['color' => 'Blue',],
                        ],
                    ],
                ],
            'type' => 'textarea',
            ];
        $result = $this->execPrivateMethod($this->BcCkeditor, 'buildTmpScript', ["Page.contents_tmp", $options]);
        $jsResult = $this->BcCkeditor->getView()->fetch('script');
        $this->assertRegExp('/<input type="hidden" id="DraftModeContentsTmpTmp" value="publish">/', $result);
        // ckeditor.jsがタグに含められてるか確認
        $this->assertRegExp('/<script src="js\/vendor\/ckeditor\/ckeditor.js">/', $jsResult);
        // applyCkeditor.bundleがタグに含められてるか確認
        $this->assertRegExp('/<script src="js\/admin\/pages\/applyCkeditor.bundle.js">/', $jsResult);
        // javascript側で使用する変数が埋め込まれてるかをテスト
        $varList = [
            "ckeditorField",
            "editorStylesSet",
            "editorEnterBr",
            "editorDomId",
            "editorUseDraft",
            "draftAreaId",
            "publishAreaId",
            "editorReadonlyPublish",
            "editorDisableDraft",
            "editorDisablePublish",
            "fieldCamelize",
            "initialStyle",
            "editorStyle",
            "themeEditorCsses",
            "editorOptions"
        ];
        foreach ($varList as $var) {
            $this->assertEquals(1, preg_match("/var\s$var/",$jsResult));
        }
    }

}
