<?php

namespace BaserCore\Test\TestCase\vendor;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Vendor\CKEditorStyleParser;

class CKEditorStyleParserTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testParse()
    {
        $css = "
        # comment
        selector {
            property: value;
        }";

        $expected = [
            [
                'name' => 'comment(selector)',
                'element' => 'selector',
                'styles' => ['property' => 'value']
            ]
        ];

        $result = CKEditorStyleParser::parse($css);
        $this->assertEquals($expected, $result);
    }

    public function test_parseCode()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function test_readString()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}