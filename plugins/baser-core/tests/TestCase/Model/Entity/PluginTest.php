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


}