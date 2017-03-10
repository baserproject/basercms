<?php

class MaildataHelperTest extends BaserTestCase {
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
    public function testToDisplayString () {
        $this->toDisplayString($type, $value, $options = "");

    }

    public function toDisplayStringProvider() {
    }
}