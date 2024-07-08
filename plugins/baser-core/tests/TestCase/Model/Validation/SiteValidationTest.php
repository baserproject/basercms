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

namespace BaserCore\Test\TestCase\Model\Validation;

use BaserCore\Model\Validation\SiteValidation;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class SiteValidationTest
 */
class SiteValidationTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * Test subject
     *
     * @var SiteValidation
     */
    public $SiteValidation;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SiteValidation = new SiteValidation();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SiteValidation);
        parent::tearDown();
    }

    /**
     * エイリアスのスラッシュをチェックする
     *
     * - 連続してスラッシュは入力できない
     * - 先頭と末尾にスラッシュは入力できない
     *
     * @param string $alias チェックするエイリアス
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider checkUrlDataProvider
     */
    public function testAliasSlashChecks($alias, $expected, $message = null)
    {
        $result = $this->SiteValidation->aliasSlashChecks($alias);
        $this->assertEquals($expected, $result);
    }

    public static function checkUrlDataProvider()
    {
        return [
            ['en', true],
            ['hoge//', false],
            ['/hoge', false],
            ['ho/ge', true],
        ];
    }

    /**
     * test checkContentExists
     * @param $alias
     * @param $expected
     */
    public function test_checkContentExists()
    {
        $contents = ContentFactory::make()->getTable();
        // 重複なし
        $this->assertTrue($this->SiteValidation->checkContentExists('aaa', []));
        // 重複コンテンツあり
        $content = ContentFactory::make(['url' => '/aaa'])->persist();
        $this->assertFalse($this->SiteValidation->checkContentExists('aaa', []));
        $contents->delete($content);
        // 重複フォルダあり
        $content = ContentFactory::make(['url' => '/aaa/'])->persist();
        $this->assertFalse($this->SiteValidation->checkContentExists('aaa', []));
        $contents->delete($content);
        // 重複フォルダあり（対象サイト）
        $content = ContentFactory::make(['url' => '/aaa/', 'site_id' => 2])->persist();
        $this->assertTrue($this->SiteValidation->checkContentExists('aaa', ['data' => ['id' => 2]]));
        $contents->delete($content);
    }

    /**
     * test checkSiteAlias
     */
    public function test_checkSiteAlias()
    {
        //use_subdomain = 0 の場合、ドット（.） が利用不可、スラッシュは利用可能
        $result = $this->SiteValidation->checkSiteAlias('example.com', ['data' => ['use_subdomain' => 0]]);
        $this->assertFalse($result);
        $result = $this->SiteValidation->checkSiteAlias('/news/new-1', ['data' => ['use_subdomain' => 0]]);
        $this->assertTrue($result);

        //use_subdomain = 1、かつ、domain_type = 1 と 2 の場合、ドット（.） が利用可能、スラッシュは利用不可
        $result = $this->SiteValidation->checkSiteAlias('example.com', ['data' => ['use_subdomain' => 1]]);
        $this->assertTrue($result);
        $result = $this->SiteValidation->checkSiteAlias('example.com/news', ['data' => ['use_subdomain' => 1]]);
        $this->assertFalse($result);
    }
}
