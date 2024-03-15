<?php

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\View\Helper\CustomContentAdminHelper;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class CustomContentAdminHelperTest extends BcTestCase
{

    use ScenarioAwareTrait;
    public function setUp(): void
    {
        parent::setUp();
        $view = new View($this->getRequest());
        $this->CustomContentAdminHelper = new CustomContentAdminHelper($view);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test label
     */
    public function test_label()
    {
        /**
         * case customField type BcCcTextarea and customLink parent_id is true
         * and $options is empty
         */
        $customField = new CustomField([
            'type' => 'BcCcTextarea',
        ]);
        //how to create one array customLink
        $customLink = new CustomLink([
            'name' => 'test custom link',
            'status' => 1,
            'custom_field' => $customField,
            'parent_id' => 1,
        ]);
        $rs = $this->CustomContentAdminHelper->label($customLink);
        $this->assertEquals('<label for="test-custom-link">Test Custom Link</label><br>', $rs);
        /**
         * case customField type BcCcTextarea and customLink parent_id is true
         * and $options is not empty
         */
        $options = ['fieldName' => 'is fieldName'];
        $customField = new CustomField([
            'name' => 'test custom field',
            'type' => 'BcCcTextarea',
        ]);
        //how to create one array customLink
        $customLink = new CustomLink([
            'name' => 'test custom link',
            'status' => 1,
            'display_admin_list' => 1,
            'custom_field' => $customField,
            'parent_id' => 1,
        ]);
        $rs = $this->CustomContentAdminHelper->label($customLink, $options);
        $this->assertEquals('<label for="is-fieldname">Is Field Name</label><br>', $rs);
        /**
         * case customField type BcCcTextarea and customLink parent_id is false
         * and options is empty
         */
        $customField = new CustomField([
            'name' => 'test custom field',
            'type' => 'BcCcTextarea',
        ]);
        //how to create one array customLink
        $customLink = new CustomLink([
            'name' => 'test custom link',
            'status' => 1,
            'display_admin_list' => 1,
            'custom_field' => $customField,
            'parent_id' => 0,
        ]);
        $rs = $this->CustomContentAdminHelper->label($customLink);
        $this->assertEquals('<label for="test-custom-link">Test Custom Link</label>', $rs);
        /**
         * case customField type BcCcTextarea and customLink parent_id is false
         * and options is not empty
         */
        $options = ['fieldName' => 'is fieldName is not empty'];
        $customField = new CustomField([
            'name' => 'test custom field',
            'type' => 'BcCcTextarea',
        ]);
        //how to create one array customLink
        $customLink = new CustomLink([
            'name' => 'test custom link',
            'status' => 1,
            'display_admin_list' => 1,
            'custom_field' => $customField,
            'parent_id' => 0,
        ]);
        $rs = $this->CustomContentAdminHelper->label($customLink, $options);
        $this->assertEquals('<label for="is-fieldname-is-not-empty">Is Field Name Is Not Empty</label>', $rs);
    }
}