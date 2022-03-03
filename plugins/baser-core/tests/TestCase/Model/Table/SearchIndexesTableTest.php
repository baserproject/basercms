<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\SearchIndexesTable;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class SearchIndexesTableTest
 * @package BaserCore\Test\TestCase\Model\Table
 * @property SearchIndexesTable $SearchIndexes
 */
class SearchIndexesTableTest extends BcTestCase
{

    /**
     * @var SearchIndexesTable
     */
    public $SearchIndexes;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.SearchIndexes',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SearchIndexes = $this->getTableLocator()->get('BaserCore.SearchIndexes');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SearchIndexes);
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * testReconstruct
     *
     * @return void
     */
    public function testReconstruct()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * testAllowPublish
     *
     * @return void
     */
    public function testAllowPublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
