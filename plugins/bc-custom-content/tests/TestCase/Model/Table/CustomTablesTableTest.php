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
        $association = $this->CustomTablesTable->getAssociation('CustomLinks');
        $this->assertEquals('threaded', $association->getFinder());
        $this->assertEquals('custom_table_id', $association->getForeignKey());
        $this->assertEquals(['CustomLinks.lft' => 'ASC'], $association->getSort());
    }

    /**
     * test setHasManyLinksByAll
     */
    public function test_setHasManyLinksByAll()
    {
        $this->CustomTablesTable->setHasManyLinksByAll();
        $association = $this->CustomTablesTable->getAssociation('CustomLinks');
        $this->assertEquals('all', $association->getFinder());
        $this->assertEquals('custom_table_id', $association->getForeignKey());
        $this->assertEquals(['CustomLinks.lft' => 'ASC'], $association->getSort());
    }

    /**
     * test validationDefault
     */
    public function test_validationDefault()
    {
        $validator = $this->CustomTablesTable->getValidator('default');
        //入力フィールドのデータが超えた場合、
        $errors = $validator->validate([
            'name' => str_repeat('a', 256),
            'title' => str_repeat('a', 256),
        ]);
        //戻り値を確認
        $this->assertEquals('255文字以内で入力してください。', current($errors['name']));
        $this->assertEquals('255文字以内で入力してください。', current($errors['title']));
        //入力フィールドのデータがない
        $errors = $validator->validate([
            'name' => '',
            'title' => '',
        ]);
        $this->assertEquals('識別名を入力してください。', current($errors['name']));
        $this->assertEquals('タイトルを入力してください。', current($errors['title']));
        //入力フィールドのデータが異常な記号含める
        $errors = $validator->validate([
            'name' => 'あ',
        ]);
        $this->assertEquals('識別名は半角英数字とアンダースコアのみで入力してください。', current($errors['name']));
        //正常系実行
        $errors = $validator->validate([
            'name' => 'test',
            'title' => 'test',
        ]);
        $this->assertEmpty($errors);
    }



}
