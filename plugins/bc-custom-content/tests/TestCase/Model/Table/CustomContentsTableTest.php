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
 */
class CustomContentsTableTest extends BcTestCase
{

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomContentsTable = $this->getTableLocator()->get('BcCustomContent.CustomContents');
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomContentsTable);
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertTrue($this->CustomContentsTable->hasBehavior('BcContents'));
        $this->assertTrue($this->CustomContentsTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->CustomContentsTable->hasBehavior('BcContents'));
        $this->assertTrue($this->CustomContentsTable->hasAssociation('CustomTables'));
    }

    /**
     * test validationWithTable
     */
    public function test_validationWithTable()
    {
        //全角文字を入力した場合
        $validator = $this->CustomContentsTable->getValidator('withTable');
        $errors = $validator->validate([
            'list_count' => '漢字'
        ]);
        $this->assertEquals([
            'range' => '一覧表示件数は100までの数値で入力してください。',
            'halfText' => '一覧表示件数は半角で入力してください。'
        ], $errors['list_count']);

        //101を入力した場合
        $validator = $this->CustomContentsTable->getValidator('withTable');
        $errors = $validator->validate([
            'list_count' => '101'
        ]);
        $this->assertEquals([
            'range' => '一覧表示件数は100までの数値で入力してください。',
        ], $errors['list_count']);

        //何も入力しない場合
        $validator = $this->CustomContentsTable->getValidator('withTable');
        $errors = $validator->validate([
            'list_count' => ''
        ]);
        $this->assertEquals([
            '_empty' => '一覧表示件数は必須項目です。',
        ], $errors['list_count']);
    }
}
