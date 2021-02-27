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

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcFormHelper;

/**
 * Class BcFormHelperTest
 * @package BaserCore\Test\TestCase\View\Helper
 * @property BcFormHelper $BcForm
 */
class BcFormHelperTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.UserGroups',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcForm = new BcFormHelper(new BcAdminAppView($this->getRequest('/contacts/add')));
    }

	/**
	 * コントロールソースを取得する
	 * Model側でメソッドを用意しておく必要がある
	 *
	 * @param string $field フィールド名
	 * @param array $options
	 * @dataProvider getControlSourceProvider
	 */
	public function testGetControlSource($field, $expected)
	{
		$result = $this->BcForm->getControlSource($field);
		if($result) {
		    $result = $result->toArray();
		} else {
		    $result = [];
		}
		$this->assertEquals($expected, $result);
	}

	public function getControlSourceProvider()
	{
		return [
			['hoge', []],
			['', []],
			['BaserCore.Users.user_group_id', [1 => 'システム管理',2 => 'サイト運営者']]
		];
	}

    /**
     * testDatePicker
     * @param string $fieldName フィールド文字列
     * @param array $attributes HTML属性
     * @param string $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider datePickerDataProvider
     */
    public function testDatePicker($fieldName, $attributes, $expected, $message)
    {
        $result = $this->BcForm->datePicker($fieldName, $attributes);
        $this->assertRegExp('/' . $expected . '/s', $result, $message);
    }

    public function datePickerDataProvider()
    {
        return [
            ['baser', [], 'type="text".*"#baser"', 'datepicker()が出力できません'],
            ['baser', ['test1' => 'testValue1'], 'test1="testValue1"', '要素に属性を付与できません'],
            ['baser', ['value' => '2010-4-1'], 'value="2010\/4\/1"', '時間を指定できません'],
        ];
    }

    /**
     * 日付カレンダーと時間フィールド
     *
     * @param string $fieldName
     * @param array $attributes
     * @param string $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider dateTimePickerDataProvider
     */
    public function testDateTimePicker($fieldName, $attributes, $expected, $message)
    {
        $result = $this->BcForm->dateTimePicker($fieldName, $attributes);
        $this->assertRegExp('/' . $expected . '/s', $result, $message);
    }

    public function dateTimePickerDataProvider()
    {
        return [
            ['baser', [], '<span><label.*?>日付.+?<input .+?<span><label.*?>時間.+?<input .+?<input type="hidden".+?', 'dateTimePicker()が出力されません'],
            ['baser', ['value' => '2010-4-1 11:22:33'], 'value="2010\/4\/1".*value="11:22:33".*value="2010-4-1 11:22:33"', '時間指定が正しく出力できません'],
            ['baser', ['value' => '2010-04-01 11:22:33'], 'value="2010\/04\/01".*value="11:22:33".*value="2010-04-01 11:22:33"', '時間指定が正しく出力できません'],
            ['baser', ['value' => '2010-4-1 '], 'value="2010\/4\/1".*value="".* value="2010-4-1 "', '時間を指定いない場合出力できません'],
            ['baser', ['value' => '2010 hogehoge'], 'value="2010".*value="hogehoge".*value="2010 hogehoge"', '時間指定が不適切でない場合出力できません'],
            ['baser', ['value' => 'hoge hogehoge'], 'value="hoge".*value="hogehoge".*value="hoge hogehoge"', '時間指定が不適切でない場合出力できません']
        ];
    }

}
