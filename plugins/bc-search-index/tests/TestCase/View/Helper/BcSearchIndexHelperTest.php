<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcSearchIndex\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcSearchIndex\View\Helper\BcSearchIndexHelper;

/**
 * Class BcSearchIndexHelperTest
 * @property BcSearchIndexHelper $BcSearchIndex
 */
class BcSearchIndexHelperTest extends BcTestCase
{

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
//        $this->_View = new BcAppView();
//        $this->_View->request = $this->_getRequest('/');
//        $this->_View->helpers = ['BcSearchIndex'];
//        $this->_View->loadHelpers();
//        $this->BcSearchIndex = $this->_View->BcSearchIndex;
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * 公開状態確認
     *
     * 詳細なテストは、SearchIndex::allowPublish() のテストに委ねる
     */
    public function testAllowPublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $result = $this->BcSearchIndex->allowPublish([
            'status' => true,
            'publish_begin' => date('Y-m-d H:i:s', strtotime("+1 hour")),
            'publish_end' => ''
        ]);
        $this->assertEquals(false, $result);
    }
}
