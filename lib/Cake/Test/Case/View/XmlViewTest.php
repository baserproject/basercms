<?php
/**
 * XmlViewTest file
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.Test.Case.View
 * @since         CakePHP(tm) v 2.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('XmlView', 'View');

/**
 * XmlViewTest
 *
 * @package       Cake.Test.Case.View
 */
class XmlViewTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		Configure::write('debug', 0);
	}

/**
 * testRenderWithoutView method
 *
 * @return void
 */
	public function testRenderWithoutView() {
		$Request = new CakeRequest();
		$Response = new CakeResponse();
		$Controller = new Controller($Request, $Response);
		$data = ['users' => ['user' => ['user1', 'user2']]];
		$Controller->set(['users' => $data, '_serialize' => 'users']);
		$View = new XmlView($Controller);
		$output = $View->render(false);

		$this->assertSame(Xml::build($data)->asXML(), $output);
		$this->assertSame('application/xml', $Response->type());

		$data = [
			[
				'User' => [
					'username' => 'user1'
				]
			],
			[
				'User' => [
					'username' => 'user2'
				]
			]
		];
		$Controller->set(['users' => $data, '_serialize' => 'users']);
		$View = new XmlView($Controller);
		$output = $View->render(false);

		$expected = Xml::build(['response' => ['users' => $data]])->asXML();
		$this->assertSame($expected, $output);

		$Controller->set('_rootNode', 'custom_name');
		$View = new XmlView($Controller);
		$output = $View->render(false);

		$expected = Xml::build(['custom_name' => ['users' => $data]])->asXML();
		$this->assertSame($expected, $output);
	}

/**
 * Test that rendering with _serialize does not load helpers
 *
 * @return void
 */
	public function testRenderSerializeNoHelpers() {
		$Request = new CakeRequest();
		$Response = new CakeResponse();
		$Controller = new Controller($Request, $Response);
		$Controller->helpers = ['Html'];
		$Controller->set([
			'_serialize' => 'tags',
			'tags' => ['cakephp', 'framework']
		]);
		$View = new XmlView($Controller);
		$View->render();
		$this->assertFalse(isset($View->Html), 'No helper loaded.');
	}

/**
 * Test that rendering with _serialize respects XML options.
 *
 * @return void
 */
	public function testRenderSerializeWithOptions() {
		$Request = new CakeRequest();
		$Response = new CakeResponse();
		$Controller = new Controller($Request, $Response);
		$data = [
			'_serialize' => ['tags'],
			'_xmlOptions' => ['format' => 'attributes', 'return' => 'domdocument'],
			'tags' => [
				'tag' => [
					[
						'id' => '1',
						'name' => 'defect'
					],
					[
						'id' => '2',
						'name' => 'enhancement'
					]
				]
			]
		];
		$Controller->set($data);
		$Controller->viewClass = 'Xml';
		$View = new XmlView($Controller);
		$result = $View->render();

		$expected = Xml::build(['response' => ['tags' => $data['tags']]], $data['_xmlOptions'])->saveXML();
		$this->assertSame($expected, $result);
	}

/**
 * Test that rendering with _serialize can work with string setting.
 *
 * @return void
 */
	public function testRenderSerializeWithString() {
		$Request = new CakeRequest();
		$Response = new CakeResponse();
		$Controller = new Controller($Request, $Response);
		$data = [
			'_serialize' => 'tags',
			'_xmlOptions' => ['format' => 'attributes'],
			'tags' => [
				'tags' => [
					'tag' => [
						[
							'id' => '1',
							'name' => 'defect'
						],
						[
							'id' => '2',
							'name' => 'enhancement'
						]
					]
				]
			]
		];
		$Controller->set($data);
		$Controller->viewClass = 'Xml';
		$View = new XmlView($Controller);
		$result = $View->render();

		$expected = Xml::build($data['tags'], $data['_xmlOptions'])->asXML();
		$this->assertSame($expected, $result);
	}

/**
 * Test render with an array in _serialize
 *
 * @return void
 */
	public function testRenderWithoutViewMultiple() {
		$Request = new CakeRequest();
		$Response = new CakeResponse();
		$Controller = new Controller($Request, $Response);
		$data = ['no' => 'nope', 'user' => 'fake', 'list' => ['item1', 'item2']];
		$Controller->set($data);
		$Controller->set('_serialize', ['no', 'user']);
		$View = new XmlView($Controller);
		$this->assertSame('application/xml', $Response->type());
		$output = $View->render(false);
		$expected = [
			'response' => ['no' => $data['no'], 'user' => $data['user']]
		];
		$this->assertSame(Xml::build($expected)->asXML(), $output);

		$Controller->set('_rootNode', 'custom_name');
		$View = new XmlView($Controller);
		$output = $View->render(false);
		$expected = [
			'custom_name' => ['no' => $data['no'], 'user' => $data['user']]
		];
		$this->assertSame(Xml::build($expected)->asXML(), $output);
	}

/**
 * Test render with an array in _serialize and alias
 *
 * @return void
 */
	public function testRenderWithoutViewMultipleAndAlias() {
		$Request = new CakeRequest();
		$Response = new CakeResponse();
		$Controller = new Controller($Request, $Response);
		$data = ['original_name' => 'my epic name', 'user' => 'fake', 'list' => ['item1', 'item2']];
		$Controller->set($data);
		$Controller->set('_serialize', ['new_name' => 'original_name', 'user']);
		$View = new XmlView($Controller);
		$this->assertSame('application/xml', $Response->type());
		$output = $View->render(false);
		$expected = [
			'response' => ['new_name' => $data['original_name'], 'user' => $data['user']]
		];
		$this->assertSame(Xml::build($expected)->asXML(), $output);

		$Controller->set('_rootNode', 'custom_name');
		$View = new XmlView($Controller);
		$output = $View->render(false);
		$expected = [
			'custom_name' => ['new_name' => $data['original_name'], 'user' => $data['user']]
		];
		$this->assertSame(Xml::build($expected)->asXML(), $output);
	}

/**
 * testRenderWithView method
 *
 * @return void
 */
	public function testRenderWithView() {
		App::build(['View' => [
			CAKE . 'Test' . DS . 'test_app' . DS . 'View' . DS
		]]);
		$Request = new CakeRequest();
		$Response = new CakeResponse();
		$Controller = new Controller($Request, $Response);
		$Controller->name = $Controller->viewPath = 'Posts';

		$data = [
			[
				'User' => [
					'username' => 'user1'
				]
			],
			[
				'User' => [
					'username' => 'user2'
				]
			]
		];
		$Controller->set('users', $data);
		$View = new XmlView($Controller);
		$output = $View->render('index');

		$expected = [
			'users' => ['user' => ['user1', 'user2']]
		];
		$expected = Xml::build($expected)->asXML();
		$this->assertSame($expected, $output);
		$this->assertSame('application/xml', $Response->type());
		$this->assertInstanceOf('HelperCollection', $View->Helpers);
	}

}
