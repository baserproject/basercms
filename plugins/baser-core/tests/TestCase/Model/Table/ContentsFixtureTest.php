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
        $this->loadFixtures('Contents', 'Sites', 'Users', 'UserGroups', 'UsersUserGroups', 'Pages', 'ContentFolders');
        parent::setUp();
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $id = array_column($this->contents, 'entity_id');
        $typeList = array_combine(array_column($this->contents, 'entity_id'), array_column($this->contents, 'type'));

        // 同じものを取得できるか
        // foreach ($this->contents as $content) {
        //     $a = $content->type;
        // }
        // 同数のエンティティが取得できるか
    }
}
