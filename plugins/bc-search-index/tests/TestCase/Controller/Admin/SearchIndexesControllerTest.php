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

namespace BcSearchIndex\Test\TestCase\Controller\Admin;

use BaserCore\TestSuite\BcTestCase;
use BcSearchIndex\Controller\Admin\SearchIndexesController;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class SearchIndexesControllerTest
 * @package BcSearchIndex\Test\TestCase\Controller\Admin
 * @property SearchIndexesController $SearchIndexesController
 */
class SearchIndexesControllerTest extends BcTestCase
{

    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Dblogs',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Permissions',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $request = $this->getRequest('/baser/admin/bc-search-index/search_indexes/');
        $request = $this->loginAdmin($request);
        $this->SearchIndexesController = new SearchIndexesController($request);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SearchIndexesController);
        parent::tearDown();
    }

    /**
     * testBeforeRender
     */
    public function testBeforeRender()
    {
        $event = new Event('Controller.beforeRender', $this->SearchIndexesController);
        $this->SearchIndexesController->beforeRender($event);
        $this->assertEquals('BcSearchIndex.BcSearchIndex', $this->SearchIndexesController->viewBuilder()->getHelpers()[0]);
    }

}
