<?php

/**
 * test for MailAppModel
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 * @package         Mail.Test.Case.Model
 * @copyright       Copyright 2008 - 2015, baserCMS Users Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0-beta
 * @license         http://basercms.net/license/index.html
 */
App::uses('MailAppModel', 'Mail.Model');

class MailAppTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.SiteConfig',
		'plugin.mail.Default/Message',
		'plugin.mail.Default/MailConfig',
		'plugin.mail.Default/MailContent',
		'plugin.mail.Default/MailField',
	);

	public function setUp() {
		$this->MailApp = ClassRegistry::init('MailAppModel');
		parent::setUp();
	}

	public function tearDown() {
		unset($this->MailApp);
		parent::tearDown();
	}

/**
 * データの消毒をおこなう
 * @return array
 */
	public function testSanitizeAndRestoreData() {
		
		$datas = array('<!--', '<a href="test">Test</a>');
		$expected = array('&lt;!--', '&lt;a href=&quot;test&quot;&gt;Test&lt;/a&gt;');

		$resultSanitized = $this->MailApp->sanitizeData($datas);
		$this->assertEquals($expected, $resultSanitized, 'データのサニタイズを正しく行えません');

		$resultRestored = $this->MailApp->restoreData($expected);
		$this->assertEquals($datas, $resultRestored, 'サニタイズされたデータを正しく復元できません');
	
	}

}
