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
use BaserCore\Model\AppTable;

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
     * @param string $value
     * @param mixed $option
     * @param boolean $expect
     * @return void
     * @dataProvider alphaNumericPlusDataProvider
     */
    public function testAlphaNumericPlus($value, $option, $expect)
    {
        $result = $this->BcValidation->alphaNumericPlus($value, $option);
        $this->assertEquals($expect, $result);
    }

    public function alphaNumericPlusDataProvider()
    {
        return [
            ['あいうえお', [], false],
            ['あいうえお', ['あ'], false],
            ['あいうえお', ['あいうえお'], true],
            ['あいうえお_', ['あいうえお'], true],
        ];
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
     * @param string $value
     * @param boolean $expect
     * @return void
     * @dataProvider bcUtileUrlencodeBlankDataProvider
     */
    public function testBcUtileUrlencodeBlank($value, $expect)
    {
        $result = $this->BcValidation->bcUtileUrlencodeBlank($value);
        $this->assertEquals($expect, $result);
    }

    public function bcUtileUrlencodeBlankDataProvider()
    {
        return [
            ['あいうえお', true],
            ['\\', true],
            ['"', false],
            ["^'|`^(){}[];/?:@&=+$,%<>#!", false]
        ];
    }

    /**
     * Test minLength
     *
     * @param mixed $value
     * @param int $min
     * @param boolean $expect
     * @return void
     * @dataProvider minLengthDataProvider
     */
    public function testMinLength($value, $min, $expect)
    {
        $result = $this->BcValidation->minLength($value, $min);
        $this->assertEquals($expect, $result);
    }

    public function minLengthDataProvider()
    {
        return [
            ['あいう', 4, false],
            ['あいう', 3, true],
            [['あいう', 'あいうえお'], 4, false],
        ];
    }

    /**
     * Test maxLength
     *
     * @param mixed $value
     * @param int $max
     * @param boolean $expect
     * @return void
     * @dataProvider maxLengthDataProvider
     */
    public function testMaxLength($value, $max, $expect)
    {
        $result = $this->BcValidation->maxLength($value, $max);
        $this->assertEquals($expect, $result);
    }

    public function maxLengthDataProvider()
    {
        return [
            ['あいう', 4, true],
            ['あいう', 3, true],
            ['あいう', 2, false],
            [['あいう', 'あいうえお'], 4, true],
        ];
    }

    /**
     * Test maxByte
     *
     * @param mixed $value
     * @param int $max
     * @param boolean $expect
     * @return void
     * @dataProvider maxByteDataProvider
     */
    public function testMaxByte($value, $max, $expect)
    {
        $result = $this->BcValidation->maxByte($value, $max);
        $this->assertEquals($expect, $result);
    }

    public function maxByteDataProvider()
    {
        return [
            ['あいう', 10, true],
            ['あいう', 9, true],
            ['あいう', 8, false]
        ];
    }

    /**
     * Test notInList
     *
     * @param string $value
     * @param array $list
     * @param boolean $expect
     * @return void
     * @dataProvider notInListDataProvider
     */
    public function testNotInList($value, $list, $expect)
    {
        $result = $this->BcValidation->notInList($value, $list);
        $this->assertEquals($expect, $result);
    }

    public function notInListDataProvider()
    {
        return [
            ['test1', ['test1', 'test2'], false],
            ['test3', ['test1', 'test2'], true],
        ];
    }

    /**
     * Test fileCheck
     *
     * @return void
     */
    public function testFileCheck()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
     * @param array $file
     * @param boolean $expect
     * @return void
     * @dataProvider notFileEmptyDataProvider
     */
    public function testNotFileEmpty($file, $expect)
    {
        $result = $this->BcValidation->notFileEmpty($file);
        $this->assertEquals($expect, $result);
    }

    public function notFileEmptyDataProvider()
    {
        return [
            [['size' => 0], false],
            [['size' => 100], true],
            [[], false],
        ];
    }

    /**
     * Test confirm
     *
     * @param mixed $value
     * @param mixed $fields
     * @param array $data
     * @param boolean $expect
     * @param string $message
     * @return void
     * @dataProvider confirmDataProvider
     */
    public function testConfirm($value, $fields, $data, $expect, $message = null)
    {
        $context = [
            'data' => $data
        ];

        $result = $this->BcValidation->confirm($value, $fields, $context);
        $this->assertEquals($expect, $result, $message);
    }

    public function confirmDataProvider()
    {
        return [
            ['', ['test1', 'test2'], ['test1' => 'value', 'test2' => 'value'], true, '2つのフィールドが同じ値の場合の判定が正しくありません'],
            ['', ['test1', 'test2'], ['test1' => 'value', 'test2' => 'other_value'], false, '2つのフィールドが異なる値の場合の判定が正しくありません'],
            ['value', 'test', ['test' => 'value'], true, 'フィールド名が一つで同じ値の場合の判定が正しくありません'],
            ['value', 'test', ['test' => 'other_value'], false, 'フィールド名が一つで異なる値の場合の判定が正しくありません'],
        ];
    }

    /**
     * Test emails
     *
     * @param string $value
     * @param boolean $expect
     * @return void
     * @dataProvider emailsDataProvider
     */
    public function testEmails($value, $expect)
    {
        $message = '複数のEメールのバリデーションチェックができません';
        $result = $this->BcValidation->emails($value);
        $this->assertEquals($expect, $result, $message);
    }

    public function emailsDataProvider()
    {
        return [
            ['test1@co.jp', true],
            ['test1@co.jp,test2@cp.jp', true],
            ['test1@cojp,test2@cp.jp', false],
            ['test1@co.jp,test2@cpjp', false],
        ];
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
     * @param string $value
     * @param boolean $expect
     * @return void
     * @dataProvider halfTextDataProvider
     */
    public function testHalfText($value, $expect)
    {
        $result = $this->BcValidation->halfText($value);
        $this->assertEquals($expect, $result);
    }

    public function halfTextDataProvider()
    {
        return [
            ['test', true],
            ['テスト', false],
        ];
    }

    /**
     * Test CheckDate
     *
     * @param string $value
     * @param boolean $expect
     * @return void
     * @dataProvider checkDateDataProvider
     */
    public function testCheckDate($value, $expect)
    {
        $result = $this->BcValidation->checkDate($value);
        $this->assertEquals($expect, $result);
    }

    public function checkDateDataProvider()
    {
        return [
            ['2015-01-01', true],
            ['201511', false],
            ['2015-01-01 00:00:00', true],
            ['2015-0101 00:00:00', false],
            ['1970-01-01 09:00:00', false],
        ];
    }

    /**
     * Test checkDateRenge
     *
     * @return void
     */
    public function testCheckDateRenge()
    {
        $value = null;
        $context = [
            'data' => [
                'begin' => '2020-01-01 00:00:00',
                'end' => '2021-01-01 00:00:00'
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
     * @param string $value
     * @param string $target
     * @param boolean $expect
     * @return void
     * @dataProvider checkDataAfterThanDataProvider
     */
    public function testCheckDateAfterThan($value, $target, $expect)
    {
        $context = [
            'data' => [
                'target' => $target
            ]
        ];

        $result = $this->BcValidation->checkDateAfterThan($value, 'target', $context);
        $this->assertEquals($expect, $result);
    }

    public function checkDataAfterThanDataProvider()
    {
        return [
            ['2015-01-01 00:00:00', '2015-01-01 00:00:00', false],
            ['2015-01-01 24:00:01', '2015-01-02 00:00:00', true],
            ['2015-01-01 00:00:00', '2015-01-02 00:00:00', false],
            ['2015-01-02 00:00:00', '2015-01-01 00:00:00', true],
        ];
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

    /**
     * 設定をチェックする
     *
     * @param array $check チェックするURL
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider checkUrlDataProvider
     */
    public function testCheckUrl($check, $expected, $message = null)
    {
        $result = $this->BcValidation->checkUrl($check);
        $this->assertEquals($expected, $result, $message);
    }

    public function checkUrlDataProvider()
    {
        return [
            [[1], false, '適当なURLです'],
            [['hoge'], false, '適当なURLです'],
            [['/hoge'], false, '適当なURLです'],
            [['hoge/'], false, '適当なURLです'],
            [['/hoge/'], false, '適当なURLです'],
            [['/hoge/*'], false, '適当なURLです'],
            // TODO: router設定でのprefix設定できてないため
            // [['admin'], true, '権限の必要なURLです'],
            // [['/admin'], true, '権限の必要なURLです'],
            // [['admin/'], true, '権限の必要なURLです'],
            // [['admin/*'], true, '権限の必要なURLです'],
            // [['/admin/*'], true, '権限の必要なURLです'],
            // [['/admin/dashboard/'], true, '権限の必要なURLです'],
            // [['/admin/dashboard/*'], true, '権限の必要なURLです'],
        ];
    }

}
