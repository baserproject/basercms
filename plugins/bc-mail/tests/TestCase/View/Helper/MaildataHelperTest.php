<?php
namespace BcMail\Test\TestCase\View\Helper;
use BaserCore\TestSuite\BcTestCase;

class MaildataHelperTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp():void
    {
        parent::setUp();
//        $this->View = new BcAppView(null);
//        $this->View->request = $this->_getRequest('/');
//        $this->Maildata = new MaildataHelper($this->View);
    }

    /**
     * tear down
     */
    public function tearDown():void
    {
//        unset($this->Maildata);
        parent::tearDown();
    }
    /**
     * メール表示用のデータを出力する
     *
     * public function testControl() {
     * $this->markTestIncomplete('このメソッドは、同一クラス内のメソッドをラッピングしているメソッドのためスキップします。');
     * }
     */

    /**
     * メール表示用のデータを出力する
     * @dataProvider toDisplayStringProvider
     */
    public function testToDisplayString($type, $value, $options, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        if ($type == 'file') {
            $this->View->set('mailContent', ['MailContent' => ['id' => 1]]);
        }

        $result = $this->Maildata->toDisplayString($type, $value, $options);
        $this->assertEquals($result, $expected);
    }

    public static function toDisplayStringProvider()
    {
        $options = [
            '資料請求' => '資料請求',
            'お問い合わせ' => 'お問い合わせ',
            'その他' => 'その他'
        ];
        $get = [
            'hoge',
            'hello',
            'world'
        ];
        return [
            ['text', 'hoge', '', 'hoge'],
            ['textarea', 'hoge', '', 'hoge'],
            ['email', 'hoge', '', 'hoge'],
            ['hidden', 'hoge', '', 'hoge'],
            ['radio', '', '', ''],
            ['radio', '', $options, ''],
            ['radio', 'hoge', $options, 'hoge'],
            ['radio', 'h', $options, 'h'],
            ['select', '', '', ''],
            ['select', '', $options, ''],
            ['select', 'hoge', $options, 'hoge'],
            ['select', 'h', $options, 'h'],
            ['pref', '', '', ''],
            ['pref', '東京都', '', '東京都'],
            ['pref', '福岡県', '', '福岡県'],
            ['check', '', '', ''],
            ['check', '', $options, ''],
            ['check', 'hoge', '', 'hoge'],
            ['check', 'hoge', $options, 'hoge'],
            ['multi_check', '', '', ''],
            ['multi_check', '', $options, ''],
            ['multi_check', $get, $options, "・hoge\n ・hello\n ・world\n"],
            ['file', 'hoge', $options, '<a href="/admin/mail_messages/attachment/1/hoge">hoge</a>'],
            ['file', 'test/hoge.jpg', $options, '<a href="/admin/mail_messages/attachment/1/test/hoge.jpg" target="_blank"><img src="/admin/mail_messages/attachment/1/test/hoge.jpg" width="400" alt=""/></a>'],
            //TODO 西暦のオーバーフロー処理ができてない
            ['date_time_calender', 'hoge', $options, '1970年 01月 01日'],
            ['date_time_calender', '21000828', $options, '2100年 08月 28日'],
            ['date_time_calender', '2100/08/32', $options, '1970年 01月 01日'],
            ['date_time_calender', '', $options, ''],
            ['autozip', '888-0000', $options, '888-0000'],
            ['autozip', '8880000', $options, '888-0000'],
            ['', 'hoge', $options, 'hoge']
        ];
    }
}
