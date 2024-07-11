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
        $code = 'color: #333; font-size: 20px;';

        $expected = [
            'color' => '#333',
            'font-size' => '20px'
        ];

        $params = [&$code];
        $result = $this->execPrivateMethod(new CKEditorStyleParser(), 'parseCode', $params);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider readStringDataProvider
     */
    public function test_readString($target, $body, $i, $expected)
    {
        $size = strlen($body);
        $params = [$target, $body, $size, &$i];
        $result = $this->execPrivateMethod(new CKEditorStyleParser(), 'readString', $params);
        $this->assertEquals($expected, $result);
    }

    public static function readStringDataProvider()
    {
        return [
            ['"', 'This is a "test" string', 11, 'test'],
            ["'", "This is a 'test' string", 11, 'test'],
            ['"', 'This is a \\"test\\" string', 12, 'test\\'],
            ['"', '', 0, ''],
            ['"', 'No target character here', 0, 'No target character here']
        ];
    }

}