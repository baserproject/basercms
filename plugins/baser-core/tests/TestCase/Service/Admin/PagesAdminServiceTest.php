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
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PagesAdminServiceTest
 * @property PagesAdminService $PagesAdminService
 */
class PagesAdminServiceTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/Pages',
    ];

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

}
