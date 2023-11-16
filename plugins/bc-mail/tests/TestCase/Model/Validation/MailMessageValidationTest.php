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
use BcMail\Model\Validation\MailMessageValidation;

/**
 * Class BlogCategoryTest
 *
 * @property MailMessageValidation $MailMessageValidation
 */
class MailMessageValidationTest extends BcTestCase
{

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
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
     * checkDate test
     */
    public function testCheckDate()
    {
        $date_time = '';
        $result = $this->MailMessageValidation->checkdate($date_time);
        $this->assertTrue($result);

        $date_time = '2023-01-01 09:01';
        $result = $this->MailMessageValidation->checkdate($date_time);
        $this->assertTrue($result);

        $date_time = '1970-01-01 09:00:00';
        $result = $this->MailMessageValidation->checkdate($date_time);
        $this->assertFalse($result);

        $date_time = '1990-01-01 09';
        $result = $this->MailMessageValidation->checkdate($date_time);
        $this->assertFalse($result);

        $date_time = '1990-01-00 09:00';
        $result = $this->MailMessageValidation->checkdate($date_time);
        $this->assertFalse($result);

        $date_time = '20a10-01-01 09:00:00';
        $this->expectExceptionMessage('checkdate(): Argument #3 ($year) must be of type int, string given');
        $this->MailMessageValidation->checkdate($date_time);

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

    /**
     * checkSame test
     */
    public function testCheckSame()
    {
        $value = 'a';
        $target = 'name';
        $context['data']['name'] = 'a';
        $result = $this->MailMessageValidation->checkSame($value, $target, $context);
        $this->assertTrue($result);

        $value = 'b';
        $result = $this->MailMessageValidation->checkSame($value, $target, $context);
        $this->assertFalse($result);

        $context = [];
        $result = $this->MailMessageValidation->checkSame($value, $target, $context);
        $this->assertFalse($result);
    }


    /**
     * dateString test
     */
    public function testDateString()
    {
        $date = '1970-01-01';
        $result = $this->MailMessageValidation->dateString($date);
        $this->assertFalse($result);

        $date = '1990-01-01';
        $result = $this->MailMessageValidation->dateString($date);
        $this->assertTrue($result);

        $date = '1999/01/01';
        $result = $this->MailMessageValidation->dateString($date);
        $this->assertTrue($result);

        $date = '19a9/01/0a';
        $result = $this->MailMessageValidation->dateString($date);
        $this->assertFalse($result);
    }
}
