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

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\View\BcAdminAppView;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\Helper\BcCkeditorHelper;

/**
 * text helper library.
 *
 * @package Baser.Test.Case.View.Helper
 * @property BcCkeditorHelper $BcCkeditor
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
        'plugin.BaserCore.Contents',
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
        $this->BcCkeditor = new BcCkeditorHelper(new BcAdminAppView($this->getRequest('/baser/admin')));
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
        $this->BcCkeditor->getView()->setTheme('BcAdminThird');
        $fieldName = 'Page.test';
        $result = $this->BcCkeditor->editor($fieldName, []);
        $tagList = ['/<span class="bca-textarea"/', '/<textarea name="Page\[test\]"/'];
        foreach ($tagList as $requiredTag) {
            $this->assertMatchesRegularExpression($requiredTag, $result);
        }
    }

    /**
     * testBuildTmpScript
     *
     * @return void
     */
    public function testBuildTmpScript()
    {
        $this->BcCkeditor->getView()->setTheme('BcAdminThird');
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
        $request = $this->BcCkeditor->getView()->getRequest()->withAttribute('formTokenData', ['dummy']);
        $this->BcCkeditor->getView()->setRequest($request);
        $this->BcCkeditor->BcAdminForm->create();
        $result = $this->execPrivateMethod($this->BcCkeditor, 'buildTmpScript', ["Page.contents_tmp", $options]);
        $this->assertMatchesRegularExpression('/<script src="\/bc_admin_third\/js\/ckeditor\.bundle.js"/', $result);
        $jsResult = $this->BcCkeditor->getView()->fetch('script');
        // ckeditor.bundle.jsがタグに含められてるか確認
        $this->assertMatchesRegularExpression('/<script src="\/bc_admin_third\/js\/vendor\/ckeditor\/ckeditor\.js"/', $jsResult);
    }

    /**
     * Test setEditorToolbar
     */
    public function testSetEditorToolbar()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test setDraft
     */
    public function testSetDraft()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test getThemeEditorCsses
     */
    public function testGetThemeEditorCsses()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
