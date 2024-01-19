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
use BaserCore\Utility\BcContainerTrait;
use BaserCore\View\Helper\BcSmartphoneHelper;

/**
 * BcSmartphoneHelper Test Case
 *
 * @property BcSmartphoneHelper $BcSmartphone
 */
class BcSmartphoneHelperTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;


    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
//        $this->View = new BcAppView();
//        $this->BcSmartphone = new BcSmartphoneHelper($this->View);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcSmartphone);

        parent::tearDown();
    }

    /**
     * afterLayout
     *
     * @return void
     */
    public function testAfterLayout()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
