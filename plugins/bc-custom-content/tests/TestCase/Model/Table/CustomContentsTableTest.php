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
use BcCustomContent\Model\Table\CustomContentsTable;

/**
 * CustomContentsTableTest
 * @property CustomContentsTable $CustomContentsTable
 *
 */
class CustomContentsTableTest extends BcTestCase
{

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomContentsTable = $this->getTableLocator()->get('BcCustomContent.CustomContentsTable');

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
        $this->assertTrue($this->CustomContentsTable->hasBehavior('BcContents'));
        $this->assertTrue($this->CustomContentsTable->hasBehavior('Timestamp'));
    }

    /**
     * test validationWithTable
     */
    public function test_validationWithTable()
    {
        $validator = $this->CustomContentsTable->getValidator('withTable');
        $errors = $validator->validate([
            'list_count' => '',
        ]);
        $this->assertArrayHasKey('list_count', $errors);
        $this->assertEquals('一覧表示件数は必須項目です。', current($errors['list_count']));
    }


}
