<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.3.1
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\BcQueryParameterTrait;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class BcQueryParameterTraitTest
 */
class BcQueryParameterTraitTest extends BcTestCase
{

    /**
     * @var object トレイトを利用する無名クラス
     */
    private $target;

    /**
     * Set Up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->target = new class {
            use BcQueryParameterTrait;
        };
    }

    /**
     * test removeOrmStructure
     *
     * ORM 内部構造キーが除去され、通常の検索・ページングキーが保持されることを確認する。
     *
     * @param array $input
     * @param array $expected
     * @return void
     * @dataProvider removeOrmStructureDataProvider
     */
    public function test_removeOrmStructure($input, $expected)
    {
        $this->assertSame($expected, $this->target->removeOrmStructure($input));
    }

    public static function removeOrmStructureDataProvider(): array
    {
        return [
            // ORM 内部構造キーは除去される
            'contain を除去' => [['contain' => ['BlogTags'], 'limit' => 10], ['limit' => 10]],
            'conditions を除去' => [['conditions' => ['1=1'], 'page' => 2], ['page' => 2]],
            'fields を除去' => [['fields' => ['id'], 'name' => 'a'], ['name' => 'a']],
            '複数の予約キーを除去' => [
                ['contain' => 1, 'conditions' => 1, 'fields' => 1, 'finder' => 1,
                    'joinType' => 1, 'strategy' => 1, 'matching' => 1, 'notMatching' => 1,
                    'leftJoinWith' => 1, 'innerJoinWith' => 1, 'select' => 1, 'group' => 1,
                    'having' => 1, 'keep' => 'x'],
                ['keep' => 'x']
            ],
            // 通常のキーは保持される（カスタムフィールド検索の動的キー・ページング等）
            '通常キーは保持' => [
                ['limit' => 10, 'page' => 2, 'sort' => 'posted', 'direction' => 'DESC',
                    'job_type' => 'engineer', 'title' => 'foo'],
                ['limit' => 10, 'page' => 2, 'sort' => 'posted', 'direction' => 'DESC',
                    'job_type' => 'engineer', 'title' => 'foo']
            ],
            '空配列' => [[], []],
        ];
    }

}
