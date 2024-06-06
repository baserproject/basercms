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
use BcMail\Model\Entity\MailField;
use BcMail\View\Helper\MailfieldHelper;
use Cake\View\View;

class MailfieldHelperTest extends BcTestCase
{

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
     * @dataProvider getOptionsDataProvider
     */
    public function testGetOptions($source, $type, $expected)
    {
        $mailField = new MailField();
        $mailField->source = $source;
        $mailField->type = $type;

        $result = $this->mailfieldHelper->getOptions($mailField);
        $this->assertEquals($expected, $result);
    }

    public static function getOptionsDataProvider()
    {
        return [
            ["option1|option2|option3", "not_check", ['option1' => 'option1', 'option2' => 'option2', 'option3' => 'option3']],
            ["option1|option2|option3", "check", []],
        ];
    }
}
