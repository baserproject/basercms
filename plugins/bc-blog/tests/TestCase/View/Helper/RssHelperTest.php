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

namespace BcBlog\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcBlog\View\BlogFrontAppView;
use BcBlog\View\Helper\RssHelper;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Blog helper library.
 *
 * @property RssHelper $RssHelper
 */
class RssHelperTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $view = new BlogFrontAppView($this->getRequest());
        $this->RssHelper = new RssHelper($view);
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
     * test elem
     * @param string $name
     * @param array $attrib
     * @param string|array|null $content
     * @param bool $endTag
     * @param string $expect
     * @dataProvider elemDataProvider
     */
    public function test_elem($name, $attrib, $content, $endTag, $expect)
    {
        // 呼び出し
        $result = $this->RssHelper->elem($name, $attrib, $content, $endTag);
        // 結果チェック
        $this->assertEquals($expect, $result);
    }

    public static function elemDataProvider()
    {
        return [
            ['testElem', ['attribute' => 'value'], 'Test Content', true, '<testElem attribute="value">Test Content</testElem>'],
            ['testElem', ['attribute' => 'value', 'namespace' => 'namespace'], ['cdata' => 'cdata', 'value' => 'content value'], true, '<testElem xmlns="namespace" attribute="value"><![CDATA[content value]]></testElem>'],
        ];

    }

    /**
     * test time
     */
    public function test_time()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test item
     */
    public function test_item()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test items
     */
    public function test_items()
    {
        //準備
        $items = [
            ['title' => 'Item 1', 'link' => 'http://example.com/item1'],
            ['title' => 'Item 2', 'link' => 'http://example.com/item2'],
            // Add more test data as needed
        ];
        //$callback = null場合、
        $result = $this->RssHelper->items($items);
        $this->assertStringContainsString('<item><title>Item 1', $result);
        $this->assertStringContainsString('<link>http://example.com/item2', $result);

        //$callback != null場合、
        $callbackMock = function ($item) {
            return ['title' => strtoupper($item['title'])];
        };
        $result = $this->RssHelper->items($items, $callbackMock);
        $this->assertStringContainsString('<item><title>ITEM 1', $result);
    }


    /**
     * test channel
     */
    public function test_channel()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
