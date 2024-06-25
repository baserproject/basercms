<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */
namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\Helper\BcAdminHelper;
use BaserCore\View\Helper\BcArrayHelper;
use Cake\View\View;

/**
 * Admin helper library.
 *
 * 管理画面用のヘルパー
 *
 * @property BcAdminHelper $Helper
 */
class BcArrayHelperTest extends BcTestCase
{
    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Helper = new BcArrayHelper(new View(null));
        $this->data = ['b' => 'カンジ', 'd' => 'リュウジ', 'a' => 'スナオ', 'c' => 'ゴンチャン'];
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * 配列の最初のキーを判定する
     *
     * */
    public function testFirst()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->assertTrue($this->Helper->first($this->data, 'b'));
        $this->assertFalse($this->Helper->first($this->data, 'c'));
    }

    /**
     * 配列の最後のキーを判定する
     *
     * */
    public function testLast()
    {
        $this->assertTrue($this->Helper->last($this->data, 'c'));
        $this->assertFalse($this->Helper->last($this->data, 'd'));
    }

    /**
     * 配列にテキストを追加する
     *
     * */
    public function testAddText()
    {
        // prefixとsuffix両方指定
        $result = $this->Helper->addText($this->data, 'baserCMS開発者:', 'さん');
        $expect = [
            'b' => 'baserCMS開発者:カンジさん',
            'd' => 'baserCMS開発者:リュウジさん',
            'a' => 'baserCMS開発者:スナオさん',
            'c' => 'baserCMS開発者:ゴンチャンさん',
        ];
        $this->assertEquals($expect, $result);

        // prefixのみ指定
        $result = $this->Helper->addText($this->data, 'baserCMS開発者:');
        $expect = [
            'b' => 'baserCMS開発者:カンジ',
            'd' => 'baserCMS開発者:リュウジ',
            'a' => 'baserCMS開発者:スナオ',
            'c' => 'baserCMS開発者:ゴンチャン',
        ];
        $this->assertEquals($expect, $result);

        // suffixのみ指定
        $result = $this->Helper->addText($this->data, null, 'さん');
        $expect = [
            'b' => 'カンジさん',
            'd' => 'リュウジさん',
            'a' => 'スナオさん',
            'c' => 'ゴンチャンさん',
        ];
        $this->assertEquals($expect, $result);

        // prefixとsuffix両方指定なし
        $result = $this->Helper->addText($this->data);
        $expect = [
            'b' => 'カンジ',
            'd' => 'リュウジ',
            'a' => 'スナオ',
            'c' => 'ゴンチャン',
        ];
        $this->assertEquals($expect, $result);
    }

    public function test_addTextWithPrefixAndSuffix()
    {
        $value = 'test';
        $this->execPrivateMethod($this->Helper, '__addText', [&$value, null, "prefix-,-suffix"]);
        $this->assertEquals("prefix-test-suffix", $value);
    }

    public function test_addTextWithOnlyPrefix()
    {
        $value = 'test';
        $this->execPrivateMethod($this->Helper, '__addText', [&$value, null, "prefix-,"]);
        $this->assertEquals("prefix-test", $value);
    }

    public function test_AddTextWithOnlySuffix()
    {
        $value = 'test';
        $this->execPrivateMethod($this->Helper, '__addText', [&$value, null, ",-suffix"]);
        $this->assertEquals("test-suffix", $value);
    }
}
