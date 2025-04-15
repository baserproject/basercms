<?php
/**
 * CuCustomField : baserCMS Custom Field Text Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @license          MIT LICENSE
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
