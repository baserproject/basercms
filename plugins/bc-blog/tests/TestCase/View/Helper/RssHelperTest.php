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
     * @param array $att
     * @param array $elements
     * @param string $expect
     * @dataProvider itemDataProvider
     */
    public function test_item(array $att, array $elements, $expect)
    {
        // 呼び出し
        $result = $this->RssHelper->item($att, $elements);
        // 結果チェック
        $this->assertEquals($expect, $result);
    }

    public static function itemDataProvider()
    {
        return [
            [
                ['attribute' => 'value'],
                [
                    'link' => 'http://example.com',
                    'guid' => 'http://example.com/guid',
                    'pubDate' => '2024-01-26',
                    'category' => ['domain' => 'example.com', 'category1', 'category2']
                ],
                '<item attribute="value"><link>http://example.com</link><guid>http://example.com/guid</guid><pubDate>Fri, 26 Jan 2024 00:00:00 +0900</pubDate><category>example.com</category><category>category1</category><category>category2</category></item>'
            ],
            [
                ['attribute' => 'value'],
                [
                    'link' => 'http://example.com',
                    'guid' => 'http://example.com/guid',
                    'pubDate' => '2024-01-26',
                    'category' => ['domain' => 'example.com', 'category1', 'category2'],
                    'convertEntities' => false
                ],
                '<item attribute="value"><link>http://example.com</link><guid>http://example.com/guid</guid><pubDate>Fri, 26 Jan 2024 00:00:00 +0900</pubDate><category>example.com</category><category>category1</category><category>category2</category><convertEntities attribute="value"/></item>'
            ],
            [
                ['attribute' => 'value'],
                [
                    'link' => 'http://example.com',
                    'guid' => 'http://example.com/guid',
                    'pubDate' => '2024-01-26',
                    'category' => ['domain' => 'example.com', 'category1', 'category2'],
                    'comments' => ['comment' => 'ok', 'url' => 'http://example.com/comment']
                ],
                '<item attribute="value"><link>http://example.com</link><guid>http://example.com/guid</guid><pubDate>Fri, 26 Jan 2024 00:00:00 +0900</pubDate><category>example.com</category><category>category1</category><category>category2</category><comments comment="ok">http://example.com/comment</comments></item>'
            ]
        ];
    }

    /**
     * test items
     */
    public function test_items()
    {
        //準備
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }


    /**
     * test channel
     */
    public function test_channel()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
