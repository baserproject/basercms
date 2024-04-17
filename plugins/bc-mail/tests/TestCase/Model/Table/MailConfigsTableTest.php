<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcMail\Test\TestCase\Model\Table;

use BaserCore\TestSuite\BcTestCase;
use BcMail\Model\Table\MailConfigsTable;

/**
 * @property MailConfigsTable $MailConfigsTable
 */
class MailConfigsTableTest extends BcTestCase
{
    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->MailConfigsTable = $this->getTableLocator()->get('BcMail.MailConfigs');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->MailConfigsTable);
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertTrue($this->MailConfigsTable->hasBehavior('BcKeyValue'));
    }

    /**
     * test validationDefault
     */
    public function test_validationDefault()
    {
        $validator = $this->MailConfigsTable->getValidator('default');

        //設定名が指定しない場合、
        $errors = $validator->validate([
            'name' => ''
        ]);
        //戻り値を確認
        $this->assertEquals('設定名を入力してください。', current($errors['name']));

        //maxLength　テスト、
        $errors = $validator->validate([
            'name' => str_repeat('a', 256),
            'value' => str_repeat('a', 65536)
        ]);
        //戻り値を確認
        $this->assertEquals('255文字以内で入力してください。', current($errors['name']));
        $this->assertEquals('65535文字以内で入力してください。', current($errors['value']));
    }

    /**
     * test validationKeyValue
     */
    public function test_validationKeyValue()
    {
        $validator = $this->MailConfigsTable->getValidator('keyValue');
        //入力しない場合、
        $errors = $validator->validate([
            'site_name' => '',
        ]);
        $this->assertEquals('Webサイト名を入力してください。', current($errors['site_name']));

        //スペースだけ入力する場合、
        $errors = $validator->validate([
            'site_name' => '    ',
        ]);
        $this->assertEquals('Webサイト名を入力してください。', current($errors['site_name']));

        //文字を入力する場合、
        $errors = $validator->validate([
            'site_name' => '    a',
        ]);
        $this->assertArrayNotHasKey('site_name', $errors);
    }
}
