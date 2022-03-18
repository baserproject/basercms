<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Controller\Admin;

use Cake\Event\Event;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\PreviewService;
use BaserCore\Service\PagesDisplayService;
use BaserCore\Controller\Admin\PreviewController;

/**
 * Class PreviewControllerTest
 *
 * @package Baser.Test.Case.Controller
 * @property  PreviewController $PreviewController
 */
class PreviewControllerTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.SearchIndexes',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Preview',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PreviewController = new PreviewController($this->getRequest());
        $this->PreviewService = new PagesDisplayService();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
    /**
     * beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
