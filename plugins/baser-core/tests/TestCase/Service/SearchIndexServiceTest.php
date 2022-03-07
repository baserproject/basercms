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

use BaserCore\Model\Table\SearchIndexesTable;
use BaserCore\Service\SearchIndexService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class SearchIndexServiceTest
 * @property SearchIndexService $SearchIndexService
 * @property SearchIndexesTable $SearchIndexes
 */
class SearchIndexServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.SearchIndexes',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.ContentFolders',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SearchIndexService = new SearchIndexService();
        $this->SearchIndexes = $this->getTableLocator()->get('SearchIndexes');
        $this->Contents = $this->getTableLocator()->get('Contents');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SearchIndexService);
        unset($this->SearchIndexes);
        parent::tearDown();
    }

    /**
     * Test get
     *
     * @return void
     */
    public function testGet()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $searchIndex = $this->SearchIndexService->get(2);
    }
}
