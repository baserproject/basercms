<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.5
 * @license         https://basercms.net/license/index.html
 */
namespace BcMail\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;

class MailBaserHelperTest extends BcTestCase
{

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
//        $this->BcBaser = new BcBaserHelper(new View());
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
//        unset($this->BcBaser);
//        Router::reload();
        parent::tearDown();
    }

    /**
     * 現在のページがメールプラグインかどうかを判定する
     *
     * @param bool $expected 期待値
     * @param string $url リクエストURL
     * @return void
     * @dataProvider isMailDataProvider
     */
    public function testIsMail($expected, $url)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $MailBaser = $this->BcBaser->getPluginBaser('BcMail');
        $MailBaser->request = $this->_getRequest($url);
        $this->assertEquals($expected, $this->BcBaser->isMail());
    }

    public static function isMailDataProvider()
    {
        return [
            //PC
            [false, '/'],
            [false, '/index'],
            [false, '/news/index'],
            [true, '/contact/index'],
            // モバイルページ
            [false, '/m/'],
            [false, '/m/index'],
            [false, '/m/news/index'],
            [true, '/m/contact/index'],
            // スマートフォンページ
            [false, '/s/'],
            [false, '/s/index'],
            [false, '/s/news/index'],
            [true, '/s/contact/index']
        ];
    }
}
