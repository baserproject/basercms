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

use BaserCore\Service\PluginsAdminService;
use BaserCore\Service\PluginsAdminServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;

/**
 * PluginsAdminServiceTest
 */
class PluginsAdminServiceTest extends BcTestCase
{

    /**
     * Fixtures
     * @var string[]
     */
    public $fixtures = [
        'plugin.BaserCore.Plugins'
    ];

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * PluginsAdminService
     * @var PluginsAdminService
     */
    public $PluginsAdmin;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PluginsAdmin = $this->getService(PluginsAdminServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->PluginsAdmin);
    }

    /**
     * test getViewVarsForInstall
     */
    public function test_getViewVarsForInstall()
    {
        $vars = $this->PluginsAdmin->getViewVarsForInstall($this->PluginsAdmin->get(1));
        $this->assertTrue(isset($vars['plugin']));
        $this->assertTrue(isset($vars['installStatusMessage']));
    }

}
