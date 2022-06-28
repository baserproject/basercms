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

/**
 * Class SiteValidationTest
 * @package BaserCore\Test\TestCase\Model\Validation
 */
class SiteValidationTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
    ];

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
    public function checkUrlDataProvider()
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

}
