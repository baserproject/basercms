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

namespace BaserCore\Test\TestCase\Model\Table;

use Cake\Utility\Inflector;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class ContentsFixtureTest
 * コンテンツ関連のFixtureの整合性テスト
 *
 * @package Baser.Test.Case.Model
 * @property ContentsTable $Contents
 */
class ContentsFixtureTest extends BcTestCase
{
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.ContentFolders',
    ];

    /**
     * Auto Fixtures
     * @var bool
     */
    public $autoFixtures = false;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures('Contents', 'Sites', 'Users', 'UserGroups', 'UsersUserGroups', 'Pages', 'ContentFolders');
        $config = $this->getTableLocator()->exists('Contents')? [] : ['className' => 'BaserCore\Model\Table\ContentsTable'];
        $this->Contents = $this->getTableLocator()->get('Contents', $config);
        $this->contents = $this->Contents->find()->applyOptions(['withDeleted'])->all()->toArray();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Contents, $this->contents);
        parent::tearDown();
    }

    /**
     * コンテンツフィクスチャーのエンティティIDが被ってないかをチェックする
     * ※ エイリアスの場合は同じエンティティIDのため含まない
     * @return void
     */
    public function testUniqueEntityId()
    {
        $aliasNum = $this->Contents->find()->where(['Contents.title  LIKE' => '%エイリアス%'])->all()->count();
        $id = array_column($this->contents, 'entity_id');
        $this->assertEquals(count(array_unique($id)), count($id) - $aliasNum);
    }

    /**
     * testFixtureRelations
     * コンテンツ関連のFixtureと同じ物が同じ数取得できるかテスト
     * @return void
     */
    public function testFixtureRelations()
    {
        $id = array_column($this->contents, 'entity_id');
        $type = array_column($this->contents, 'type');
        $typeCount = array_count_values($type);
        $typeList = array_combine($id, $type);
        // テストに含めないリスト
        $exclude = ["BcAdminContentsTest", "MailContent", "BlogContent"];
        // contentFixtureに存在する$entityIdがちゃんと関連するFixtureに存在するかテスト
        foreach($typeList as $entityId => $type) {
            if (!in_array($type, $exclude)) {
                $table = $this->getTableLocator()->get(Inflector::pluralize($type));
                $this->assertFalse($table->findById($entityId)->isEmpty(), "ID: $entityId 失敗");
            }
        }
        // 同数のエンティティが取得できるか
        $testedType = array_diff_key($typeCount, array_flip($exclude));
        foreach($testedType as $type => $count) {
            $table = $this->getTableLocator()->get(Inflector::pluralize($type));
            // エイリアスを排除した実際の個数
            $alias_entityId = $this->Contents->find()->where(['Contents.title  LIKE' => '%エイリアス%', 'type' => $type])->all()->count();
            $actualCount = $count - $alias_entityId;
            $this->assertEquals($actualCount, $table->find()->all()->count(), "$type フィクスチャーのエンティティ数がコンテンツフィクスチャーと異なります");
        }
    }
}
