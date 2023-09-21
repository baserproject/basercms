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

namespace BcCustomContent\Test\TestCase\Model\Table;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Model\Table\CustomTablesTable;

/**
 * CustomTablesTableTest
 * @property CustomTablesTable $CustomTablesTable
 */
class CustomTablesTableTest extends BcTestCase
{

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomTablesTable = new CustomTablesTable();
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertTrue($this->CustomTablesTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->CustomTablesTable->hasAssociation('CustomEntries'));
        $this->assertTrue($this->CustomTablesTable->hasAssociation('CustomContents'));
        $this->assertTrue($this->CustomTablesTable->hasAssociation('CustomLinks'));
    }

    /**
     * test setHasManyLinksByThreaded
     */
    public function test_setHasManyLinksByThreaded()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setHasManyLinksByAll
     */
    public function test_setHasManyLinksByAll()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test validationDefault
     */
    public function test_validationDefault()
    {
        //準備

        //正常系実行

        //異常系実行


    }



}
