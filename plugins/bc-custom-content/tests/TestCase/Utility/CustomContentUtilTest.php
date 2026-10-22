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

    /**
     * test getDeniedFileExts
     */
    public function test_getDeniedFileExts()
    {
        $this->assertContains('php', CustomContentUtil::getDeniedFileExts());
        $this->assertContains('phtml', CustomContentUtil::getDeniedFileExts());

        $backup = Configure::read('BcCustomContent.deniedExtension');
        Configure::write('BcCustomContent.deniedExtension', ' PHP , cgi ,');
        $this->assertEquals(['php', 'cgi'], CustomContentUtil::getDeniedFileExts());
        Configure::write('BcCustomContent.deniedExtension', null);
        $this->assertEquals([], CustomContentUtil::getDeniedFileExts());
        Configure::write('BcCustomContent.deniedExtension', $backup);
    }

    /**
     * test filterDeniedFileExts
     */
    public function test_filterDeniedFileExts()
    {
        $this->assertEquals(['jpg', 'png'], CustomContentUtil::filterDeniedFileExts(['jpg', 'PHP', ' png ', 'phtml', '']));
        $this->assertEquals([], CustomContentUtil::filterDeniedFileExts(['php']));
    }
}
