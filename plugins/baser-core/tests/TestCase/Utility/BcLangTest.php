<?php

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcLang;

class BcLangTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testSetConfig()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testGetDefaultConfig()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testGetPattern()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test parseLang
     * @dataProvider parseLangDataProvider
     */
    public function testParseLang($acceptLanguage, $expected)
    {
        $result = BcLang::parseLang($acceptLanguage);
        $this->assertEquals($expected, $result);
    }

    public static function parseLangDataProvider()
    {
        return [
            [null, 'ja'],
            ['', 'ja'],
            ['en-US', 'en'],
            ['fr-CH, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5', 'fr'],
            ['en-US,en;q=0.9', 'en'],
            ['zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7', 'zh'],
            ['en-US,en;q=0.8,es-ES;q=0.5,es;q=0.3', 'en'],
            ['123,456', '123'],
        ];
    }
}
