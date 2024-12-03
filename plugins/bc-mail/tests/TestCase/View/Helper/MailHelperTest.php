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
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcFrontAppView;
use BcMail\Model\Entity\MailContent;
use BcMail\View\Helper\MailHelper;
use Cake\ORM\Entity;
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
     * @param $description
     * @param $expected
     * @dataProvider descriptionExistsProvider
     */
    public function testDescriptionExists($description, $expected)
    {
        //setUp
        $mailContent = new Entity([
            'description' => $description
        ]);
        $this->MailHelper->currentMailContent = $mailContent;

        $result = $this->MailHelper->descriptionExists();

        $this->assertEquals($expected, $result);
    }

    public static function descriptionExistsProvider()
    {
        return [
            ['This is a test description', true],
            ['', false],
            [null, false],
            ['0', false],
            ['   ', true],
            ['<p>This is a <strong>HTML</strong> description</p>', true],
        ];
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
        SiteFactory::make(['id' => '1'])->persist();
        $view = new BcFrontAppView($this->getRequest('/'));
        $this->MailHelper = new MailHelper($view);
        $result = $this->MailHelper->getFormTemplates(1);
        $this->assertEquals(['default' => 'default'], $result);
    }

    /**
     * メールテンプレートを取得
     */
    public function testGetMailTemplates()
    {
        SiteFactory::make(['id' => '1'])->persist();
        $view = new BcFrontAppView($this->getRequest('/'));
        $this->MailHelper = new MailHelper($view);
        $result = $this->MailHelper->getMailTemplates(1);
        $this->assertEquals(['mail_default' => 'mail_default'], $result);
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
