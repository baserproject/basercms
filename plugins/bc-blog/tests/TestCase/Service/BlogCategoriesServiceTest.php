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

namespace BcBlog\Test\TestCase\Service;

/**
 * BlogCategoriesServiceTest
 */
class BlogCategoriesServiceTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * コントロールソースを取得する
     *
     * @param string $field フィールド名
     * @param array $option オプション
     * @param array $expected 期待値
     * @dataProvider getControlSourceDataProvider
     */
    public function testGetControlSource($field, $options, $expected)
    {
        $this->markTestIncomplete('このテストは、動作の確認が必要です。');
        $result = $this->BlogCategory->getControlSource($field, $options);
        $this->assertEquals($expected, $result, 'コントロールソースを正しく取得できません');
    }

    public function getControlSourceDataProvider()
    {
        return [
            ['parent_id', ['blogContentId' => 1], [
                1 => 'プレスリリース',
                2 => '　　　└子カテゴリ',
                3 => '親子関係なしカテゴリ'
            ]],
            ['parent_id', ['blogContentId' => 0], []],
            ['parent_id', ['blogContentId' => 1, 'excludeParentId' => true], [3 => '親子関係なしカテゴリ']],
            ['parent_id', ['blogContentId' => 1, 'ownerId' => 2], []],
            ['parent_id', ['blogContentId' => 1, 'ownerId' => 1], [
                1 => 'プレスリリース',
                2 => '　　　└子カテゴリ',
                3 => '親子関係なしカテゴリ'
            ]],
            ['owner_id', [], [
                1 => 'システム管理',
                2 => 'サイト運営'
            ]],
        ];
    }

}
