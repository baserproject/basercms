<?php
namespace BcMail\Test\TestCase\View\Helper;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcFrontAppView;
use BcMail\View\Helper\MaildataHelper;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MaildataHelperTest extends BcTestCase
{

    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $view = new BcFrontAppView($this->getRequest('/'));
        $this->MaildataHelper = new MaildataHelper($view);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        unset($this->MaildataHelper);
        parent::tearDown();
    }

    /**
     * メール表示用のデータを出力する
     * @dataProvider controlDataProvider
     */
    public function testControl($type, $value, $escape, $expected)
    {
        $result = $this->MaildataHelper->control($type, $value, $escape);
        $this->assertEquals($expected, $result);
    }

    public static function controlDataProvider()
    {
        return [
            ['text', '<b>bold</b>', true, ' &lt;b&gt;bold&lt;/b&gt;'],
            ['text', '<b>bold</b>', false, ' <b>bold</b>'],
        ];
    }

    /**
     * メール表示用のデータを出力する
     * @dataProvider toDisplayStringProvider
     */
    public function testToDisplayString($type, $value, $options, $expected)
    {
        $result = $this->MaildataHelper->toDisplayString($type, $value, $options);
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
            ['radio', $options, '', '・資料請求
・お問い合わせ
・その他
'],
            ['radio', $options, true, ' ・資料請求
 ・お問い合わせ
 ・その他
'],
            ['radio', $options, false, '・資料請求
・お問い合わせ
・その他
'],
            ['select', '', '', ''],
            ['select', $options, '', '・資料請求
・お問い合わせ
・その他
'],
            ['select', $options, '', '・資料請求
・お問い合わせ
・その他
'],
            ['pref', '', '', ''],
            ['pref', '東京都', '', '東京都'],
            ['pref', '福岡県', '', '福岡県'],
            ['check', '', '', ''],
            ['check', $options, '', '・資料請求
・お問い合わせ
・その他
'],
            ['check', 'hoge', '', 'hoge'],
            ['check', $options, '', '・資料請求
・お問い合わせ
・その他
'],
            ['multi_check', '', '', ''],
            ['multi_check', $options, '', '・資料請求
・お問い合わせ
・その他
'],
            ['multi_check', $get, '', "・hoge
・hello
・world
"],
//            ['file', 'hoge', '', '<a href="/admin/mail_messages/attachment/1/hoge">hoge</a>'],
            ['date_time_calender', 'hoge', '', '1970年 01月 01日'],
            ['date_time_calender', '21000828', '', '2100年 08月 28日'],
            ['date_time_calender', '2100/08/32', '', '1970年 01月 01日'],
            ['date_time_calender', '', '', ''],
            ['autozip', '888-0000', '', '888-0000'],
            ['autozip', '8880000', '', '888-0000'],
            ['', 'hoge', '', 'hoge']
        ];
    }
}
