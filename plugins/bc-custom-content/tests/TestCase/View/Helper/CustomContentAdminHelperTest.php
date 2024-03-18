<?php

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcCustomContent\View\Helper\CustomContentAdminHelper;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class CustomContentAdminHelperTest extends BcTestCase
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
        $view = new View($this->getRequest());
        $this->CustomContentAdminHelper = new CustomContentAdminHelper($view);
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
     * test getFieldName
     */
    public function test_getFieldName()
    {
        //case option is empty
        $customLink = CustomLinkFactory::make([
            'name' => 'test',
        ])->getEntity();
        $result = $this->CustomContentAdminHelper->getFieldName($customLink);
        $this->assertEquals('test', $result);
        //case option is not empty
        $options = [
            'fieldName' => 'fieldName option',
        ];
        $customLink = CustomLinkFactory::make([
            'name' => 'test',
        ])->getEntity();
        $result = $this->CustomContentAdminHelper->getFieldName($customLink, $options);
        $this->assertEquals('fieldName option', $result);
    }
}