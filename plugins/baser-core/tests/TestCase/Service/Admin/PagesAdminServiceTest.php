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

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Service\Admin\PagesAdminService;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\PageFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use Cake\Core\Plugin;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PagesAdminServiceTest
 * @property PagesAdminService $PagesAdminService
 */
class PagesAdminServiceTest extends \BaserCore\TestSuite\BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PagesAdminService = new PagesAdminService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PagesAdminService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        PageFactory::make(['id' => 100, 'content' => 'test', 'page_template' => 'template 1'])->persist();
        ContentFactory::make([
            'id' => 100,
            'plugin' => 'BaserCore',
            'type' => 'Page',
            'site_id' => 1,
            'parent_id' => 3,
            'lft' => 100,
            'rght' => 101,
            'entity_id' => 100,
            'status' => true
        ])->persist();
        $rs = $this->PagesAdminService->getViewVarsForEdit($this->PagesAdminService->get(100));
        $this->assertArrayHasKey('page', $rs);
        $this->assertArrayHasKey('pageTemplateList', $rs);
        $this->assertArrayHasKey('editor', $rs);
        $this->assertArrayHasKey('editorOptions', $rs);
        $this->assertArrayHasKey('editorEnterBr', $rs);
    }

    /**
     * test getViewVarsForEdit
     * サブサイトのテーマが「メインサイトに従う」の場合、メインサイトのテーマの固定ページテンプレートを取得する
     */
    public function test_getViewVarsForEdit_followMainSiteTheme()
    {
        $this->loadPlugins(['BcThemeSample']);
        $templatePath = Plugin::templatePath('BcThemeSample') . 'Pages' . DS;
        $folder = new BcFolder($templatePath);
        $folderCreated = !is_dir($templatePath);
        $folder->create();
        $templateFile = new BcFile($templatePath . 'test_follow_main.php');
        $templateFile->write('test');

        SiteFactory::make()->main()->setField('theme', 'BcThemeSample')->persist();
        SiteFactory::make(['id' => 2, 'theme' => '', 'main_site_id' => 1])->persist();
        PageFactory::make(['id' => 100, 'content' => 'test'])->persist();
        ContentFactory::make([
            'id' => 100,
            'plugin' => 'BaserCore',
            'type' => 'Page',
            'site_id' => 2,
            'parent_id' => 3,
            'lft' => 100,
            'rght' => 101,
            'entity_id' => 100,
            'status' => true
        ])->persist();
        $rs = $this->PagesAdminService->getViewVarsForEdit($this->PagesAdminService->get(100));
        $templateFile->delete();
        if ($folderCreated) $folder->delete();

        $this->assertArrayHasKey('test_follow_main', $rs['pageTemplateList']);
    }

    /**
     * test getViewVarsForAdd
     */
    public function test_getViewVarsForAdd()
    {
        PageFactory::make(['id' => 100, 'content' => 'test', 'page_template' => 'template 1'])->persist();
        ContentFactory::make([
            'id' => 100,
            'plugin' => 'BaserCore',
            'type' => 'Page',
            'site_id' => 1,
            'parent_id' => 3,
            'lft' => 100,
            'rght' => 101,
            'entity_id' => 100,
            'status' => true
        ])->persist();
        $rs = $this->PagesAdminService->getViewVarsForAdd($this->PagesAdminService->get(100));
        $this->assertArrayHasKey('page', $rs);
        $this->assertArrayHasKey('pageTemplateList', $rs);
        $this->assertArrayHasKey('editor', $rs);
        $this->assertArrayHasKey('editorOptions', $rs);
        $this->assertArrayHasKey('editorEnterBr', $rs);
    }

}
