<?php

/**
 * test for BcTimeHelper
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('View', 'View');
App::uses('BcTimeHelper', 'View/Helper');

/**
 * @package Baser.Test.Case.View.Helper
 * @property BcTimeHelper $Helper
 */
class BcTimeHelperTest extends BaserTestCase {

	public function setUp() {
		parent::setUp();
		$this->Helper = new BcTimeHelper(new View(null));
	}

	public function tearDown() {
		unset($this->Helper);
		parent::tearDown();
	}

/**
 * @dataProvider nengoDataProvider
 */
	public function testNengo($data, $expects) {
		$result = $this->Helper->nengo($data);
		$this->assertSame($expects, $result);
	}

	public function nengoDataProvider() {
		return array(
			array('m', '明治'),
			array('t', '大正'),
			array('s', '昭和'),
			array('h', '平成'),
		);
	}

/**
 * @dataProvider warekiDataProvider
 */
	public function testWareki($data, $expects) {
		$data = 's-48/5/10';
		$result = $this->Helper->wareki($data);
		$this->assertSame($expects, $result);
	}

	public function warekiDataProvider() {
		return array(
			array('s-48/5/10', 's'),
		);
	}

/**
 * @dataProvider wyearDataProvider
 */
	public function testWyear($data, $expects) {
		$result = $this->Helper->wyear($data);
		$this->assertSame($expects, $result);
	}

	public function wyearDataProvider() {
		return array(
			array('s-48/5/10', '48'),
		);
	}

/**
 * @dataProvider convertToWarekiYearDataProvider
 */
	public function testConvertToWarekiYear($data, $expects, $message) {
		$result = $this->Helper->convertToWarekiYear($data);
		$this->assertSame($expects, $result, $message);
	}

	public function convertToWarekiYearDataProvider() {
		return array(
			array(1867, false, '明治以前'),
			array(1868, array('m-1'), '明治元年'),
			array(1912, array('m-45', 't-1'), '大正元年'),
			array(1913, array('t-2'), '大正2年'),
			array(1926, array('t-15', 's-1'), '昭和元年'),
			array(1927, array('s-2'), '昭和2年'),
			array(1989, array('s-64', 'h-1'), '平成元年'),
			array(1990, array('h-2'), '平成2年'),
		);
	}

/**
 * @dataProvider convertToSeirekiYearDataProvider
 */
	public function testConvertToSeirekiYear($data, $expects, $message) {
		$result = $this->Helper->convertToSeirekiYear($data);
		$this->assertSame($expects, $result, $message);
	}

	public function convertToSeirekiYearDataProvider() {
		return array(
			array('m-1', 1868, '明治元年'),
			array('m-45', 1912, '明治45年'),
			array('t-1', 1912, '大正元年'),
			array('t-2', 1913, '大正2年'),
			array('t-15', 1926, '大正15年'),
			array('s-1', 1926, '昭和元年'),
			array('s-2', 1927, '昭和2年'),
			array('s-64', 1989, '昭和64年'),
			array('h-1', 1989, '平成元年'),
			array('h-2', 1990, '平成2年'),
		);
	}

/**
 * @dataProvider convertToWarekiArrayDataProvider
 */
	public function testConvertToWarekiArray($data, $expects, $message) {
		$result = $this->Helper->convertToWarekiArray($data);
		$this->assertSame($expects, $result, $message);
	}

	public function convertToWarekiArrayDataProvider() {
		return array(
			array(null, '', '未入力'),
			array('invalid date', '', '不正な日付形式'),
			array('19120729', array('wareki' => true, 'year' => 'm-45', 'month' => '07', 'day' => '29'), '明治45年7月29日'),
			array('19120730', array('wareki' => true, 'year' => 't-1', 'month' => '07', 'day' => '30'), '大正元年7月30日'),
			array('19261224', array('wareki' => true, 'year' => 't-15', 'month' => '12', 'day' => '24'), '大正15年12月24日'),
			array('19261225', array('wareki' => true, 'year' => 's-1', 'month' => '12', 'day' => '25'), '昭和元年12月25日'),
			array('19890107', array('wareki' => true, 'year' => 's-64', 'month' => '01', 'day' => '07'), '昭和64年1月7日'),
			array('19890108', array('wareki' => true, 'year' => 'h-1', 'month' => '01', 'day' => '08'), '平成元年1月8日'),
		);
	}

/**
 * @dataProvider convertToWarekiDataProvider
 */
	public function testConvertToWareki($data, $expects, $message) {
		$result = $this->Helper->convertToWareki($data);
		$this->assertSame($expects, $result, $message);
	}

	public function convertToWarekiDataProvider() {
		return array(
			array(null, '', '未入力'),
			array('invalid date', '', '不正な日付形式'),
			array('19120729', 'm-45/07/29', '明治45年7月29日'),
			array('19120730', 't-1/07/30', '大正元年7月30日'),
			array('19261224', 't-15/12/24', '大正15年12月24日'),
			array('19261225', 's-1/12/25', '昭和元年12月25日'),
			array('19890107', 's-64/01/07', '昭和64年1月7日'),
			array('19890108', 'h-1/01/08', '平成元年1月8日'),
		);
	}

/**
 * @dataProvider minutesDataProvider
 */
	public function testMinutes($data, $expects, $message) {
		$result = $this->Helper->minutes($data);
		$this->assertSame($expects, $result, $message);
	}

	public function minutesDataProvider() {
		return array(
			array('invalid time', null, '不正な日付形式'),
			array('1 days', '1440分', '1日'),
			array('2 week', '20160分', '2週間'),
		);
	}

/**
 * @dataProvider formatDataProvider
 */
	public function testFormat($format, $date, $expects, $message) {
		$result = $this->Helper->format($format, $date);
		$this->assertSame($expects, $result, $message);
	}

	public function formatDataProvider() {
		return array(
			array('Y-m-d', '2012-03-04 05:06:07', '2012-03-04', '日付'),
			array('Y/m/d H:i:s', '2012-03-04 05:06:07', '2012/03/04 05:06:07', '日時'),
			array('Y-m-d', '0000-00-00 00:00:00', '', 'nll datetime'),
			array('Y-m-d', false, '', 'date is false'),
			array('Y-m-d', 0, '', 'date is zero'),
		);
	}

/**
 * @dataProvider pastDaysDataProvider
 */
	public function testPastDays($date, $days, $nowDate, $expects, $message) {
		$now = strtotime($nowDate);
		$result = $this->Helper->pastDays($date, $days, $now);
		$this->assertSame($expects, $result, $message);
	}

	public function pastDaysDataProvider() {
		return array(
			array('2012-10-03 00:00:00', 1, '2012-10-04 00:00:01', true, '指定日から1日経過している'),
			array('2012-10-03 00:00:00', 1, '2012-10-04 00:00:00', false, '指定日から1日経過していない'),
			array('2012-10-03 00:00:00', 30, '2012-11-02 00:00:01', true, '指定日から30日経過している'),
			array('2012-10-03 00:00:00', 30, '2012-11-02 00:00:00', false, '指定日から30日経過していない'),
		);
	}

}
