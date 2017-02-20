<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Model
 * @since			baserCMS v 4.0.2
 * @license			http://basercms.net/license/index.html
 */
App::uses('Site', 'Model');

/**
 * SiteTest class
 *
 * @property Site $Site
 * @package Baser.Test.Case.Model
 */
class SiteTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.Site',
		'baser.Default.ContentFolder',
		'baser.Default.Content',
		'baser.Default.User'
	);

	public function setUp() {
		parent::setUp();
		$this->Site = ClassRegistry::init('Site');
	}

	public function tearDown() {
		unset($this->Site);
		parent::tearDown();
	}

/**
 * testResetDevice
 */
	public function testResetDevice() {
		$this->Site->resetDevice();
		$sites = $this->Site->find('all', ['recursive' => -1]);
		foreach($sites as $site) {
			$this->assertEquals($site['Site']['device'], '');
			$this->assertFalse($site['Site']['same_main_url']);
			$this->assertFalse($site['Site']['auto_redirect']);
			$this->assertFalse($site['Site']['auto_link']);
		}
	}

/**
 * testResetDevice
 */
	public function testResetLang() {
		$this->Site->resetLang();
		$sites = $this->Site->find('all', ['recursive' => -1]);
		foreach($sites as $site) {
			$this->assertEquals($site['Site']['lang'], '');
			$this->assertFalse($site['Site']['same_main_url']);
			$this->assertTrue($site['Site']['auto_redirect']);
		}
	}
}
