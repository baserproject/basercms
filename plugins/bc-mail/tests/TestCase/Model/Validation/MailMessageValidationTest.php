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

namespace BcMail\Test\TestCase\Model\Validation;

use BaserCore\TestSuite\BcTestCase;
use BcMail\Model\Table\MailAppTable;
use BcMail\Model\Table\MailConfigsTable;
use BcMail\Model\Table\MailContentsTable;
use BcMail\Model\Table\MailFieldsTable;
use BcMail\Model\Table\MailMessagesTable;
use BcMail\Model\Validation\MailMessageValidation;
use BcMail\Test\Factory\MailContentFactory;
use BcMail\Test\Factory\MailFieldsFactory;

/**
 * Class BlogCategoryTest
 *
 * @property MailMessageValidation $MailMessageValidation
 */
class MailMessageValidationTest extends BcTestCase
{

    public $fixtures = [
        'plugin.BcMail.Factory/MailContents',
        'plugin.BcMail.Factory/MailFields',
    ];

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->MailMessageValidation = new MailMessageValidation();
    }

    /**
     * Tear down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * dateArray test
     */
    public function testDateArray()
    {
        $date['year'] = '1';
        $date['month'] = '1';
        $date['day'] = '1';
        $result = $this->MailMessageValidation->dateArray($date);
        $this->assertTrue($result);

        $date['day'] = '';
        $result = $this->MailMessageValidation->dateArray($date);
        $this->assertFalse($result);

        $date['month'] = '';
        $result = $this->MailMessageValidation->dateArray($date);
        $this->assertFalse($result);

        $date['year'] = '';
        $result = $this->MailMessageValidation->dateArray($date);
        $this->assertFalse($result);
    }
}
