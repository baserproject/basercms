<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\AppView;
use BaserCore\View\Helper\BcFormTableHelper;

class BcFormTableHelperTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $View = new AppView();
        $this->BcListTable = new BcFormTableHelper($View);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testDispatchBefore()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testDispatchAfter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}