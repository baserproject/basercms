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

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcFrontAppView;
use BcCcPref\View\Helper\BcCcPrefHelper;
use BcCustomContent\Test\Factory\CustomLinkFactory;

/**
 * BcCcPrefHelper Test Case
 * @property BcCcPrefHelper $BcCcPrefHelper
 */
class BcCcPrefHelperTest extends BcTestCase
{

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcCcPrefHelper = new BcCcPrefHelper(new BcFrontAppView($this->getRequest()));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->BcCcPrefHelper);
    }

    /**
     * Test preview
     */
    public function testPreview()
    {
        $customLink = CustomLinkFactory::make(['name' => 'pref'])->getEntity();
        $result = $this->BcCcPrefHelper->preview($customLink);

        $this->assertStringContainsString('v-model="entity.default_value"', $result);
        $this->assertStringNotContainsString(':value="entity.default_value"', $result);
    }

}
