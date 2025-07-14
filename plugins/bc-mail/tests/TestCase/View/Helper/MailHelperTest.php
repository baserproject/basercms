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
use BcMail\Test\Scenario\MailContentsScenario;
use BcMail\View\Helper\MailHelper;
use Cake\ORM\Entity;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class MailHelperTest
 *
 * @property MailHelper $MailHelper
 */
class MailHelperTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        SiteFactory::make(['id' => '1'])->persist();
        $view = new BcFrontAppView($this->getRequest('/'));
        $this->MailHelper = new MailHelper($view);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        unset($this->MailHelper);
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
     * 送信完了文を取得する
     */
    public function testGetThanks()
    {
        $this->MailHelper->currentMailContent = new Entity(['thanks' => 'ありがとうございました。']);
        $result = $this->MailHelper->getThanks();
        $this->assertEquals($result, 'ありがとうございました。');
    }

    /**
     * 送信完了文の存在確認
     * @param $thanks
     * @param $expected
     * @dataProvider ThanksExistsProvider
     */
    public function testThanksExists($thanks, $expected)
    {
        //setUp
        $mailContent = new Entity([
            'thanks' => $thanks
        ]);
        $this->MailHelper->currentMailContent = $mailContent;

        $result = $this->MailHelper->thanksExists();

        $this->assertEquals($expected, $result);
    }


    public static function thanksExistsProvider()
    {
        return [
            ['This is a test thanks', true],
            ['', false],
            [null, false],
            ['0', false],
            ['   ', true],
            ['<p>This is a <strong>HTML</strong> thanks</p>', true],
        ];
    }

    /**
     * 受付中止文を取得する
     */
    public function testGetUnpublish()
    {
        $this->MailHelper->currentMailContent = new Entity(['unpublish' => '受付中止です。']);
        $result = $this->MailHelper->getUnpublish();
        $this->assertEquals($result, '受付中止です。');
    }

    /**
     * 受付中止文の存在確認
     * @param $unpublish
     * @param $expected
     * @dataProvider ThanksExistsProvider
     */
    public function testUnpublishExists($unpublish, $expected)
    {
        //setUp
        $mailContent = new Entity([
            'unpublish' => $unpublish
        ]);
        $this->MailHelper->currentMailContent = $mailContent;

        $result = $this->MailHelper->unpublishExists();

        $this->assertEquals($expected, $result);
    }


    public static function unpublishExistsProvider()
    {
        return [
            ['This is a test unpublish', true],
            ['', false],
            [null, false],
            ['0', false],
            ['   ', true],
            ['<p>This is a <strong>HTML</strong> unpublish</p>', true],
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
        $result = $this->MailHelper->getFormTemplates(1);
        $this->assertEquals(['default' => 'default'], $result);
    }

    /**
     * メールテンプレートを取得
     */
    public function testGetMailTemplates()
    {
        $result = $this->MailHelper->getMailTemplates(1);
        $this->assertEquals(['mail_default' => 'mail_default'], $result);
    }

    /**
     * ブラウザの戻るボタンを取得
     */
    public function testGetToken()
    {
        $result = $this->MailHelper->getToken();
        $expected = '/<script.*<\/script>.*/s';
        $this->assertMatchesRegularExpression($expected, $result);
    }

    /**
     * メールフォームへのリンクを取得
     * @dataProvider linkProvider
     */
    public function testLink($title, $contentsName, $url, $expected)
    {
        ContentFactory::make(['plugin' => 'BcMail', 'type' => 'MailContent', 'name' => $contentsName, 'url' => $url, 'entity_id' => 1])->persist();
        $this->expectOutputString($expected);
        $this->MailHelper->link($title, $contentsName);
    }

    public static function linkProvider()
    {
        return [
            ['タイトル', '/Members/', '/a/', '<a href="/a/index">タイトル</a>'],
            [' ', 'a', '/a/', '<a href="/a/index"> </a>'],
            [' ', ' ', '', '<a href="/bc-mail/mail"> </a>'],
            [' ', '///', '///', '<a href="/index"> </a>'],
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
     * test thanksExists
     */
    public function test_thanksExists()
    {
        $mailContent = new MailContent();
        $mailContent->thanks = 'test thanks';

        //with thanks
        $this->MailHelper->currentMailContent = $mailContent;
        $rs = $this->MailHelper->thanksExists();
        $this->assertTrue($rs);

        //with empty thanks
        $mailContent->thanks = '';
        $this->MailHelper->currentMailContent = $mailContent;
        $rs = $this->MailHelper->thanksExists();
        $this->assertFalse($rs);
    }

    /**
     * test testGetThanks
     */
    public function test_getThanks()
    {
        $mailContent = new MailContent();
        $mailContent->thanks = 'test thanks';

        $this->MailHelper->currentMailContent = $mailContent;

        $rs = $this->MailHelper->getThanks();
        $this->assertEquals('test thanks', $rs);
    }

    /**
     * test unpublishExists
     */
    public function test_unpublishExists()
    {
        $mailContent = new MailContent();
        $mailContent->unpublish = 'test unpublish';

        //with unpublish
        $this->MailHelper->currentMailContent = $mailContent;
        $rs = $this->MailHelper->unpublishExists();
        $this->assertTrue($rs);

        //with empty unpublish
        $mailContent->unpublish = '';
        $this->MailHelper->currentMailContent = $mailContent;
        $rs = $this->MailHelper->unpublishExists();
        $this->assertFalse($rs);
    }

    /**
     * test testGetUnpublish
     */
    public function test_getUnpublish()
    {
        $mailContent = new MailContent();
        $mailContent->unpublish = 'test unpublish';

        $this->MailHelper->currentMailContent = $mailContent;

        $rs = $this->MailHelper->getUnpublish();
        $this->assertEquals('test unpublish', $rs);
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

    /**
     * test getPublishedMailContents
     */
    public function testGetPublishedMailContents()
    {
        $this->loadFixtureScenario(MailContentsScenario::class);
        $rs = $this->MailHelper->getPublishedMailContents(1);
        $this->assertEquals(1, $rs->count());
    }
}
