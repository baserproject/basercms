<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Validation;

use BaserCore\Test\Scenario\InitAppScenario;
use Cake\Routing\Router;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Validation\BcValidation;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcValidationTest
 * @property BcValidation $BcValidation
 */
class BcValidationTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

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
        Router::reload();
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

    public static function alphaNumericPlusDataProvider()
    {
        $alpha = implode('', array_merge(range('a', 'z'), range('A', 'Z')));
        $numeric = implode('', range(0, 9));
        $mark = '-_';
        $allowedChars = $alpha . $numeric . $mark;

        return [
            ['test', [], true],
            ['test!', [], false],
            [$allowedChars, [], true],
            [$allowedChars . '!', [], false],
            ['あいうえお', [], false],
            ['あいうえお', ['あ'], false],
            ['あいうえお', ['あいうえお'], true],
            ['あいうえお_', ['あいうえお'], true],
        ];
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

    public static function bcUtileUrlencodeBlankDataProvider()
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

    public static function minLengthDataProvider()
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

    public static function maxLengthDataProvider()
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

    public static function maxByteDataProvider()
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

    public static function notInListDataProvider()
    {
        return [
            ['test1', ['test1', 'test2'], false],
            ['test3', ['test1', 'test2'], true],
        ];
    }

    /**
     * Test fileCheck
     *
     * @param string $fileName チェック対象ファイル名
     * @param string $fileSize チェック対象ファイルサイズ
     * @param boolean $expect
     * @dataProvider fileCheckDataProvider
     */
    public function testFileCheck($fileName, $fileSize, $errorCode, $expect)
    {
        $check = [
            "name" => $fileName,
            "size" => $fileSize,
            "error" => $errorCode,
        ];
        $size = 1048576;

        $_POST = ['fileCheck require $_POST' => true];
        $result = $this->BcValidation->fileCheck($check, $size);
        $this->assertEquals($expect, $result);
    }

    public static function fileCheckDataProvider()
    {
        return [
            ["test.jpg", 1048576, 0, true],
            ["", 1048576, 0, true],
            ["test.jpg", null, 2, false],
            [null, null, 4, true],
        ];
    }

    /**
     * ファイルの拡張子チェック
     *
     * @param string $fileName チェック対象ファイル名
     * @param string $fileType チェック対象ファイルタイプ
     * @param boolean $expect
     * @dataProvider fileExtDataProvider
     */
    public function testFileExt($fileType, $expect)
    {
        $ext = "jpg,png";

        $result = $this->BcValidation->fileExt($fileType, $ext);
        $this->assertEquals($expect, $result);
    }

    public static function fileExtDataProvider()
    {
        return [
            [
                [
                    'name' => 'test.jpg',
                    'size' => 1,
                    'type' => 'image/jpeg',
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => 'test',
                    'ext' => 'jpg'
                ],
                true
            ],
            [
                [
                    'name' => 'test.png',
                    'size' => 1,
                    'type' => 'image/jpeg',
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => 'test',
                    'ext' => 'png'
                ],
                true
            ],
            [
                [
                    'name' => 'test.gif',
                    'size' => 1,
                    'type' => 'image/jpeg',
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => 'test',
                    'ext' => 'gif'
                ],
                true
            ],
            [
                [
                    'name' => 'test.png',
                    'size' => 1,
                    'type' => 'image/gif',
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => 'test',
                    'ext' => 'png'
                ],
                false
            ]
        ];
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

    public static function notFileEmptyDataProvider()
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

    public static function confirmDataProvider()
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

    public static function emailsDataProvider()
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

    public static function halfTextDataProvider()
    {
        return [
            ['test', true],
            ['テスト', false],
        ];
    }

    /**
     * Test checkDateRange
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

        $this->assertEquals(true, $this->BcValidation->checkDateRange($value, ['begin', 'end'], $context));
        $this->assertEquals(true, $this->BcValidation->checkDateRange($value, ['test', 'end'], $context));
        $this->assertEquals(true, $this->BcValidation->checkDateRange($value, ['begin', 'test'], $context));

        $context['data']['end'] = '2020-01-01 00:00:00';
        $this->assertEquals(false, $this->BcValidation->checkDateRange($value, ['begin', 'end'], $context));

        $context['data']['end'] = '2019-01-01 00:00:00';
        $this->assertEquals(false, $this->BcValidation->checkDateRange($value, ['begin', 'end'], $context));
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

    public static function checkDataAfterThanDataProvider()
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
     * @dataProvider containsScriptDataProvider
     */
    public function testContainsScript($value, $expect)
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        if ($expect) Router::setRequest($this->loginAdmin($this->getRequest()));
        $result = $this->BcValidation->containsScript($value);
        $this->assertEquals($expect, $result);
    }

    public static function containsScriptDataProvider()
    {
        return [
            // phpコードの場合
            ['<?php echo $test; ?>', false],
            // jsコードの場合
            ['<script type="text/javascript">', false],
            // イベントが入ってる場合
            ['<input type="text" onclick="select()"/>', false],
            // jsコードへのリンクがある場合
            ['<a href="javascript:doSomething();">click me</a>', false],
            // アドミンユーザーでログインしてる場合
            ['<a href="javascript:doSomething();">click me</a>', true],
        ];
    }

    /**
     * Test checkKatakana
     *
     * @return void
     */
    public function testCheckKatakana()
    {
        $t = [
            'アい' => false,
            'ァアイウエオラヰヱヴヸヹヺ・ーヽヾ' => true,
            ' 　' => true,
            'ゞカタカナ' => false,
            'English' => false,
            '漢字' => false,
        ];
        foreach($t as $str => $hope) {
            $result = $this->BcValidation->checkKatakana($str);
            $this->assertEquals($result, $hope);
        }

        $result = $this->BcValidation->checkKatakana('カタカナ除外カタカナ', '除外');
        $this->assertEquals($result, true);

        $result = $this->BcValidation->checkKatakana('スペースキンシ　 ', '');
        $this->assertEquals($result, false);
    }

    /**
     * Test checkHiragana
     *
     * @return void
     */
    public function testCheckHiragana()
    {
        $t = [
            'アい' => false,
            'ぁあぃいぅうぇえぉおかがきぎくぐけげこごさざしじすずせぜそぞただちぢっつづてでとどなにぬねのはばぱひびぴふぶぷへべぺほぼぽまみむめもゃやゅゆょよらりるれろゎわゐゑをんゔゕゖ' => true,
            ' 　ー' => true,
            'ヾカタカナ' => false,
            'English' => false,
            '漢字' => false,
        ];
        foreach($t as $str => $hope) {
            $result = $this->BcValidation->checkHiragana($str);
            $this->assertEquals($result, $hope);
        }

        $result = $this->BcValidation->checkHiragana('ひらがな除外ひらがな', '除外');
        $this->assertEquals($result, true);

        $result = $this->BcValidation->checkHiragana('すぺーすきんし　 ', '');
        $this->assertEquals($result, false);
    }

    /**
     * test checkSelectList
     */
    public function test_checkSelectList()
    {
        //戻り＝falseケース
        $str = "あ\rべ\r\nあ\nべ\ntest";
        $result = $this->BcValidation->checkSelectList($str);
        $this->assertFalse($result);
        //戻り＝trueケース
        $str = "あa\nべ\nあ";
        $result = $this->BcValidation->checkSelectList($str);
        $this->assertTrue($result);
    }

    /**
     * 範囲を指定しての長さチェック
     *
     * @param mixed $check
     * @param int $min
     * @param int $max
     * @param boolean $expect
     * @dataProvider betweenDataProvider
     */
    public function testBetween($check, $min, $max, $expect)
    {
        $result = $this->BcValidation->between($check, $min, $max);
        $this->assertEquals($expect, $result);
    }

    public static function betweenDataProvider()
    {
        return [
            ["あいう", 2, 4, true],
            ["あいう", 3, 3, true],
            ["あいう", 4, 3, false],
            ["あいうえお", 2, 4, false],
        ];
    }


    /**
     * test notBlankOnlyString
     */
    public function test_notBlankOnlyString()
    {
        //戻り＝falseケース：半角スペース
        $str = " ";
        $result = $this->BcValidation->notBlankOnlyString($str);
        $this->assertFalse($result);
        //戻り＝falseケース：全角スペース
        $str = "　";
        $result = $this->BcValidation->notBlankOnlyString($str);
        $this->assertFalse($result);
        //戻り＝falseケース：半角・全角
        $str = "　 ";
        $result = $this->BcValidation->notBlankOnlyString($str);
        $this->assertFalse($result);
        //戻り＝trueケース
        $str = "あa　";
        $result = $this->BcValidation->notBlankOnlyString($str);
        $this->assertTrue($result);
    }

    /**
     * @param $str
     * @param $key
     * @param $regex
     * @param $expect
     *
     * test checkWithJson
     * @dataProvider checkWithJsonProvider
     *
     */
    public function test_checkWithJson($key, $str, $regex, $expect)
    {
        $request = $this->getRequest('/')->withData('validate', ['EMAIL_CONFIRM', 'FILE_EXT', 'MAX_FILE_SIZE']);
        Router::setRequest($request);

        $result = $this->BcValidation->checkWithJson($str, $key, $regex);
        $this->assertEquals($expect, $result);
    }

    public static function checkWithJsonProvider()
    {
        return [
            //Eメール比較先フィールド名 テスト
            ['BcCustomContent.email_confirm', '{"BcCustomContent":{"email_confirm":"ああ"}}', "/^[a-z0-9_]+$/", false],       //戻り＝falseケース：全角文字
            ['BcCustomContent.email_confirm', '{"BcCustomContent":{"email_confirm":" "}}', "/^[a-z0-9_]+$/", false],         //戻り＝falseケース：半角スペース
            ['BcCustomContent.email_confirm', '{"BcCustomContent":{"email_confirm":"ああaaa"}}', "/^[a-z0-9_]+$/", false],    //戻り＝falseケース：半角・全角
            ['BcCustomContent.email_confirm', '{"BcCustomContent":{"email_confirm":"aaaa_bbb"}}', "/^[a-z0-9_]+$/", true],    //戻り＝trueケース

            //ファイルアップロードサイズ制限 テスト
            ['BcCustomContent.max_file_size', '{"BcCustomContent":{"max_file_size":"aaaaa111"}}', "/^[0-9]+$/", false],       //戻り＝falseケース：文字列
            ['BcCustomContent.max_file_size', '{"BcCustomContent":{"max_file_size":"ああaaa"}}', "/^[0-9]+$/", false],         //戻り＝falseケース：半角・全角
            ['BcCustomContent.max_file_size', '{"BcCustomContent":{"max_file_size":"123"}}', "/^[0-9]+$/", true],             //戻り＝falseケース：数値

            //ファイル拡張子チェック テスト
            ['BcCustomContent.file_ext', '{"BcCustomContent":{"file_ext":"ああaaa"}}', "/^[a-z,]+$/", false],                  //戻り＝falseケース：半角・全角
            ['BcCustomContent.file_ext', '{"BcCustomContent":{"file_ext":"1111"}}', "/^[a-z,]+$/", false],                    //戻り＝falseケース：数字
            ['BcCustomContent.file_ext', '{"BcCustomContent":{"file_ext":"pdf,png"}}', "/^[a-z,]+$/", true],                  //戻り＝trueケース：文字列
        ];
    }

    /**
     * test hexColorPlus
     *
     * @param string $value
     * @param boolean $expect
     * @return void
     * @dataProvider hexColorPlusDataProvider
     */
    public function testHexColorPlus($value, $expect)
    {
        $result = $this->BcValidation->hexColorPlus($value);
        $this->assertEquals($expect, $result);
    }

    public static function hexColorPlusDataProvider()
    {
        return [
            ['000', true],
            ['0fF', true],
            ['123f', true],
            ['123abc', true],
            ['1234abcd', true],
            ['black', false],
            ['#000', false]
        ];
    }

    /**
     * @dataProvider reservedDataProvider
     */
    public function test_reserved($value, $expect)
    {
        $result = $this->BcValidation->reserved($value);
        $this->assertEquals($expect, $result);
    }

    public static function reservedDataProvider()
    {
        return [
            ['test', true],
            ['admin', true],
            ['accessible', false],
            ['before', false],
        ];
    }

}
