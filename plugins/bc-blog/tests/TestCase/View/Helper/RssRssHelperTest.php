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
class RssRssHelperTest extends BcTestCase
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
     */
    public function test_elem()
    {
        // 準備
        $name = 'testElem';
        $attrib = ['attribute' => 'value'];
        $content = 'Test Content';
        $endTag = true;

        // 呼び出し
        $result = $this->RssHelper->elem($name, $attrib, $content, $endTag);
        // 結果チェック
        $this->assertEquals('<testElem attribute="value">Test Content</testElem>', $result);

    }


}
