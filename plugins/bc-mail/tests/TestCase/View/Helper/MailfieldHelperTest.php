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
     * @dataProvider getAttributesProvider
     */
    public function testGetAttributes($expected, $data)
    {
        $this->assertEquals($expected, $this->mailfieldHelper->getAttributes($data));
    }

    public static function getAttributesProvider()
    {
        return [
            [['size' => 10, 'rows' => 5, 'maxlength' => 100, 'class' => 'test', 'autocomplete' => 'on', 'type' => 'tel'], ['MailField' => ['size' => 10, 'text_rows' => 5, 'maxlength' => 100, 'class' => 'test', 'autocomplete' => 'on', 'type' => 'tel', 'options' => '']]],
            [['size' => 10, 'rows' => 5, 'maxlength' => 100, 'class' => 'test', 'autocomplete' => 'on', 'multiple' => true], ['MailField' => ['size' => 10, 'text_rows' => 5, 'maxlength' => 100, 'class' => 'test', 'autocomplete' => 'on', 'type' => 'multi_check', 'options' => '']]],
            [['size' => 10, 'rows' => 5, 'maxlength' => 100, 'class' => 'test', 'autocomplete' => 'on', 'type' => 'number'], ['MailField' => ['size' => 10, 'text_rows' => 5, 'maxlength' => 100, 'class' => 'test', 'autocomplete' => 'on', 'type' => 'number', 'options' => '']]],
            [['size' => 10, 'rows' => 5, 'maxlength' => 100, 'class' => 'test', 'autocomplete' => 'on', 'type' => 'number', 'key1' => 'value1', 'key2' => 'value2', 'key3' => null], ['MailField' => ['size' => 10, 'text_rows' => 5, 'maxlength' => 100, 'class' => 'test', 'autocomplete' => 'on', 'type' => 'number', 'options' => 'key1|value1|key2|value2|key3']]],
        ];
    }

    /**
     * コントロールのソースを取得する
     */
    public function testGetOptions()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
