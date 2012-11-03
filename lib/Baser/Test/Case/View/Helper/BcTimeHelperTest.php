<?php

/**
 * test for BcTimeHelper
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('View', 'View');
App::uses('BcTimeHelper', 'View/Helper');

/**
 * @package Baser.Test.Case
 * @property BcTimeHelper $Helper
 */
class BcTimeHelperTest extends CakeTestCase {

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

	public function testWareki() {
		$this->markTestIncomplete();
	}

	public function testWyear() {
		$this->markTestIncomplete();
	}

	public function testConvertToWarekiYear() {
		$this->markTestIncomplete();
	}

	public function testConvertToSeirekiYear() {
		$this->markTestIncomplete();
	}

	public function testConvertToWarekiArray() {
		$this->markTestIncomplete();
	}

	public function testConvertToWareki() {
		$this->markTestIncomplete();
	}

	public function testMinutes() {
		$this->markTestIncomplete();
	}

	public function testFormat() {
		$this->markTestIncomplete();
	}

	public function testPastDays() {
		$this->markTestIncomplete();
	}
}
