<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Controller.Component
 * @since           baserCMS v 4.1.6
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcContentsComponent', 'Controller/Component');
App::uses('Controller', 'Controller');

/**
 * Class BcMessageTestController
 *
 * @package Baser.Test.Case.Controller.Component
 * @property BcMessageComponent $BcMessage
 */
class BcMessageTestController extends Controller
{
	public $components = ['Flash', 'BcMessage'];
}

/**
 * Class BcMessageComponentTest
 *
 * @package Baser.Test.Case.Controller.Component
 */
class BcMessageComponentTest extends BaserTestCase
{

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.Dblog',
	];

	/**
	 * BcMessageComponent
	 *
	 * @var BcMessageComponent
	 */
	public $BcMessage;

	/**
	 * Set Up
	 */
	public function setUp()
	{
		parent::setUp();
		$controller = new BcMessageTestController(new CakeRequest(), new CakeResponse());
		$controller->constructClasses();
		$controller->startupProcess();
		$this->BcMessage = $controller->BcMessage;
	}

	/**
	 * Tear Down
	 */
	public function tearDown()
	{
		parent::tearDown();
		unset($this->BcMessage);
		$_SESSION['Message'] = null;
	}

	/**
	 * set
	 */
	public function testSet()
	{
		// notice message
		$this->BcMessage->set('test');
		$this->assertEquals('test', $_SESSION['Message']['flash'][0]['message']);
		$this->assertEquals('notice-message', $_SESSION['Message']['flash'][0]['params']['class']);

		// multi message
		$this->BcMessage->set('test');
		$this->assertEquals(2, count($_SESSION['Message']['flash']));
		$_SESSION['Message'] = null;

		// alert message
		$this->BcMessage->set('test', true);
		$this->assertEquals('alert-message', $_SESSION['Message']['flash'][0]['params']['class']);
		$this->BcMessage->set('test', false, true);
		$_SESSION['Message'] = null;

		// set db log
		/* @var Dblog $dbLogModel */
		$dbLogModel = ClassRegistry::init('DbLog');
		$dbLogModel->field('name', [], 'id DESC');
		$this->assertEquals('test', $dbLogModel->field('name', [], 'id DESC'));
		$_SESSION['Message'] = null;

		// not flash message
		$this->BcMessage->set('test', false, false, false);
		$this->assertEquals(null, $_SESSION['Message']);
	}

	/**
	 * setSuccess
	 */
	public function testSetSuccess()
	{
		$this->BcMessage->setSuccess('test');
		$this->assertEquals('notice-message', $_SESSION['Message']['flash'][0]['params']['class']);
	}

	/**
	 * setError
	 */
	public function testSetError()
	{
		$this->BcMessage->setError('test');
		$this->assertEquals('alert-message', $_SESSION['Message']['flash'][0]['params']['class']);
	}

	/**
	 * setWarning
	 */
	public function testSetWarning()
	{
		$this->BcMessage->setWarning('test');
		$this->assertEquals('warning-message', $_SESSION['Message']['flash'][0]['params']['class']);
	}

	/**
	 * setInfo
	 */
	public function testSetInfo()
	{
		$this->BcMessage->setInfo('test');
		$this->assertEquals('info-message', $_SESSION['Message']['flash'][0]['params']['class']);
	}

}
