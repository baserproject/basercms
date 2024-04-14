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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }


    /**
     * test channel
     */
    public function test_channel()
    {
        $attrib = ['attribute' => 'value'];
        $elements = [
            'link' => '/news',
            'title' => 'Channel Title',
            'description' => 'Channel Description',
            'customElement' => 'Custom Value',
        ];
        $content = 'Channel Content';
        //正常系実行
        $result = $this->RssHelper->channel($attrib, $elements, $content);
        $this->assertStringContainsString('<channel attribute="value">', $result);
        $this->assertStringContainsString('Channel Content</channel>', $result);
        $this->assertStringContainsString('<link>https://localhost/news</link>', $result);
        $this->assertStringContainsString('<title>Channel Title', $result);
        $this->assertStringContainsString('<description>Channel Description', $result);
        $this->assertStringContainsString('<customElement>Custom Value', $result);
    }
}
