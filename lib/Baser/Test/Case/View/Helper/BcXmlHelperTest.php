<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.View.Helper
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('View', 'View');
App::uses('BcXmlHelper', 'View/Helper');

/**
 * text helper library.
 *
 * @package Baser.Test.Case.View.Helper
 * @property BcTextHelper $Helper
 */
class BcXmlHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array
 */
	public $fixtures = array();

	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->BcXml = new BcXmlHelper($View);
	}

	public function tearDown() {
		unset($this->BcXml);
		parent::tearDown();
	}

/**
 * XML宣言を生成
 * IE6以外の場合のみ生成する
 * 
 * @param array $attrib
 * @param string $agent ユーザーエージェント
 * @param string $expected 期待値
 * @dataProvider headerDataProvider
 */
	public function testHeader($attrib, $agent, $expected) {

		$_SERVER['HTTP_USER_AGENT'] = $agent;

		$result = $this->BcXml->header($attrib);
		$this->assertEquals($expected, $result);
	}

	public function headerDataProvider() {
		return array(
			array(
				array('test' => 'testValue'),
				'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)',
				'<?xml version="1.0" encoding="UTF-8" test="testValue" ?>'
			),
			array(
				array('test1' => 'testValue1', 'test2' => 'testValue2'),
				'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)',
				'<?xml version="1.0" encoding="UTF-8" test1="testValue1" test2="testValue2" ?>'
			),
			array(
				array('test' => 'testValue'),
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0;)',
				''
			),
			array(
				array('encoding' => 'SJIS'),
				'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1',
				'<?xml version="1.0" encoding="SJIS" ?>'
			),
		);
	}

}