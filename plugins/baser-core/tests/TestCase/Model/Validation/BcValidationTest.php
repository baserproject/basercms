<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Validation;

use BaserCore\Model\Validation\BcValidation;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Table\AppTable;

/**
 * Class BcValidationTest
 * @package BaserCore\Test\TestCase\Model\Validation
 * @property BcValidation $BcValidation
 */
class BcValidationTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var BcValidation
     */
    public $BcValidation;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcValidation = new BcValidation();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcValidation);
        parent::tearDown();
    }

    /**
     * Test alphaNumericPlus
     *
     * @return void
     */
    public function testAlphaNumericPlus()
    {
        $alpha = implode('', array_merge(range('a', 'z'), range('A', 'Z')));
        $numeric = implode('', range(0, 9));
        $mark = '-_';
        $allowedChars = $alpha . $numeric . $mark;

        $this->assertEquals(true, $this->BcValidation->alphaNumericPlus(null));
        $this->assertEquals(true, $this->BcValidation->alphaNumericPlus($allowedChars));
        $this->assertEquals(false, $this->BcValidation->alphaNumericPlus($allowedChars . '!'));
        $this->assertEquals(true, $this->BcValidation->alphaNumericPlus($allowedChars . '!', '!'));
        $this->assertEquals(true, $this->BcValidation->alphaNumericPlus($allowedChars . '!', ['!']));
    }

    /**
     * Test alphaNumericDashUnderscore
     *
     * @return void
     */
    public function testAlphaNumericDashUnderscore()
    {
        $alpha = implode('', array_merge(range('a', 'z'), range('A', 'Z')));
        $numeric = implode('', range(0, 9));
        $mark = '-_';
        $allowedChars = $alpha . $numeric . $mark;

        $this->assertEquals(true, $this->BcValidation->alphaNumericDashUnderscore($allowedChars));
        $this->assertEquals(false, $this->BcValidation->alphaNumericDashUnderscore($allowedChars . '!'));
    }

    /**
     * Test bcUtileUrlencodeBlank
     *
     * @return
     */
    public function testBcUtileUrlencodeBlank()
    {
        $disallowedChars = '\\\'|`^"(){}[];/?:@&=+$,%<>#! 　';

        $this->assertEquals(true, $this->BcValidation->bcUtileUrlencodeBlank(null));
        $this->assertEquals(true, $this->BcValidation->bcUtileUrlencodeBlank($disallowedChars));
        $this->assertEquals(true, $this->BcValidation->bcUtileUrlencodeBlank($disallowedChars . '_'));
    }

    /**
     * Test minLength
     *
     * @return void
     */
    public function testMinLength()
    {
        $value = 'テスト';

        $this->assertEquals(true, $this->BcValidation->minLength($value, 3));
        $this->assertEquals(true, $this->BcValidation->minLength([$value], 3));
        $this->assertEquals(false, $this->BcValidation->minLength($value, 4));
        $this->assertEquals(false, $this->BcValidation->minLength([$value], 4));
    }

    /**
     * Test maxLength
     *
     * @return void
     */
    public function testMaxLength()
    {
        $value = 'テスト';

        $this->assertEquals(true, $this->BcValidation->maxLength($value, 3));
        $this->assertEquals(true, $this->BcValidation->maxLength([$value], 3));
        $this->assertEquals(false, $this->BcValidation->maxLength($value, 2));
        $this->assertEquals(false, $this->BcValidation->maxLength([$value], 2));
    }

    /**
     * Test maxByte
     *
     * @return void
     */
    public function testMaxByte()
    {
        $value = 'テスト';

        $this->assertEquals(true, $this->BcValidation->maxByte($value, 9));
        $this->assertEquals(true, $this->BcValidation->maxByte([$value], 9));
        $this->assertEquals(false, $this->BcValidation->maxByte($value, 8));
        $this->assertEquals(false, $this->BcValidation->maxByte([$value], 8));
    }

    /**
     * Test notInList
     *
     * @return void
     */
    public function testNotInList()
    {
        $this->assertEquals(true, $this->BcValidation->notInList('test1', ['test2']));
        $this->assertEquals(false, $this->BcValidation->notInList('test1', ['test1']));
    }

    /**
     * Test fileCheck
     *
     * @return void
     */
    public function testFileCheck()
    {
        $AppTable = new AppTable();
        $uploadMaxSize = $AppTable->convertSize(ini_get('upload_max_filesize'));
        $overSize = $uploadMaxSize + 1;

        $this->assertEquals(true, $this->BcValidation->fileCheck(null, 1));
        $this->assertEquals(true, $this->BcValidation->fileCheck('file', 1));
        $this->assertEquals(true, $this->BcValidation->fileCheck(['error' => 0], 1));
        $this->assertEquals(true, $this->BcValidation->fileCheck(['error' => 4], 1));
        $this->assertEquals(true, $this->BcValidation->fileCheck(['name' => 'file', 'size' => 1], 1));
        $this->assertIsString($this->BcValidation->fileCheck(['error' => 1], 1));
        $this->assertIsString($this->BcValidation->fileCheck(['error' => 2], 1));
        $this->assertIsString($this->BcValidation->fileCheck(['error' => 3], 1));
        $this->assertIsString($this->BcValidation->fileCheck(['error' => 6], 1));
        $this->assertIsString($this->BcValidation->fileCheck(['error' => 7], 1));
        $this->assertIsString($this->BcValidation->fileCheck(['error' => 8], 1));
        $this->assertIsString($this->BcValidation->fileCheck(['name' => 'file', 'size' => 0], 1));
        $this->assertIsString($this->BcValidation->fileCheck(['name' => 'file', 'size' => 2], 1));
        $this->assertIsString($this->BcValidation->fileCheck(['name' => 'file', 'size' => $overSize], $overSize));
    }

    /**
     * Test fileExt
     *
     * @return void
     */
    public function testFileExt()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test notFileEmpty
     *
     * @return void
     */
    public function testNotFileEmpty()
    {
        $this->assertEquals(true, $this->BcValidation->notFileEmpty(['size' => 1]));
        $this->assertEquals(true, $this->BcValidation->notFileEmpty('file'));
        $this->assertEquals(false, $this->BcValidation->notFileEmpty(null));
        $this->assertEquals(false, $this->BcValidation->notFileEmpty(['size' => 0]));
    }

    /**
     * Test confirm
     *
     * @return void
     */
    public function testConfirm()
    {
        $context = [
            'data' => [
                'field0' => true,
                'field1' => true,
                'field2' => false
            ]
        ];

        $this->assertEquals(true, $this->BcValidation->confirm(true, ['field0', 'field1'], $context));
        $this->assertEquals(false, $this->BcValidation->confirm(true, ['field0', 'field2'], $context));
        $this->assertEquals(false, $this->BcValidation->confirm(true, ['field0', 'nofield'], $context));
        $this->assertEquals(true, $this->BcValidation->confirm(true, 'field0', $context));
        $this->assertEquals(true, $this->BcValidation->confirm(true, ['field0'], $context));
        $this->assertEquals(false, $this->BcValidation->confirm(true, 'field2', $context));
        $this->assertEquals(false, $this->BcValidation->confirm(true, ['field2'], $context));
        $this->assertEquals(false, $this->BcValidation->confirm(true, null, $context));
    }

    /**
     * Test emails
     *
     * @return void
     */
    public function testEmails()
    {
        $this->assertEquals(true, $this->BcValidation->emails('test@example.com'));
        $this->assertEquals(true, $this->BcValidation->emails('test1@example.com,test1@example.com'));
        $this->assertEquals(false, $this->BcValidation->emails('test@@example.com'));
        $this->assertEquals(false, $this->BcValidation->emails('test@example.com,test@@example.com'));
    }

    /**
     * Test notEmptyMultiple
     *
     * @return void
     */
    public function testNotEmptyMultiple()
    {
        $value = [
            '_ids' => [
                'check0' => true,
                'check1' => false
            ],
            'check2' => true,
            'check3' => false
        ];

        $this->assertEquals(false, $this->BcValidation->notEmptyMultiple(['_ids' => true], []));
        $this->assertEquals(false, $this->BcValidation->notEmptyMultiple('value', []));
        $this->assertEquals(true, $this->BcValidation->notEmptyMultiple($value, []));

        unset($value['_ids']['check0']);
        $this->assertEquals(false, $this->BcValidation->notEmptyMultiple($value, []));

        unset($value['_ids']);
        $this->assertEquals(true, $this->BcValidation->notEmptyMultiple($value, []));

        unset($value['check2']);
        $this->assertEquals(false, $this->BcValidation->notEmptyMultiple($value, []));
    }

    /**
     * Test halfText
     *
     * @return void
     */
    public function testHalfText()
    {
        $halfText = 'test';
        $mbText = mb_convert_kana($halfText, 'A');

        $this->assertEquals(true, $this->BcValidation->halfText($halfText));
        $this->assertEquals(false, $this->BcValidation->halfText($mbText));
    }

    /**
     * Test CheckDate
     *
     * @return void
     */
    public function testCheckDate()
    {
        $this->assertEquals(true, $this->BcValidation->checkDate(null));
        $this->assertEquals(true, $this->BcValidation->checkDate('2021-01-01'));
        $this->assertEquals(true, $this->BcValidation->checkDate('2021-01-01 00:00:00'));
        $this->assertEquals(false, $this->BcValidation->checkDate('2021-01-32'));
        $this->assertEquals(false, $this->BcValidation->checkDate('2021-01-32 00:00:00'));
        $this->assertEquals(false, $this->BcValidation->checkDate('2021-01-01 00:60:00'));
        $this->assertEquals(false, $this->BcValidation->checkDate('1970-01-01 09:00:00'));
    }

    /**
     * Test checkDateRenge
     *
     * @return void
     */
    public function testCheckDateRenge()
    {
        $value   = null;
        $context = [
            'data' => [
                'begin' => '2020-01-01 00:00:00',
                'end'   => '2021-01-01 00:00:00'
            ]
        ];

        $this->assertEquals(true, $this->BcValidation->checkDateRenge($value, 'begin', 'end', $context));
        $this->assertEquals(true, $this->BcValidation->checkDateRenge($value, 'test', 'end', $context));
        $this->assertEquals(true, $this->BcValidation->checkDateRenge($value, 'begin', 'test', $context));

        $context['data']['end'] = '2020-01-01 00:00:00';
        $this->assertEquals(false, $this->BcValidation->checkDateRenge($value, 'begin', 'end', $context));

        $context['data']['end'] = '2019-01-01 00:00:00';
        $this->assertEquals(false, $this->BcValidation->checkDateRenge($value, 'begin', 'end', $context));
    }

    /**
     * Test checkDateAfterThan
     *
     * @return void
     */
    public function testCheckDateAfterThan()
    {
        $context = [
            'data' => [
                'target' => '2020-01-01 00:00:00'
            ]
        ];

        $this->assertEquals(true, $this->BcValidation->checkDateAfterThan(null, 'target', $context));
        $this->assertEquals(true, $this->BcValidation->checkDateAfterThan('2021-01-01 00:00:00', 'test', $context));
        $this->assertEquals(true, $this->BcValidation->checkDateAfterThan('2021-01-01 00:00:00', 'target', $context));
        $this->assertEquals(true, $this->BcValidation->checkDateAfterThan(['2021-01-01 00:00:00'], 'target', $context));
        $this->assertEquals(false, $this->BcValidation->checkDateAfterThan('2020-01-01 00:00:00', 'target', $context));
        $this->assertEquals(false, $this->BcValidation->checkDateAfterThan('2019-01-01 00:00:00', 'target', $context));
    }

    /**
     * Test containsScript
     *
     * @return void
     */
    public function testContainsScript()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
