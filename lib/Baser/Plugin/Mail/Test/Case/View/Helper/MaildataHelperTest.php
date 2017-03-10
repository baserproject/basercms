<?php

App::uses('MaildataHelper', 'Mail.View/Helper');
App::uses('BcAppView', 'View');

class MaildataHelperTest extends BaserTestCase {

    public $fixtures = array(
        'baser.Default.Content',
        'baser.Default.Site',
        'baser.Default.User',
        'baser.Default.SiteConfig'
    );
/**
 * set up
 */
    public function setUp() {
        parent::setUp();
        $this->View = new BcAppView(null);
        $this->View->request = $this->_getRequest('/');
        $this->Maildata = new MaildataHelper($this->View);
    }

/**
 * tear down
 */
    public function tearDown() {
        unset($this->Maildata);
        parent::tearDown();
    }
/**
 * メール表示用のデータを出力する
 */
    public function testControl() {
        $this->markTestIncomplete('このメソッドは、同一クラス内のメソッドをラッピングしているメソッドの為スキップします。');
    }

/**
 * メール表示用のデータを出力する
 * @dataProvider toDisplayStringProvider
 */
    public function testToDisplayString ($type, $value,$expected) {
        $result = $this->Maildata->toDisplayString($type, $value, $options = "");
        $this->assertEquals($result,$expected);

        print_r($result);

    }

    public function toDisplayStringProvider() {
        return [
            ['text', 'hoge', 'hoge'],
            ['textarea', 'hoge', 'hoge'],
            ['email', 'hoge', 'hoge'],
            ['hidden', 'hoge', 'hoge'],
            ['radio', 'hoge', ''],
            ['select', 'hoge', ''],
            ['pref', 'hoge', ''],
            ['check', 'hoge', ''],
            ['multi_check', 'hoge', ''],
            ['fire', 'hoge', 'hoge'],
            ['date_time_calender', 'hoge', '1970年 01月 01日'],
            ['date_time', 'hoge', 'hoge'],
            ['autozip', 'hoge', 'hoge'],
            ['', 'hoge', 'hoge']
        ];
    }
}