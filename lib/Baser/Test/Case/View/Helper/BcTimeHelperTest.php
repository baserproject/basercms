<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.View.Helper
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('View', 'View');
App::uses('BcTimeHelper', 'View/Helper');

/**
 * @package Baser.Test.Case.View.Helper
 * @property BcTimeHelper $Helper
 */
class BcTimeHelperTest extends BaserTestCase
{

	public function setUp()
	{
		parent::setUp();
		$this->Helper = new BcTimeHelper(new View(null));
	}

	public function tearDown()
	{
		unset($this->Helper);
		parent::tearDown();
	}

	/**
	 * 年号を取得
	 *
	 * @dataProvider nengoDataProvider
	 */
	public function testNengo($data, $expects)
	{
		$result = $this->Helper->nengo($data);
		$this->assertSame($expects, $result);
	}

	public function nengoDataProvider()
	{
		return [
			['m', '明治'],
			['t', '大正'],
			['s', '昭和'],
			['h', '平成'],
			['r', '令和'],
		];
	}

	/**
	 * 和暦を取得（アルファベット）
	 *
	 * @dataProvider warekiDataProvider
	 */
	public function testWareki($data, $expects)
	{
		$data = 's-48/5/10';
		$result = $this->Helper->wareki($data);
		$this->assertSame($expects, $result);
	}

	public function warekiDataProvider()
	{
		return [
			['s-48/5/10', 's'],
		];
	}

	/**
	 * 和暦の年を取得
	 *
	 * @dataProvider wyearDataProvider
	 */
	public function testWyear($data, $expects)
	{
		$result = $this->Helper->wyear($data);
		$this->assertSame($expects, $result);
	}

	public function wyearDataProvider()
	{
		return [
			['s-48/5/10', '48'],
		];
	}

	/**
	 * 西暦を和暦の年に変換する
	 * 西暦をまたがる場合があるので配列で返す
	 *
	 * @dataProvider convertToWarekiYearDataProvider
	 */
	public function testConvertToWarekiYear($data, $expects, $message)
	{
		$result = $this->Helper->convertToWarekiYear($data);
		$this->assertSame($expects, $result, $message);
	}

	public function convertToWarekiYearDataProvider()
	{
		return [
			[1867, false, '明治以前'],
			[1868, ['m-1'], '明治元年'],
			[1912, ['m-45', 't-1'], '大正元年'],
			[1913, ['t-2'], '大正2年'],
			[1926, ['t-15', 's-1'], '昭和元年'],
			[1927, ['s-2'], '昭和2年'],
			[1989, ['s-64', 'h-1'], '平成元年'],
			[1990, ['h-2'], '平成2年'],
			[2019, ['h-31', 'r-1'], '令和元年'],
			[2020, ['r-2'], '令和2年'],
		];
	}

	/**
	 * 和暦の年を西暦に変換する
	 * 和暦のフォーマット例：s-48
	 *
	 * @dataProvider convertToSeirekiYearDataProvider
	 */
	public function testConvertToSeirekiYear($data, $expects, $message)
	{
		$result = $this->Helper->convertToSeirekiYear($data);
		$this->assertSame($expects, $result, $message);
	}

	public function convertToSeirekiYearDataProvider()
	{
		return [
			['m-1', 1868, '明治元年'],
			['m-45', 1912, '明治45年'],
			['t-1', 1912, '大正元年'],
			['t-2', 1913, '大正2年'],
			['t-15', 1926, '大正15年'],
			['s-1', 1926, '昭和元年'],
			['s-2', 1927, '昭和2年'],
			['s-64', 1989, '昭和64年'],
			['h-1', 1989, '平成元年'],
			['h-2', 1990, '平成2年'],
			['h-31', 2019, '平成31年'],
			['r-1', 2019, '令和元年'],
			['r-2', 2020, '令和2年'],
		];
	}

	/**
	 * 和暦変換(配列で返す)
	 *
	 * @dataProvider convertToWarekiArrayDataProvider
	 */
	public function testConvertToWarekiArray($data, $expects, $message)
	{
		$result = $this->Helper->convertToWarekiArray($data);
		$this->assertSame($expects, $result, $message);
	}

	public function convertToWarekiArrayDataProvider()
	{
		return [
			[null, '', '未入力'],
			['invalid date', '', '不正な日付形式'],
			['19120729', ['wareki' => true, 'year' => 'm-45', 'month' => '07', 'day' => '29'], '明治45年7月29日'],
			['19120730', ['wareki' => true, 'year' => 't-1', 'month' => '07', 'day' => '30'], '大正元年7月30日'],
			['19261224', ['wareki' => true, 'year' => 't-15', 'month' => '12', 'day' => '24'], '大正15年12月24日'],
			['19261225', ['wareki' => true, 'year' => 's-1', 'month' => '12', 'day' => '25'], '昭和元年12月25日'],
			['19890107', ['wareki' => true, 'year' => 's-64', 'month' => '01', 'day' => '07'], '昭和64年1月7日'],
			['19890108', ['wareki' => true, 'year' => 'h-1', 'month' => '01', 'day' => '08'], '平成元年1月8日'],
			['20190430', ['wareki' => true, 'year' => 'h-31', 'month' => '04', 'day' => '30'], '平成31年4月30日'],
			['20190501', ['wareki' => true, 'year' => 'r-1', 'month' => '05', 'day' => '01'], '令和元年5月1日'],
		];
	}

	/**
	 * 和暦変換
	 *
	 * @dataProvider convertToWarekiDataProvider
	 */
	public function testConvertToWareki($data, $expects, $message)
	{
		$result = $this->Helper->convertToWareki($data);
		$this->assertSame($expects, $result, $message);
	}

	public function convertToWarekiDataProvider()
	{
		return [
			[null, '', '未入力'],
			['invalid date', '', '不正な日付形式'],
			['19120729', 'm-45/07/29', '明治45年7月29日'],
			['19120730', 't-1/07/30', '大正元年7月30日'],
			['19261224', 't-15/12/24', '大正15年12月24日'],
			['19261225', 's-1/12/25', '昭和元年12月25日'],
			['19890107', 's-64/01/07', '昭和64年1月7日'],
			['19890108', 'h-1/01/08', '平成元年1月8日'],
			['20190430', 'h-31/04/30', '平成31年4月30日'],
			['20190501', 'r-1/05/01', '令和元年5月1日'],
		];
	}

	/**
	 * 文字列から時間（分）を取得
	 *
	 * @dataProvider minutesDataProvider
	 */
	public function testMinutes($data, $expects, $message)
	{
		$result = $this->Helper->minutes($data);
		$this->assertSame($expects, $result, $message);
	}

	public function minutesDataProvider()
	{
		return [
			['invalid time', null, '不正な日付形式'],
			['1 days', '1440分', '1日'],
			['2 week', '20160分', '2週間'],
		];
	}

	/**
	 * format 拡張
	 *
	 * @dataProvider formatDataProvider
	 */
	public function testFormat($format, $date, $expects, $message)
	{
		$result = $this->Helper->format($format, $date);
		$this->assertSame($expects, $result, $message);
	}

	public function formatDataProvider()
	{
		return [
			['Y-m-d', '2012-03-04 05:06:07', '2012-03-04', '日付'],
			['Y/m/d H:i:s', '2012-03-04 05:06:07', '2012/03/04 05:06:07', '日時'],
			['Y-m-d', '0000-00-00 00:00:00', '', 'nll datetime'],
			['Y-m-d', false, '', 'date is false'],
			['Y-m-d', 0, '', 'date is zero'],
		];
	}

	/**
	 * 指定した日数が経過しているか確認する
	 * 経過していない場合はtrueを返す
	 * 日付が確認できなかった場合もtrueを返す
	 *
	 * @dataProvider pastDaysDataProvider
	 */
	public function testPastDays($date, $days, $nowDate, $expects, $message)
	{
		$now = strtotime($nowDate);
		$result = $this->Helper->pastDays($date, $days, $now);
		$this->assertSame($expects, $result, $message);
	}

	public function pastDaysDataProvider()
	{
		return [
			['2012-10-03 00:00:00', 1, '2012-10-04 00:00:01', true, '指定日から1日経過している'],
			['2012-10-03 00:00:00', 1, '2012-10-04 00:00:00', false, '指定日から1日経過していない'],
			['2012-10-03 00:00:00', 30, '2012-11-02 00:00:01', true, '指定日から30日経過している'],
			['2012-10-03 00:00:00', 30, '2012-11-02 00:00:00', false, '指定日から30日経過していない'],
		];
	}

	/**
	 * 日本の曜日名を1文字 + $suffixの形式で取得する
	 * - 引数により、指定しない場合は本日の曜日
	 * - 文字列で、strtotime関数で解析可能な場合は解析された日付の曜日
	 *
	 * @dataProvider getJpWeekDataProvider
	 */
	public function testGetJpWeek($dateStr, $suffix, $expects, $message)
	{
		$result = $this->Helper->getJpWeek($dateStr, $suffix);
		$this->assertSame($expects, $result, $message);
	}

	public function getJpWeekDataProvider()
	{
		return [
			['2015-8-11', '', '火', '火曜日'],
			['2015-8-11', 'ようび', '火ようび', '$suffix変更'],
			['2015-8-111', '', '', '日付として解析できなかった場合'],
		];
	}

	/**
	 * 曜日情報を出力する
	 * - 曜日情報が正しく取得できない場合は接尾辞も表示しない
	 * - ex) <?php $this->BcTime->jpWeek($post['posts_date'], '曜日'); ?>
	 *
	 * @dataProvider jpWeekDataProvider
	 */
	public function testJpWeek($dateStr, $suffix, $expects, $message)
	{
		$this->expectOutputString($expects);
		$this->Helper->jpWeek($dateStr, $suffix);
	}

	public function jpWeekDataProvider()
	{
		return [
			['2015-8-11', '', '火', '火曜日'],
			['2015-8-11', 'ようび', '火ようび', '$suffix変更'],
			['2015-8-111', '', '', '日付として解析できなかった場合'],
		];
	}
}
