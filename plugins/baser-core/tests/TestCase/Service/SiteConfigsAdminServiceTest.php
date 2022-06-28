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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\SiteConfigsAdminService;
use BaserCore\Service\SiteConfigsAdminServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;

/**
 * SiteConfigsAdminServiceTest
 */
class SiteConfigsAdminServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * SiteConfigsAdmin
     * @var SiteConfigsAdminService
     */
    public $SiteConfigsAdmin;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SiteConfigsAdmin = $this->getService(SiteConfigsAdminServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->SiteConfigsAdmin);
    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        $vars = $this->SiteConfigsAdmin->getViewVarsForIndex($this->SiteConfigsAdmin->get());
        $this->assertTrue(isset($vars['siteConfig']));
        $this->assertTrue(isset($vars['isWritableEnv']));
        $this->assertTrue(isset($vars['modeList']));
        $this->assertTrue(isset($vars['widgetAreaList']));
        $this->assertTrue(isset($vars['adminThemeList']));
        $this->assertTrue(isset($vars['editorList']));
        $this->assertTrue(isset($vars['mailEncodeList']));
    }

}
