<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Test.Case.View.Helper
 * @since			baserCMS v 4.0.3
 * @license			http://basercms.net/license/index.html
 */

App::uses('MailHelper', 'Mail.View/Helper');

/**
 * Class MailHelperTest
 */
class MailHelperTest extends BaserTestCase {

/**
 * Fixture
 *
 * @var array
 */
    public $fixtures = array (
        'plugin.Mail.Default/MailContent'
    );

/**
 * set up
 */
    public function setUp() {
        parent::setUp();
        $this->Mail = new MailHelper(new View(null));
    }

/**
 * tear down
 */
    public function tearDown() {
        unset($this->Mail);
        parent::tearDown();
    }

/**
 * 説明文の取得結果
 */
    public function testDescription() {
        $this->markTestIncomplete('このメソッドは、同一クラス内のメソッドをラッピングしているメソッドの為スキップします。');
    }

/**
 * 説明文を取得する
 */
    public function testGetDescription() {
        ClassRegistry::flush();
        $this->Mail->setMailContent(1);
        $expected = '<p><span style="color:#C30">*</span> 印の項目は必須となりますので、必ず入力してください。</p>';
        $result = $this->Mail->getDescription();
        $this->assertEquals($result, $expected, "説明文の取得結果が違います。");
    }

/**
 * 説明文の存在確認
 */
    public function testDescriptionExists() {
        $this->Mail->setMailContent(1);
        $result = $this->Mail->descriptionExists();
        $this->assertTrue($result, "メールの説明文が指定されていません。");
    }

/**
 * メールフォームを取得
 */
    public function testGetForm() {
        //$result = $this->Mail->getForm();
        //var_dump($result);
    }


}