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

namespace BcCcCheckbox\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcFrontAppView;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Factory\CustomLinkFactory;

/**
 * BcCcCheckboxHelper Test Case
 * @property BcCcCheckboxHelper $BcCcCheckboxHelper
 */
class BcCcCheckboxHelperTest extends BcTestCase
{

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcCcCheckboxHelper = new BcCcCheckboxHelper(new BcFrontAppView($this->getRequest()));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->BcCcCheckboxHelper);
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $customField = CustomFieldFactory::make()->getEntity();
        $customField->meta = ['BcCcCheckbox' => ['label' => 'label']];
        $customLink = CustomLinkFactory::make()->getEntity();
        $customLink->custom_field = $customField;
        $result = $this->BcCcCheckboxHelper->get(true, $customLink);
        $this->assertEquals('label', $result);

        $result = $this->BcCcCheckboxHelper->get(false, $customLink);
        $this->assertEquals('', $result);
    }

}
