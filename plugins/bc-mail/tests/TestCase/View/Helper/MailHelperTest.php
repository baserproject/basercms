<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.3
 * @license         https://basercms.net/license/index.html
 */
namespace BcMail\Test\TestCase\View\Helper;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\TestSuite\BcTestCase;
use BcMail\Model\Entity\MailContent;
use BcMail\View\Helper\MailHelper;
use Cake\View\View;

/**
 * Class MailHelperTest
 *
 * @property MailHelper $Mail
 */
class MailHelperTest extends BcTestCase
{
    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->MailHelper = new MailHelper(new View());
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
//        unset($this->Mail);
        parent::tearDown();
    }

    /**
     * 説明文の取得結果
     *
     * public function testDescription() {
     * $this->markTestIncomplete('このメソッドは、同一クラス内のメソッドをラッピングしているメソッドのためスキップします。');
     * }
     */

    /**
     * 説明文を取得する
     */
    public function testGetDescription()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        ClassRegistry::flush();
        $this->Mail->setMailContent(1);
        $expected = '<p><span style="color:#C30">*</span> 印の項目は必須となりますので、必ず入力してください。</p>';
        $result = $this->Mail->getDescription();
        $this->assertEquals($result, $expected, "説明文の取得結果が違います。");
    }

    /**
     * 説明文の存在確認
     */
    public function testDescriptionExists()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->Mail->setMailContent(1);
        $result = $this->Mail->descriptionExists();
        $this->assertTrue($result, "メールの説明文が指定されていません。");
    }

    /**
     * メールフォームを取得
     */
    public function testGetForm()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $MailMessage = ClassRegistry::init('BcMail.MailMessage');
        $MailMessage->createTable(1);
        ClassRegistry::flush();
        $result = $this->Mail->getForm();
        $expected = '/.*<form.*<\/form>.*/s';
        $this->assertMatchesRegularExpression($expected, $result, "メールフォームが取得できません。");
    }

    /**
     * メールフォームテンプレートを取得
     */
    public function testGetFormTemplates()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $View = new View(null);
        $View->set('siteConfig', Configure::read('BcSite'));
        $this->Mail->BcBaser = new BcBaserHelper($View);
        $result = $this->Mail->getFormTemplates();
        $expected = [
            'default' => 'default',
            'smartphone' => 'smartphone'
        ];
        $this->assertEquals($result, $expected, 'フォームテンプレートの取得結果が違います。');
    }

    /**
     * メールテンプレートを取得
     */
    public function testGetMailTemplates()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $View = new View(null);
        $View->set('siteConfig', Configure::read('BcSite'));
        $this->Mail->BcBaser = new BcBaserHelper($View);
        $result = $this->Mail->getMailTemplates();
        $expected = [
            'mail_default' => 'mail_default',
            'default' => 'default',
            'reset_password' => 'reset_password',
            'send_activate_url' => 'send_activate_url',
            'send_activate_urls' => 'send_activate_urls',
        ];
        $this->assertEquals($result, $expected, 'メールテンプレートの取得結果が違います。');
    }

    /**
     * ブラウザの戻るボタンを取得
     */
    public function testGetToken()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $result = $this->Mail->getToken();
        $expected = '/<script.*<\/script>.*/s';
        $this->assertMatchesRegularExpression($expected, $result, 'スクリプトが取得できません。');
    }

    /**
     * メールフォームへのリンクを取得
     * @dataProvider linkProvider
     */
    public function testLink($title, $contentsName, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->expectOutputString($expected);
        $this->Mail->link($title, $contentsName, $datas = [], $options = []);
    }

    public static function linkProvider()
    {
        return [
            ['タイトル', 'Members', '<a href="/Members">タイトル</a>'],
            [' ', 'a', '<a href="/a"> </a>'],
            [' ', ' ', '<a href="/ "> </a>'],
            [' ', '///', '<a href="/"> </a>'],
            [' ', '////a', '<a href="/a"> </a>'],
            [' ', '////a//a/aa', '<a href="/a/a/aa"> </a>'],
            [' ', '/../../../../a', '<a href="/../../../../a"> </a>'],
            ['', 'javascript:void(0);', '<a href="/javascript:void(0);"></a>'],
            ['<script>alert(1)</script>', '////a', '<a href="/a"><script>alert(1)</script></a>']
        ];
    }

    /**
     * メールコンテンツデータをセット
     * @dataProvider setMailContentDataProvider
     */
    public function testSetMailContent($id, $expect)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        unset($this->Mail->mailContent);
        $this->Mail->setMailContent($id);
        $this->assertEquals((bool)($this->Mail->mailContent), $expect);
    }

    public static function setMailContentDataProvider()
    {
        return [
            [1, true],
            [2, false]
        ];
    }

    /**
     * ブラウザの戻るボタンの生成結果取得
     *
     * public function testToken() {
     * $this->markTestIncomplete('このメソッドは、同一クラス内のメソッドをラッピングしているメソッドのためスキップします。');
     * }
     */

    /**
     * beforeRender
     */
    public function testBeforeRender()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test descriptionExists
     */
    public function test_descriptionExists()
    {
        $mailContent = new MailContent();
        $mailContent->description = 'test description';

        //with description
        $this->MailHelper->currentMailContent = $mailContent;
        $rs = $this->MailHelper->descriptionExists();
        $this->assertTrue($rs);

        //with empty description
        $mailContent->description = '';
        $this->MailHelper->currentMailContent = $mailContent;
        $rs = $this->MailHelper->descriptionExists();
        $this->assertFalse($rs);
    }

    /**
     * test testGetDescription
     */
    public function test_getDescription()
    {
        $mailContent = new MailContent();
        $mailContent->description = 'test description';

        $this->MailHelper->currentMailContent = $mailContent;

        $rs = $this->MailHelper->getDescription();
        $this->assertEquals('test description', $rs);
    }

    /**
     * test isMail
     */
    public function test_isMail()
    {
        //no content
        $result = $this->MailHelper->isMail();
        $this->assertFalse($result);

        //with content
        $content = ContentFactory::make(['plugin' => 'BcMail'])->getEntity();
        $this->MailHelper->getView()->setRequest($this->getRequest()->withAttribute('currentContent', $content));
        $result = $this->MailHelper->isMail();
        $this->assertTrue($result);
    }
}
