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

namespace BcSearchIndex\Test\TestCase\Service;

use BaserCore\Model\Table\ContentsTable;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcSearchIndex\Service\SearchIndexesAdminService;
use BcSearchIndex\Test\Factory\SearchIndexFactory;

/**
 * Class SearchIndexesAdminServiceTest
 * @property SearchIndexesAdminService $SearchIndexesAdminService
 * @property ContentsTable $Contents
 */
class SearchIndexesAdminServiceTest extends BcTestCase
{
    /**
     * Trait
     */
    use BcContainerTrait;

    public $fixtures = [
        'plugin.BaserCore.Empty/Users',
        'plugin.BaserCore.Empty/Sites',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->SearchIndexesAdminService = new SearchIndexesAdminService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SearchIndexesAdminService);
        parent::tearDown();
    }

    /**
     * Test getViewVarsForIndex
     *
     * @return void
     */
    public function testGetViewVarsForIndex()
    {
        SiteFactory::make(1)->setField('status', 1)->persist();
        SearchIndexFactory::make(1)->setField('status', 1)->persist();
        $searchIndexesService = new SearchIndexesAdminService();
        $searchIndexes = $searchIndexesService->getIndex([])->all();
        $request = $this->getRequest('/')->withQueryParams([]);
        $rs = $this->SearchIndexesAdminService->getViewVarsForIndex($searchIndexes, $request);

        $this->assertTrue(isset($rs['searchIndexes']));
        $this->assertTrue(isset($rs['folders']));
        $this->assertTrue(isset($rs['sites']));
    }

}
