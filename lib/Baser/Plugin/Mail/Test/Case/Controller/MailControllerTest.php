<?php

/**
 * test for MailController
 *
 * PHP versions 5
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 * @package         Mail Plugin
 * @copyright       Copyright 2008 - 2013, baserCMS Users Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0-beta
 * @license         http://basercms.net/license/index.html
 */
App::uses('MailController', 'Mail.Controller');
App::uses('Message', 'Mail.Model');

class MailControllerTest extends ControllerTestCase {

	public $fixtures = array(
		'baser.site_config',
		'baser.page',
		'plugin.blog.blog_content',
		'plugin.mail.message',
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
