<?php

namespace BaserCore\Test\TestCase\Model\Entity;

use BaserCore\Model\Entity\Plugin;
use BaserCore\TestSuite\BcTestCase;

class PluginTest extends BcTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->Plugin = new Plugin();
    }

    public function tearDown(): void
    {
        unset($this->Plugin);
        parent::tearDown();
    }

    public function testIsPlugin()
    {
        $this->Plugin->type = 'Plugin';
        $this->assertTrue($this->Plugin->isPlugin());

        $this->Plugin->type = 'Theme';
        $this->assertFalse($this->Plugin->isPlugin());
    }

    /**
     * @dataProvider hasTypeDataProvider
     */
    public function test_hasType($type, $types, $expected)
    {
        $this->Plugin->type = $type;
        $result = $this->Plugin->hasType($types);
        $this->assertEquals($expected, $result);
    }

    public static function hasTypeDataProvider()
    {
        return [
            [null, ['exampleType'], false],
            ['exampleType', ['exampleType'], true],
            ['exampleType', ['otherType'], false],
            [['exampleType', 'anotherType'], ['exampleType'], true],
            [['exampleType', 'anotherType'], ['anotherType'], true],
            [['exampleType', 'anotherType'], ['differentType'], false]
        ];
    }

    public function testIsAdminTheme()
    {
        //with type = 'AdminTheme'
        $this->Plugin->type = 'AdminTheme';
        $this->assertTrue($this->Plugin->isAdminTheme());

        //with type = 'Plugin'
        $this->Plugin->type = 'Plugin';
        $this->assertFalse($this->Plugin->isAdminTheme());
    }

    public function testIsTheme()
    {
        //with type = 'Theme'
        $this->Plugin->type = 'Theme';
        $this->assertTrue($this->Plugin->isTheme());

        //with type = 'Plugin'
        $this->Plugin->type = 'Plugin';
        $this->assertFalse($this->Plugin->isTheme());
    }

    /**
     * test isCorePlugin
     */
    public function testIsCorePlugin()
    {
        //with type = 'CorePlugin' and name in Configure::read('BcApp.corePlugins')
        $this->Plugin->type = 'CorePlugin';
        $this->Plugin->name = 'BcContentLink';
        $this->assertTrue($this->Plugin->isCorePlugin());

        //with type = 'CorePlugin' and name not in Configure::read('BcApp.corePlugins')
        $this->Plugin->type = 'CorePlugin';
        $this->Plugin->name = 'notCorePlugin';
        $this->assertFalse($this->Plugin->isCorePlugin());

        //with type = 'Plugin'
        $this->Plugin->type = 'Plugin';
        $this->assertFalse($this->Plugin->isCorePlugin());
    }

}
