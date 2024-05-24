<?php

namespace BcCustomContent\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Utility\CustomContentUtil;
use Cake\Core\Configure;

class CustomContentUtilTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomContentUtil = new CustomContentUtil();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test getPluginSetting
     */
    public function test_getPluginSetting()
    {
        //plugin and name is not empty
        Configure::write('BcCustomContent.fieldTypes.testPlugin.testSetting', 'testValue');
        $rs = CustomContentUtil::getPluginSetting('testPlugin', 'testSetting');
        $this->assertEquals('testValue', $rs);

        //plugin is not empty and name is empty
        $rs = CustomContentUtil::getPluginSetting('testPlugin');
        $this->assertEquals(['testSetting' => 'testValue'], $rs);
    }
}