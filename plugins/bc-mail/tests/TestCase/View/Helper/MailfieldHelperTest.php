<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */
namespace BcMail\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcMail\Test\Factory\MailFieldsFactory;
use BcMail\Test\Scenario\MailFieldsScenario;
use BcMail\View\Helper\MailfieldHelper;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MailfieldHelperTest extends BcTestCase
{
    use ScenarioAwareTrait;
    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->mailfieldHelper = new MailfieldHelper(new View());
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        unset($this->mailfieldHelper);
        parent::tearDown();
    }

    /**
     * htmlの属性を取得する
     */
    public function testGetAttributes()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コントロールのソースを取得する
     */
    public function testGetOptions()
    {
        //with source data not empty
        $mailField = MailFieldsFactory::make(['source' => '資料請求|問い合わせ|その他'])->getEntity();
        $result = $this->mailfieldHelper->getOptions($mailField);
        $this->assertEquals(['資料請求' => '資料請求', '問い合わせ' => '問い合わせ', 'その他' => 'その他'], $result);

        //with source data empty
        $mailField = MailFieldsFactory::make(['source' => ''])->getEntity();
        $result = $this->mailfieldHelper->getOptions($mailField);
        $this->assertEquals([], $result);
    }
}
