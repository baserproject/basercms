<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */
namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\Helper\BcTextHelper;

/**
 * text helper library.
 *
 * @property BcTextHelper $Helper
 */
class BcXmlHelperTest extends BcTestCase
{
    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
//        $View = new View();
//        $this->BcXml = new BcXmlHelper($View);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcXml);
        parent::tearDown();
    }

    /**
     * XML宣言を生成
     * IE6以外の場合のみ生成する
     *
     * @param array $attrib
     * @param string $agent ユーザーエージェント
     * @param string $expected 期待値
     * @dataProvider headerDataProvider
     */
    public function testHeader($attrib, $agent, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $_SERVER['HTTP_USER_AGENT'] = $agent;

        $result = $this->BcXml->header($attrib);
        $this->assertEquals($expected, $result);
    }

    public static function headerDataProvider()
    {
        return [
            [
                ['test' => 'testValue'],
                'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)',
                '<?xml version="1.0" encoding="UTF-8" test="testValue" ?>'
            ],
            [
                ['test1' => 'testValue1', 'test2' => 'testValue2'],
                'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)',
                '<?xml version="1.0" encoding="UTF-8" test1="testValue1" test2="testValue2" ?>'
            ],
            [
                ['test' => 'testValue'],
                'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0;)',
                ''
            ],
            [
                ['encoding' => 'SJIS'],
                'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1',
                '<?xml version="1.0" encoding="SJIS" ?>'
            ],
        ];
    }

}
