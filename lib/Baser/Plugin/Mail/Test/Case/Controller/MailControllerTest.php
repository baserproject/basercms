<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Test.Case.Controller
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('MailController', 'Mail.Controller');
App::uses('MailMessage', 'Mail.Model');

class MailControllerTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.SiteConfig',
		'baser.Default.Page',
		'plugin.mail.Default/MailMessage',
	);

	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

/**
 * [test_index description]
 * @return [type] [description]
 */
	public function test_index() {
		// $result = $this->testAction('/contact/index');
	}

}
