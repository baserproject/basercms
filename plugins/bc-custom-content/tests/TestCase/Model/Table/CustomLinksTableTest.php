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
use BcCustomContent\Test\Factory\CustomLinkFactory;

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
        $validator = $this->CustomLinksTable->getValidator('default');
        //入力フィールドのデータが超えた場合、
        $errors = $validator->validate([
            'name' => str_repeat('a', 256),
            'title' => str_repeat('a', 256),
        ]);
        $this->assertEquals('255文字以内で入力してください。', current($errors['name']));
        $this->assertEquals('255文字以内で入力してください。', current($errors['title']));
        //入力フィールドのデータがない
        $errors = $validator->validate([
            'name' => '',
            'title' => '',
        ]);
        $this->assertEquals('フィールド名を入力してください。', current($errors['name']));
        $this->assertEquals('タイトルを入力してください。', current($errors['title']));
        //nameは半角英数字以外の記号が含めるケース
        $errors = $validator->validate([
            'name' => 'aこんにちは',
        ]);
        $this->assertEquals('フィールド名は半角小文字英数字とアンダースコアのみで入力してください。', current($errors['name']));
        //システム予約名称のケース
        $errors = $validator->validate([
            'name' => 'option',
        ]);
        $this->assertEquals('group, rows, option はシステム予約名称のため利用できません。', current($errors['name']));
        //既に登録のケース
        CustomLinkFactory::make([
            'name' => 'recruit_category',
        ])->persist();
        $errors = $validator->validate([
            'name' => 'recruit_category',
        ]);
        $this->assertEquals('既に登録のあるフィールド名です。', current($errors['name']));

    }

    /**
     * test implementedEvents
     */
    public function test_implementedEvents()
    {
        $result = $this->CustomLinksTable->implementedEvents();
        $this->assertArrayHasKey('Model.beforeSave', $result);
        $this->assertArrayHasKey('Model.beforeDelete', $result);
    }

    /**
     * test setTreeScope
     */
    public function test_setTreeScope()
    {
        $this->CustomLinksTable->setTreeScope(1);
        $result = $this->CustomLinksTable->getBehavior('Tree')->getConfig('scope');
        $this->assertEquals(['custom_table_id' => 1], $result);
    }

    /**
     * test beforeSave
     */
    public function test_beforeSave()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test beforeDelete
     */
    public function test_beforeDelete()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test updateSort
     */
    public function test_updateSort()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test getCurentSort
     */
    public function test_getCurentSort()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test moveOffset
     */
    public function test_moveOffset()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * test getUniqueName
     */
    public function test_getUniqueName()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }


}
