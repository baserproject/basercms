<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSearchIndex\Test\TestCase\Model\Table;

use BcSearchIndex\Model\Table\SearchIndexesTable;
use BaserCore\TestSuite\BcTestCase;
use BcSearchIndex\Test\Scenario\Service\SearchIndexesServiceScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class SearchIndexesTableTest
 * @property SearchIndexesTable $SearchIndexes
 */
class SearchIndexesTableTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * @var SearchIndexesTable
     */
    public $SearchIndexes;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SearchIndexes = $this->getTableLocator()->get('BcSearchIndex.SearchIndexes');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SearchIndexes);
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertTrue($this->SearchIndexes->hasBehavior('Timestamp'));
    }

    /**
     * 公開状態を取得する
     *
     * 公開期間は「現在からの相対指定」で受け取り、テスト実行時点で日時へ変換する。
     * データプロバイダで日時を確定させると、変換から実行までの経過時間により
     * 結果が変わってしまうため。
     *
     * @param string|null $publishBegin 公開開始日時の相対指定
     * @param string|null $publishEnd 公開終了日時の相対指定
     * @param bool $status 公開状態
     * @param bool $expected 期待値
     * @dataProvider allowPublishDataProvider
     */
    public function testAllowPublish($publishBegin, $publishEnd, $status, $expected)
    {
        $this->loadFixtureScenario(SearchIndexesServiceScenario::class);
        $data = [
            'publish_begin' => $this->createDateTime($publishBegin),
            'publish_end' => $this->createDateTime($publishEnd),
            'status' => $status
        ];
        $this->assertEquals($expected, $this->SearchIndexes->allowPublish($data));
    }

    public static function allowPublishDataProvider()
    {
        return [
            // 公開状態が false の場合は非公開
            [null, null, false, false],
            // 公開期間の指定がない場合は公開状態に従う
            [null, null, true, true],
            // 公開終了日時を過ぎている場合は非公開
            [null, '-1 hour', true, false],
            // 公開終了日時前の場合は公開
            [null, '+1 hour', true, true],
            // 公開開始日時を過ぎている場合は公開
            ['-1 hour', null, true, true],
            // 公開開始日時前の場合は非公開
            ['+1 hour', null, true, false],
            // 公開期間が終了している場合は非公開
            ['-2 hours', '-1 hour', true, false]
        ];
    }

    /**
     * 現在からの相対指定を日時文字列に変換する
     *
     * @param string|null $modifier strtotime() が解釈できる相対指定
     * @return string|null
     */
    private function createDateTime(?string $modifier): ?string
    {
        if ($modifier === null) return null;
        return date('Y-m-d H:i:s', strtotime($modifier));
    }
}
