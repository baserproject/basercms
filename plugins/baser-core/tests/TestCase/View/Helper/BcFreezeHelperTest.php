<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.6
 * @license         https://basercms.net/license/index.html
 */

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\Helper\BcFormHelper;
use BaserCore\View\Helper\BcFreezeHelper;
use Cake\View\View;

/**
 * Class FormHelperTest
 *
 * @property BcFormHelper $Form
 * @property BcFreezeHelper $BcFreeze
 */
class BcFreezeHelperTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcFreeze = new BcFreezeHelper(new View());
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * テキストボックスを表示する
     *
     * @param boolean $freezed フォームを凍結させる
     * @param string $fieldName フィールド文字列
     * @param array $attributes html属性
     * @param string $expexted 期待値(htmlタグ)
     * @dataProvider textDataProvider
     */
    public function testText($freezed, $fieldName, $attributes, $expected)
    {
        // 凍結させる
        if ($freezed) {
            [$model, $field] = explode('.', $fieldName);
            $request = $this->getRequest()->withData($model, [$field => 'BaserCMS'])->withData('freezed', 'BaserCMS');
            $this->BcFreeze = new BcFreezeHelper(new View($request));
            $this->BcFreeze->freeze();
        }

        $result = $this->BcFreeze->text($fieldName, $attributes);
        $this->assertEquals($expected, $result);
    }

    public static function textDataProvider()
    {
        return [
            [false, 'baser', [], '<input type="text" name="baser">'],
            [false, 'baser', ['class' => 'bcclass'], '<input type="text" name="baser" class="bcclass">'],
            [false, 'baser', ['class' => 'bcclass', 'id' => 'bcid'], '<input type="text" name="baser" class="bcclass" id="bcid">'],
            [false, 'baser', ['type' => 'hidden'], '<input type="hidden" name="baser">'],
            [true, 'baser.freezed', [], '<input type="hidden" name="baser[freezed]" value="BaserCMS">BaserCMS'],
            [true, 'baser.freezed', ['value' => 'BaserCMS2'], '<input type="hidden" name="baser[freezed]" value="BaserCMS2">BaserCMS2'],
            [true, 'baser.freezed', ['type' => 'hidden'], '<input type="hidden" name="baser[freezed]" value="BaserCMS">BaserCMS'],
        ];
    }

    /**
     * select プルダウンメニューを表示
     *
     * @param boolean $freezed フォームを凍結させる
     * @param string $fieldName フィールド文字列
     * @param array $options コントロールソース
     * @param array $attributes html属性
     * @param array    空データの表示有無
     * @param string $expexted 期待値(htmlタグ)
     * @dataProvider selectDataProvider
     */
    public function testSelect($freezed, $fieldName, $options, $attributes, $expected)
    {
        // 凍結させる
        if ($freezed) {
            $this->BcFreeze->freeze();
        }

        $result = $this->BcFreeze->select($fieldName, $options, $attributes);
        $this->assertEquals($expected, $result);
    }

    public static function selectDataProvider()
    {
        return [
            [false, 'baser', [], [], '<select name="baser"></select>'],
            [false, 'baser', ['1' => 'ラーメン'], [], '<select name="baser"><option value="1">ラーメン</option></select>'],
            [false, 'baser', ['1' => 'ラーメン', '2' => '寿司'], [], '<select name="baser"><option value="1">ラーメン</option><option value="2">寿司</option></select>'],
            [false, 'baser', [], ['class' => 'bcclass'], '<select name="baser" class="bcclass"></select>'],
            [false, 'baser', ['1' => 'ラーメン'], ['class' => 'bcclass'], '<select name="baser" class="bcclass"><option value="1">ラーメン</option></select>'],
            [false, 'baser', ['1' => 'ラーメン', '2' => '寿司'], ['cols' => 10], '<select name="baser" cols="10"><option value="1">ラーメン　　　　　　</option><option value="2">寿司　　　　　　　　</option></select>'],
            [true, 'baser.freezed', ['1' => 'ラーメン'], ['class' => 'bcclass'], '<input type="hidden" name="baser[freezed]" class="bcclass">'],
        ];
    }


    /**
     * 日付タグを表示
     *
     * @param boolean $freezed フォームを凍結させる
     * @param string $fieldName フィールド文字列
     * @param string $dateFormat 日付フォーマット
     * @param string $timeFormat 時間フォーマット
     * @param array $attributes html属性
     * @param string $expexted 期待値
     * @dataProvider dateTimeDataProvider
     */
    public function testDateTime($freezed, $fieldName, $dateFormat, $timeFormat, $attributes, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // 凍結させる
        if ($freezed) {
            $this->BcFreeze->freeze();
        }

        $result = $this->BcFreeze->dateTime($fieldName, $dateFormat, $timeFormat, $attributes);
        $this->assertMatchesRegularExpression('/' . $expected . '/s', $result);
    }

    public static function dateTimeDataProvider()
    {
        return [
            [false, 'test', 'YMD', '12', [], 'id="testYear".*id="testMonth".*id="testDay".*id="testHour".*id="testMin".*id="testMeridian"'],
            [false, 'test', 'DMY', '12', [], 'id="testDay".*id="testMonth".*id="testYear"'],
            [false, 'test', 'YMD', '24', [], '59<\/option>.<\/select>$'],
            [false, 'test', 'YMD', '12', ['class' => 'bcclass'], 'class="bcclass"'],
            [false, 'test', 'YMD', '12', ['empty' => false], '^(?!value="").*$'],
            [false, 'test', 'YMD', '12', ['empty' => ['day' => '選択されていません']], '<option value="">選択されていません'],
            [true, 'test', 'YMD', '12', [], 'type="hidden"'],
            [true, 'test', 'YMD', '12', ['selected' => ['year' => '2010', 'month' => '4', 'day' => '1']], '2010年.*4月.*1日'],
            [true, 'test', 'YMD', '12', ['selected' => '2010-4-1 11:22:33'], '2010年.*4月.*1日.*11時.*22分'],
            [true, 'test', 'YMD', '12', ['selected' => ['day' => '100']], 'value="100"'],
            [true, 'test', 'YMD', '12', ['empty' => true], '^((?!value="").)*$'],
        ];
    }

    /**
     * 和暦年
     *
     * @param boolean $freezed フォームを凍結させる
     * @param string $fieldName Prefix name for the SELECT element
     * @param integer $minYear First year in sequence
     * @param integer $maxYear Last year in sequence
     * @param string $selected Option which is selected.
     * @param array $attributes Attribute array for the select elements.
     * @param boolean $showEmpty Show/hide the empty select option
     * @param string $expexted 期待値
     * @dataProvider wyearDataProvider
     */
    public function testWyear($freezed, $fieldName, $minYear, $maxYear, $selected, $attributes, $showEmpty, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // 凍結させる
        if ($freezed) {
            $this->BcFreeze->freeze();

        }

        $result = $this->BcFreeze->wyear($fieldName, $minYear, $maxYear, $selected, $attributes, $showEmpty);
        $this->assertMatchesRegularExpression('/' . $expected . '/s', $result);
    }

    public static function wyearDataProvider()
    {
        return [
            [false, 'test', null, null, null, [], true, 'id="testYear">.*<option value="r-17">'],
            [false, 'test', 2010, null, null, [], true, '<option value="h-22">平成 22<\/option>.<\/select>$'],
            [false, 'test', null, 2010, null, [], true, '<option value=""><\/option>.<option value="h-22">'],
            [false, 'test', null, null, 'r-17', [], true, 'value="r-17" selected="selected"'],
            [false, 'test', null, null, null, ['type' => 'hidden'], true, 'type="hidden"'],
            [false, 'test', null, null, null, [], false, 'id="testYear">.*<option value="r-17">'],
            [true, 'test', null, null, null, [], true, 'type="hidden"'],
            [true, 'test', null, null, '2035-1-1', [], true, '令和 17'],
        ];
    }

    /**
     * チェックボックスを表示する
     *
     * @param boolean $freezed フォームを凍結させる
     * @param string $fieldName フィールド文字列
     * @param title $title タイトル
     * @param array $attributes html属性
     * @param string $expexted 期待値
     * @dataProvider checkboxDataProvider
     */
    public function testCheckbox($freezed, $fieldName, $attributes, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // 凍結させる
        if ($freezed) {
            $this->BcFreeze->freeze();
        }

        $result = $this->BcFreeze->checkbox($fieldName, $attributes);
        $this->assertMatchesRegularExpression('/' . $expected . '/s', $result);
    }

    public static function checkboxDataProvider()
    {
        return [
            [false, 'baser', [], "<input type=\"hidden\" name=\"data\[baser\]\" id=\"baser_\" value=\"0\"\/><input type=\"checkbox\" name=\"data\[baser\]\" value=\"1\" id=\"baser\""],
            [false, 'baser', ['class' => 'bcclass'], 'class="bcclass"'],
            [true, 'baser.freezed', [], "name=\"data\[baser\]\[freezed\]\".*id=\"baserFreezed\""],
            [true, 'baser.freezed', ['label' => 'test'], 'label="test"'],
        ];
    }

    /**
     * テキストエリアを表示する
     *
     * @param boolean $freezed フォームを凍結させる
     * @param string フィールド文字列
     * @param array html属性
     * @param string $expexted 期待値
     * @dataProvider textareaDataProvider
     */
    public function testTextarea($freezed, $fieldName, $attributes, $expected)
    {
        // 凍結させる
        if ($freezed) {
            [$model, $field] = explode('.', $fieldName);
            $request = $this->getRequest()->withData($model, [$field => 'BaserCMS'])->withData('freezed', 'BaserCMS');
            $this->BcFreeze = new BcFreezeHelper(new View($request));
            $this->BcFreeze->freeze();
        }

        $result = $this->BcFreeze->textarea($fieldName, $attributes);
        $this->assertEquals($expected, $result);
    }

    public static function textareaDataProvider()
    {
        return [
            [false, 'baser', [], '<textarea name="baser" rows="5"></textarea>'],
            [false, 'baser', ['class' => 'bcclass'], '<textarea name="baser" class="bcclass" rows="5"></textarea>'],
            [true, 'baser.freezed', [], '<input type="hidden" name="baser[freezed]" value="BaserCMS">BaserCMS'],
            [true, 'baser.freezed', ['value' => 'BaserCMS2'], '<input type="hidden" name="baser[freezed]" value="BaserCMS2">BaserCMS2'],
            [true, 'baser.freezed', ['class' => 'bcclass'], '<input type="hidden" name="baser[freezed]" class="bcclass" value="BaserCMS">BaserCMS'],
        ];
    }

    /**
     * ラジオボタンを表示する
     *
     * @param boolean $freezed フォームを凍結させる
     * @param string $fieldName フィールド文字列
     * @param array $options コントロールソース
     * @param array $attributes html属性
     * @param string $expexted 期待値
     * @dataProvider radioDataProvider
     */
    public function testRadio($freezed, $fieldName, $options, $attributes, $expected)
    {
        // 凍結させる
        if ($freezed) {
            $this->BcFreeze->freeze();
        }

        $result = $this->BcFreeze->radio($fieldName, $options, $attributes);
        $this->assertEquals($expected, $result);
    }

    public static function radioDataProvider()
    {
        return [
            [false, 'baser', [], [], '<input type="hidden" name="baser" id="baser" value="">'],
            [
                false, 'baser', ['test1' => 'testValue1'], [],
                '<input type="hidden" name="baser" id="baser" value=""><label for="baser-test1"><input type="radio" name="baser" value="test1" id="baser-test1">testValue1</label>'
            ],
            [
                false, 'baser', ['test1' => 'testValue1'], ['class' => 'bcclass'],
                '<input type="hidden" name="baser" id="baser" value=""><label for="baser-test1"><input type="radio" name="baser" value="test1" id="baser-test1" class="bcclass">testValue1</label>'
            ],
            [true, 'baser.freezed', [], [], '<input type="hidden" name="baser[freezed]" class="">'],
            [true, 'baser.freezed', ['test1' => 'testValue1'], ['class' => 'bcclass'], '<input type="hidden" name="baser[freezed]" class="bcclass">'],
        ];
    }

    /**
     * ファイルタグを出力
     *
     * MEMO : 3番目のテストは、以下のエラーに対応できなかったためスキップしています。
     * BcUploadHelper を利用するには、モデルで BcUploadBehavior の利用設定が必要です。
     *
     * @param boolean $freezed フォームを凍結させる
     * @param string $fieldName
     * @param array $options
     * @param string $expexted 期待値
     * @dataProvider fileDataProvider
     */
    public function testFile($freezed, $fieldName, $options, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // 凍結させる
        if ($freezed) {
            // $this->markTestIncomplete('このテストは、一部未完成です。');
            $this->BcFreeze->freeze();
            $this->EditorTemplate = ClassRegistry::init('EditorTemplate');
            $this->EditorTemplate->BcFreeze = new BcFreezeHelper(new View);
            $result = $this->EditorTemplate->BcFreeze->file($fieldName, $options);
            $this->assertMatchesRegularExpression('/' . $expected . '/s', $result);


        } else {
            $result = $this->BcFreeze->file($fieldName, $options);
            $this->assertMatchesRegularExpression('/' . $expected . '/s', $result);

        }

    }

    public static function fileDataProvider()
    {
        return [
            [false, 'baser', [], '<input type="file" name="data\[baser\]" id="baser"'],
            [false, 'baser', ['size' => 100], 'size="100"'],
            [true, 'baser.freezed', [], '<input type="file" name="data\[baser\]\[freezed\]" id="baserFreezed"\/>'],
        ];
    }

    /**
     * test email
     * @param boolean $freezed フォームを凍結させる
     * @param string $fieldName フィールド文字列
     * @param array $attributes html属性
     * @param string $expexted 期待値(htmlタグ)
     * @dataProvider emailDataProvider
     */
    public function testEmail($freezed, $fieldName, $attributes, $expected)
    {
        if ($freezed) {
            [$model, $field] = explode('.', $fieldName);
            $request = $this->getRequest()->withData($model, [$field => 'BaserCMS'])->withData('freezed', 'BaserCMS');
            $this->BcFreeze = new BcFreezeHelper(new View($request));
            $this->BcFreeze->freeze();
        }

        $result = $this->BcFreeze->email($fieldName, $attributes);
        $this->assertEquals($expected, $result);
    }

    public static function emailDataProvider()
    {
        return [
            [false, 'baser', [], '<input type="email" name="baser">'],
            [false, 'baser', ['class' => 'bcclass'], '<input type="email" name="baser" class="bcclass">'],
            [false, 'baser', ['class' => 'bcclass', 'id' => 'bcid'], '<input type="email" name="baser" class="bcclass" id="bcid">'],
            [true, 'baser.freezed', [], '<input type="hidden" name="baser[freezed]" value="BaserCMS">BaserCMS'],
            [true, 'baser.freezed', ['value' => 'BaserCMS2'], '<input type="hidden" name="baser[freezed]" value="BaserCMS2">BaserCMS2'],
            [true, 'baser.freezed', ['type' => 'hidden'], '<input type="hidden" name="baser[freezed]" value="BaserCMS">BaserCMS'],
        ];
    }

    /**
     * ファイルコントロール（画像）を表示する
     *
     * @param boolean $freezed フォームを凍結させる
     * @param string $name $this->request->data[$model][$field]['name']に格納する値
     * @param string $exist フィールド文字列 $this->request->data[$model][$field . '_exists'] に格納する値
     * @param string $fieldName フィールド文字列
     * @param array $attributes html属性
     * @param array $imageAttributes 画像属性
     * @param string $expexted 期待値
     * @dataProvider imageDataProvider
     * @TODO 確認画面には未チェック
     */
    public function testImage($freezed, $name, $exist, $fieldName, $attributes, $imageAttributes, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        [$model, $field] = explode('.', $fieldName);
        $this->BcFreeze->request->data[$model][$field]['name'] = $name;
        $this->BcFreeze->request->data[$model][$field . '_exists'] = $exist;

        // 凍結させる
        if ($freezed) {
            $this->BcFreeze->freeze();

        }

        $result = $this->BcFreeze->image($fieldName, $attributes, $imageAttributes);
        $this->assertMatchesRegularExpression('/' . $expected . '/s', $result);
    }

    public static function imageDataProvider()
    {
        return [
            [false, null, null, 'test.image', [], [], '<input type="file" name="data\[test\]\[image\]" id="testImage"'],
            [false, null, null, 'test.image', ['size' => 100], [], 'size="100"'],
            [false, null, 'testexist', 'test.image', [], [], 'src="\/\/tests.*<label.+?削除する<'],
            [false, null, 'testexist', 'test.image', [], ['dir' => 'testdir'], 'src="\/testdir\/tests'],
            [true, null, null, 'test.image', [], [], '&nbsp;'],
            [true, 'testname', null, 'test.image', [], [], 'id="testImageExists".*src="tmp\/test\/img'],
            [true, null, null, 'test.image', [], ['alt' => 'testalt'], '&nbsp;'],
            [true, null, 'testexist', 'test.image', [], [], 'dir=""'],
            [true, null, 'testexist', 'test.image', [], ['dir' => 'testdir'], 'dir="testdir"'],
        ];
    }

    /**
     * test tel
     * @param boolean $freezed フォームを凍結させる
     * @param string $fieldName フィールド文字列
     * @param array $attributes html属性
     * @param string $expexted 期待値(htmlタグ)
     * @dataProvider telDataProvider
     */
    public function testTel($freezed, $fieldName, $attributes, $expected)
    {
        if ($freezed) {
            [$model, $field] = explode('.', $fieldName);
            $request = $this->getRequest()->withData($model, [$field => 'BaserCMS'])->withData('freezed', 'BaserCMS');
            $this->BcFreeze = new BcFreezeHelper(new View($request));
            $this->BcFreeze->freeze();
        }

        $result = $this->BcFreeze->tel($fieldName, $attributes);
        $this->assertEquals($expected, $result);
    }

    public static function telDataProvider()
    {
        return [
            [false, 'baser', [], '<input type="tel" name="baser">'],
            [false, 'baser', ['class' => 'bcclass'], '<input type="tel" name="baser" class="bcclass">'],
            [false, 'baser', ['class' => 'bcclass', 'id' => 'bcid'], '<input type="tel" name="baser" class="bcclass" id="bcid">'],
            [true, 'baser.freezed', [], '<input type="hidden" name="baser[freezed]" value="BaserCMS">BaserCMS'],
            [true, 'baser.freezed', ['value' => 'BaserCMS2'], '<input type="hidden" name="baser[freezed]" value="BaserCMS2">BaserCMS2'],
            [true, 'baser.freezed', ['type' => 'hidden'], '<input type="hidden" name="baser[freezed]" value="BaserCMS">BaserCMS'],
        ];
    }

    /**
     * カレンダーコントロール付きのテキストフィールド
     * jquery-ui-1.7.2 必須
     *
     * @param boolean $freezed フォームを凍結させる
     * @param string $date 凍結させた日時
     * @param string $fieldName フィールド文字列
     * @param array $attributes HTML属性
     * @param string $expexted 期待値
     * @dataProvider datepickerDataProvider
     */
    public function testDatepicker($freezed, $date, $fieldName, $attributes, $expected)
    {
        // 凍結させる
        if ($freezed) {
            [$model, $field] = explode('.', $fieldName);
            if (!empty($date)){
                $request = $this->getRequest()->withData($model, [$field => $date]);
            }else{
                $request = $this->getRequest()->withData($model, [$field => 'BaserCMS'])->withData('freezed', 'BaserCMS');
            }
            $this->BcFreeze = new BcFreezeHelper(new View($request));
            $this->BcFreeze->freeze();
        }

        $result = $this->BcFreeze->datepicker($fieldName, $attributes);
        $this->assertMatchesRegularExpression('/' . $expected . '/s', $result);
    }

    public static function datepickerDataProvider()
    {
        return [
            [false, null, 'baser', [], 'type="text".*id="baser".*("#baser")'],
            [false, null, 'baser', ['test1' => 'testValue1'], 'test1="testValue1"'],
            [true, '2015-4-1', 'baser.freezed', [], 'type="hidden".*2015\/04\/01'],
            [true, null, 'baser.freezed', [], 'type="hidden".*1970\/01\/01'],
            [true, null, 'baser.freezed', ['test1' => 'testValue1'], 'test1="testValue1"'],
        ];
    }

    public function testFreeze()
    {
        $this->assertFalse($this->BcFreeze->freezed);
        $this->BcFreeze->freeze();
        $this->assertTrue($this->BcFreeze->freezed);
    }

    /**
     * 凍結時用のコントロールを取得する
     *
     * @param string    フィールド文字列
     * @param array    コントロールソース
     * @param array    html属性
     * @param string $expexted 期待値
     * @dataProvider freezeControllDataProvider
     */
    public function testFreezeControll($fieldName, $options, $attributes, $expected)
    {
        $result = $this->BcFreeze->freezeControll($fieldName, $options, $attributes);
        $this->assertMatchesRegularExpression('/' . $expected . '/s', $result);
    }

    public static function freezeControllDataProvider()
    {
        return [
            ['baser.freezed', [], [], 'input type="hidden" name="baser\[freezed\]" class=""'],
            ['baser.freezed', [], ['value' => 'BaserCMS'], 'value="BaserCMS"'],
            ['baser.freezed', [], ['value' => 'BaserCMS', 'multiple' => 'select'], 'value="BaserCMS">BaserCMS'],
            ['baser.freezed', [], ['value' => 'BaserCMS', 'multiple' => 'checkbox'], 'value="BaserCMS">BaserCMS'],
            ['baser.freezed', ['1' => 'BaserCMS1', '2' => 'BaserCMS2', '3' => 'BaserCMS3',], ['value' => [1, 2, 3], 'multiple' => 'checkbox'], '<li>BaserCMS1.*<li>BaserCMS2.*<li>BaserCMS3.*value="1".*value="2".*value="3"'],
            ['baser.freezed', ['1' => 'BaserCMS1'], ['value' => [1], 'multiple' => 'hoge'], '<input type="hidden" name="baser\[freezed\]\[\]" class="" value="1"><ul class="" value="1"><li>BaserCMS1<\/li><\/ul>'],
            ['baser.freezed', ['1' => 'BaserCMS1', '2' => 'BaserCMS2', '3' => 'BaserCMS3',], ['value' => [1, 2, 3], 'multiple' => 'checkbox'], '<li>BaserCMS1.*<li>BaserCMS2.*<li>BaserCMS3.*value="1".*value="2".*value="3"'],
            ['baser.freezed', ['1' => 'BaserCMS1'], ['value' => 1], '<input type="hidden" name="baser\[freezed\]" class="" value="1">BaserCMS1'],
            ['baser.freezed.hoge', ['1' => 'BaserCMS1'], ['value' => 1], '<input type="hidden" name="baser\[freezed\]\[hoge\]" class="" value="1">BaserCMS1'],
        ];
    }

    /**
     * @dataProvider passwordDataProvider
     */
    public function test_password($freezed, $fieldName, $attributes, $expected)
    {
        if ($freezed) {
            [$model, $field] = explode('.', $fieldName);
            $request = $this->getRequest()->withData($model, [$field => 'BaserCMS'])->withData('freezed', 'BaserCMS');
            $this->BcFreeze = new BcFreezeHelper(new View($request));
            $this->BcFreeze->freeze();
        }

        $result = $this->BcFreeze->password($fieldName, $attributes);
        $this->assertEquals($expected, $result);
    }

    public static function passwordDataProvider()
    {
        return [
            [false, 'baser', [], '<input type="password" name="baser">'],
            [false, 'baser', ['class' => 'bcclass'], '<input type="password" name="baser" class="bcclass">'],
            [false, 'baser', ['class' => 'bcclass', 'id' => 'bcid'], '<input type="password" name="baser" class="bcclass" id="bcid">'],
            [true, 'baser.freezed', [], '<input type="hidden" name="baser[freezed]" value="BaserCMS">********'],
            [true, 'baser.freezed', ['value' => 'BaserCMS2'], '<input type="hidden" name="baser[freezed]" value="BaserCMS2">*********'],
            [true, 'baser.freezed', ['type' => 'hidden'], '<input type="hidden" name="baser[freezed]" value="BaserCMS">********'],
        ];
    }

    /**
     * @dataProvider numberlDataProvider
     */
    public function test_number($freezed, $fieldName, $attributes, $expected)
    {
        if ($freezed) {
            [$model, $field] = explode('.', $fieldName);
            $request = $this->getRequest()->withData($model, [$field => '123'])->withData('freezed', '123');
            $this->BcFreeze = new BcFreezeHelper(new View($request));
            $this->BcFreeze->freeze();
        }

        $result = $this->BcFreeze->number($fieldName, $attributes);
        $this->assertEquals($expected, $result);
    }

    public static function numberlDataProvider()
    {
        return [
            [false, 'baser', [], '<input type="number" name="baser">'],
            [false, 'baser', ['class' => 'bcclass'], '<input type="number" name="baser" class="bcclass">'],
            [false, 'baser', ['class' => 'bcclass', 'id' => 'bcid'], '<input type="number" name="baser" class="bcclass" id="bcid">'],
            [true, 'baser.freezed', [], '<input type="hidden" name="baser[freezed]" value="123">123'],
            [true, 'baser.freezed', ['value' => '1234'], '<input type="hidden" name="baser[freezed]" value="1234">1234'],
            [true, 'baser.freezed', ['type' => 'hidden'], '<input type="hidden" name="baser[freezed]" value="123">123'],
        ];
    }

    /**
     * @dataProvider checkboxProvider
     */
    public function test_checkbox($freezed, $fieldName, $option, $expected)
    {
        if ($freezed) {
            $this->BcFreeze->freeze();
        }
        $result = $this->BcFreeze->checkbox($fieldName, $option);
        $this->assertEquals($expected, $result);
    }


    public static function checkboxProvider()
    {
        return [
            [false, 'baser', ['label' => 'text'], '<input type="hidden" name="baser" value="0"><input type="checkbox" name="baser" value="1" label="text">'],
            [true, 'baser', ['label' => 'text'], '<input type="hidden" name="baser" class="" ="" text="text">'],
            [false, 'baser', [], '<input type="hidden" name="baser" value="0"><input type="checkbox" name="baser" value="1">'],
            [true, 'baser', [], '<input type="hidden" name="baser" class="" ="" ="">'],
        ];
    }
}
