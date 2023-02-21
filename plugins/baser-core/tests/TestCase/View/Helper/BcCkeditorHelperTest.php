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
use Cake\Core\Plugin;
use Cake\Core\PluginCollection;

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
        'plugin.BaserCore.Plugins',
    ];
    /**
     * setUp
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
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
        $request = $this->getRequest('/')->withAttribute('formTokenData', ['test']);
        $this->BcCkeditor->getView()->setRequest($request);
        $this->BcCkeditor->BcAdminForm->create();
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
    public function testBuild()
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
        $result = $this->execPrivateMethod($this->BcCkeditor, 'build', ["Page.contents", $options]);
        $this->assertMatchesRegularExpression('/<input type="hidden" name="ContentPreviewMode"/', $result);
        $jsResult = $this->BcCkeditor->getView()->fetch('script');
        $this->assertMatchesRegularExpression('/let config = JSON\.parse\(\'\{"ckeditorField/', $jsResult);
    }

    /**
     * Test setEditorToolbar
     */
    public function testSetEditorToolbar()
    {
        $options = $this->BcCkeditor->setEditorToolbar(['editorToolbar' => [['Bold']]]);
        $this->assertEquals(['editorToolbar' => [['Bold']]], $options);
        $options = $this->BcCkeditor->setEditorToolbar([
            'editorToolbar' => [['Bold']],
            'editorUseTemplates' => false,
            'editorToolType' => 'simple'
        ]);
        $this->assertIsArray($options['editorToolbar']);
        (new \BcEditorTemplate\Plugin())->install(['connection' => 'test']);
        $options = $this->BcCkeditor->setEditorToolbar([
            'editorToolbar' => [],
            'editorUseTemplates' => true,
            'editorToolType' => 'simple'
        ]);
        $this->assertContains('Templates', $options['editorToolbar'][0]);
        $options = $this->BcCkeditor->setEditorToolbar([
            'editorToolbar' => [],
            'editorUseTemplates' => true,
            'editorToolType' => 'normal'
        ]);
        $this->assertContains('Templates', $options['editorToolbar'][1]);
    }

    /**
     * Test setDraft
     */
    public function testSetDraft()
    {
        $request = $this->BcCkeditor->getView()->getRequest()->withAttribute('formTokenData', ['dummy']);
        $this->BcCkeditor->getView()->setRequest($request);
        $this->BcCkeditor->BcAdminForm->create();

        $options = [
            'editorToolbar' => [['Bold']],
            'editorDisableCopyDraft' => ['test draft'],
            'editorDisableCopyPublish' => ['test publish'],
            'editorDraftField' => 'test',
        ];
        $result = $this->BcCkeditor->setDraft('contents', $options);
        $this->assertCount(5, $result['editorToolbar'][0]);

        $options = [
            'editorToolbar' => [['Bold']],
            'editorDisableCopyDraft' => [],
            'editorDisableCopyPublish' => [],
            'editorDraftField' => 'test',
        ];
        $result = $this->BcCkeditor->setDraft('contents', $options);
        $this->assertContains('CopyDraft', $result['editorToolbar'][0]);
        $this->assertContains('CopyPublish', $result['editorToolbar'][0]);
        $this->assertEquals('Contents', $result['publishAreaId']);
        $this->assertEquals('Test', $result['draftAreaId']);

        $result = $this->BcCkeditor->setDraft('page.contents', $options);
        $this->assertEquals('PageContents', $result['publishAreaId']);
        $this->assertEquals('PageTest', $result['draftAreaId']);
    }

    /**
     * Test getThemeEditorCsses
     */
    public function testGetThemeEditorCsses()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
