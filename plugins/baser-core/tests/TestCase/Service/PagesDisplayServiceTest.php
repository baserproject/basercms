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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\PagesDisplayService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class PagesDisplayServiceTest
 * @property PagesDisplayService $PagesDisplayService
 */
class PagesDisplayServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.SearchIndexes',
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PagesDisplayService = new PagesDisplayService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PagesDisplayService);
        parent::tearDown();
    }

    /**
     * Test getPreviewData
     *
     * @return void
     */
    public function testGetPreviewData()
    {
        $request = $this->getRequest('/baser/admin');
        $pagesDisplay = $this->PagesDisplayService->getPreviewData($request);
        $this->assertEquals([], $pagesDisplay);
    }
}
