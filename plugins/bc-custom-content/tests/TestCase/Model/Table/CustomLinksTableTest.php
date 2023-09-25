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
use BcCustomContent\Model\Table\CustomLinksTable;

/**
 * CustomTablesTableTest
 * @property CustomLinksTable $CustomLinksTable
 */
class CustomLinksTableTest extends BcTestCase
{

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomLinksTable = new CustomLinksTable();
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
        $this->assertTrue($this->CustomLinksTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->CustomLinksTable->hasBehavior('Tree'));
        $this->assertTrue($this->CustomLinksTable->hasAssociation('CustomFields'));
        $this->assertTrue($this->CustomLinksTable->hasAssociation('CustomTables'));
    }

    /**
     * test validationDefault
     */
    public function test_validationDefault()
    {

    }

    /**
     * test implementedEvents
     */
    public function test_implementedEvents()
    {

    }

    /**
     * test setTreeScope
     */
    public function test_setTreeScope()
    {

    }

    /**
     * test beforeSave
     */
    public function test_beforeSave()
    {

    }

    /**
     * test beforeDelete
     */
    public function test_beforeDelete()
    {

    }

    /**
     * test updateSort
     */
    public function test_updateSort()
    {

    }

    /**
     * test getCurentSort
     */
    public function test_getCurentSort()
    {

    }

    /**
     * test moveOffset
     */
    public function test_moveOffset()
    {

    }

    /**
     * test getUniqueName
     */
    public function test_getUniqueName()
    {

    }


}
