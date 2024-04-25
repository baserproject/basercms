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

use BaserCore\Service\BcDatabaseServiceInterface;
use ArrayObject;
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Model\Table\CustomLinksTable;
use BcCustomContent\Service\CustomLinksServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcThemeFile\Model\Entity\ThemeFile;
use Cake\Event\Event;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomTablesTableTest
 * @property CustomLinksTable $CustomLinksTable
 */
class CustomLinksTableTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;

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
        $this->assertStringContainsString( 'システム予約名称のため利用できません。', current($errors['name']));
        //既に登録のケース
        CustomLinkFactory::make([
            'name' => 'recruit_category',
            'custom_table_id' => 1,
        ])->persist();
        $errors = $validator->validate([
            'name' => 'recruit_category',
            'custom_table_id' => 1,
        ]);
        $this->assertEquals('既に登録のあるフィールド名です。', current($errors['name']));

        //スペースだけ登録した場合、
        $errors = $validator->validate([
            'title' => '      '
        ]);
        $this->assertEquals('タイトルを入力してください。', current($errors['title']));
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
        //データを生成
        CustomLinkFactory::make([
            'custom_table_id' => 1,
            'name' => 'recruit_category'
        ])->persist();
        $entity = CustomLinkFactory::make([
            'custom_table_id' => 1,
            'name' => 'recruit_category'
        ])->getEntity();
        $this->CustomLinksTable->dispatchEvent('Model.beforeSave',
            ['entity' => $entity, 'options' => new \ArrayObject()]);
        //check unique name
        $this->assertEquals('recruit_category_2', $entity->get('name'));
        //check tree scope
        $treeScope = $this->CustomLinksTable->getBehavior('Tree')->getConfig('scope');
        $this->assertEquals(['custom_table_id' => 1], $treeScope);
    }

    /**
     * test beforeDelete
     */
    public function test_beforeDelete()
    {
        //データを生成
        CustomLinkFactory::make(['id' => 1])->persist();
        //対象メソッドをコール
        $this->CustomLinksTable->beforeDelete(new Event('beforeDelete'), CustomLinkFactory::get(1), new \ArrayObject());
        //スコープが設定できるか確認
        $result = $this->CustomLinksTable->getBehavior('Tree')->getConfig('scope');
        $this->assertEquals(['custom_table_id' => 1], $result);
    }

    /**
     * test updateSort
     */
    public function test_updateSort()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customLinks = $this->getService(CustomLinksServiceInterface::class);
        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact'
        ]);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        CustomFieldFactory::make(['id' => 1])->persist();
        CustomLinkFactory::make([
            'id' => 1,
            'no' => 2,
            'sort' => 4
        ])->persist();
        CustomLinkFactory::make([
            'id' => 2,
            'no' => 1,
            'lft' => 3,
            'rght' => 4
        ])->persist();

        //$fieldNameが存在した場合、
        $this->CustomLinksTable->updateSort($customLinks->getIndex(1)->toArray());
        //並び順を更新するできるか確認すること
        $customLink1 = $customLinks->get(1);
        $this->assertEquals(2, $customLink1->no);
        //lft: 1->3
        $this->assertEquals(3, $customLink1->lft);
        //rght: 2->4
        $this->assertEquals(4, $customLink1->rght);

        $customLink2 = $customLinks->get(2);
        $this->assertEquals(1, $customLink2->no);
        //lft: 3->1
        $this->assertEquals(1, $customLink2->lft);
        //rght: 4->2
        $this->assertEquals(2, $customLink2->rght);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');
    }

    /**
     * test getCurentSort
     */
    public function test_getCurentSort()
    {
        //データを生成
        CustomLinkFactory::make([
            'id' => 1,
            'lft' => 1,
            'rght' => 6,
        ])->persist();
        CustomLinkFactory::make([
            'id' => 2,
            'parent_id' => 1,
            'lft' => 2,
            'rght' => 3
        ])->persist();
        CustomLinkFactory::make([
            'id' => 3,
            'parent_id' => 1,
            'lft' => 4,
            'rght' => 5
        ])->persist();

        $rs = $this->CustomLinksTable->getCurentSort(1, 1, null);
        $this->assertEquals(1, $rs);

        $rs = $this->CustomLinksTable->getCurentSort(2, 1, 1);
        $this->assertEquals(1, $rs);

        $rs = $this->CustomLinksTable->getCurentSort(3, 1, 1);
        $this->assertEquals(2, $rs);
    }

    /**
     * test moveOffset
     */
    public function test_moveOffset()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customLinks = $this->getService(CustomLinksServiceInterface::class);
        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact'
        ]);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        CustomFieldFactory::make(['id' => 1])->persist();
        CustomLinkFactory::make([
            'id' => 1,
            'no' => 2,
            'sort' => 4
        ])->persist();
        CustomLinkFactory::make([
            'id' => 2,
            'no' => 1,
            'lft' => 3,
            'rght' => 4
        ])->persist();

        //$offset > 0、
        $rs = $this->CustomLinksTable->moveOffset($customLinks->get(1), 1);
        //並び順を更新するできるか確認すること
        $this->assertEquals(2, $rs->no);
        //lft: 1->3
        $this->assertEquals(3, $rs->lft);
        //rght: 2->4
        $this->assertEquals(4, $rs->rght);

        //$offset < 0、
        $rs = $this->CustomLinksTable->moveOffset($customLinks->get(2), -1);
        $this->assertEquals(1, $rs->no);
        //lft: 3->1
        $this->assertEquals(1, $rs->lft);
        //rght: 4->2
        $this->assertEquals(2, $rs->rght);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');
    }

    /**
     * test getUniqueName
     */
    public function test_getUniqueName()
    {
        //サービスをコル
        CustomLinkFactory::make([
            'custom_table_id' => 1,
            'name' => 'recruit_category'
        ])->persist();

        //nameが登録したことがない場合、
        $rs = $this->CustomLinksTable->getUniqueName('email', 1);
        $this->assertEquals('email', $rs);

        //nameが登録した場合、
        $rs = $this->CustomLinksTable->getUniqueName('recruit_category', 1);
        $this->assertEquals('recruit_category_2', $rs);
    }


}
