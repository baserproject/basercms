<?php
/**
 * ModelValidationTest file
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
 * @package       Cake.Test.Case.Model
 * @since         CakePHP(tm) v 1.2.0.4206
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

require_once dirname(__FILE__) . DS . 'ModelTestBase.php';

/**
 * ModelValidationTest
 *
 * @package       Cake.Test.Case.Model
 */
class ModelValidationTest extends BaseModelTest {

/**
 * override locale to the default (eng).
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('Config.language', 'eng');
	}

/**
 * Tests validation parameter order in custom validation methods
 *
 * @return void
 */
	public function testValidationParams() {
		$TestModel = new ValidationTest1();
		$TestModel->validate['title'] = [
			'rule' => 'customValidatorWithParams',
			'required' => true
		];
		$TestModel->create(['title' => 'foo']);
		$TestModel->invalidFields();

		$expected = [
			'data' => [
				'title' => 'foo'
			],
			'validator' => [
				'rule' => 'customValidatorWithParams',
				'on' => null,
				'last' => true,
				'allowEmpty' => false,
				'required' => true,
				'message' => null
			],
			'or' => true,
			'ignoreOnSame' => 'id'
		];
		$this->assertEquals($expected, $TestModel->validatorParams);

		$TestModel->validate['title'] = [
			'rule' => 'customValidatorWithMessage',
			'required' => true
		];
		$expected = [
			'title' => ['This field will *never* validate! Muhahaha!']
		];

		$this->assertEquals($expected, $TestModel->invalidFields());

		$TestModel->validate['title'] = [
			'rule' => ['customValidatorWithSixParams', 'one', 'two', null, 'four'],
			'required' => true
		];
		$TestModel->create(['title' => 'foo']);
		$TestModel->invalidFields();
		$expected = [
			'data' => [
				'title' => 'foo'
			],
			'one' => 'one',
			'two' => 'two',
			'three' => null,
			'four' => 'four',
			'five' => [
				'rule' => [1 => 'one', 2 => 'two', 3 => null, 4 => 'four'],
				'on' => null,
				'last' => true,
				'allowEmpty' => false,
				'required' => true,
				'message' => null
			],
			'six' => 6
		];
		$this->assertEquals($expected, $TestModel->validatorParams);

		$TestModel->validate['title'] = [
			'rule' => ['customValidatorWithSixParams', 'one', ['two'], null, 'four', ['five' => 5]],
			'required' => true
		];
		$TestModel->create(['title' => 'foo']);
		$TestModel->invalidFields();
		$expected = [
			'data' => [
				'title' => 'foo'
			],
			'one' => 'one',
			'two' => ['two'],
			'three' => null,
			'four' => 'four',
			'five' => ['five' => 5],
			'six' => [
				'rule' => [1 => 'one', 2 => ['two'], 3 => null, 4 => 'four', 5 => ['five' => 5]],
				'on' => null,
				'last' => true,
				'allowEmpty' => false,
				'required' => true,
				'message' => null
			]
		];
		$this->assertEquals($expected, $TestModel->validatorParams);
	}

/**
 * Tests validation parameter fieldList in invalidFields
 *
 * @return void
 */
	public function testInvalidFieldsWithFieldListParams() {
		$TestModel = new ValidationTest1();
		$TestModel->validate = $validate = [
			'title' => [
				'rule' => 'alphaNumeric',
				'required' => true
			],
			'name' => [
				'rule' => 'alphaNumeric',
				'required' => true
		]];
		$TestModel->set(['title' => '$$', 'name' => '##']);
		$TestModel->invalidFields(['fieldList' => ['title']]);
		$expected = [
			'title' => ['This field cannot be left blank']
		];
		$this->assertEquals($expected, $TestModel->validationErrors);
		$TestModel->validationErrors = [];

		$TestModel->invalidFields(['fieldList' => ['name']]);
		$expected = [
			'name' => ['This field cannot be left blank']
		];
		$this->assertEquals($expected, $TestModel->validationErrors);
		$TestModel->validationErrors = [];

		$TestModel->invalidFields(['fieldList' => ['name', 'title']]);
		$expected = [
			'name' => ['This field cannot be left blank'],
			'title' => ['This field cannot be left blank']
		];
		$this->assertEquals($expected, $TestModel->validationErrors);
		$TestModel->validationErrors = [];

		$TestModel->whitelist = ['name'];
		$TestModel->invalidFields();
		$expected = ['name' => ['This field cannot be left blank']];
		$this->assertEquals($expected, $TestModel->validationErrors);

		$this->assertEquals($TestModel->validate, $validate);
	}

/**
 * Test that invalidFields() integrates well with save(). And that fieldList can be an empty type.
 *
 * @return void
 */
	public function testInvalidFieldsWhitelist() {
		$TestModel = new ValidationTest1();
		$TestModel->validate = [
			'title' => [
				'rule' => 'alphaNumeric',
				'required' => true
			],
			'name' => [
				'rule' => 'alphaNumeric',
				'required' => true
		]];

		$TestModel->whitelist = ['name'];
		$TestModel->save(['name' => '#$$#', 'title' => '$$$$']);

		$expected = ['name' => ['This field cannot be left blank']];
		$this->assertEquals($expected, $TestModel->validationErrors);
	}

/**
 * testValidates method
 *
 * @return void
 */
	public function testValidates() {
		$TestModel = new TestValidate();

		$TestModel->validate = [
			'user_id' => 'numeric',
			'title' => ['allowEmpty' => false, 'rule' => 'notBlank'],
			'body' => 'notBlank'
		];

		$data = ['TestValidate' => [
			'user_id' => '1',
			'title' => '',
			'body' => 'body'
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = ['TestValidate' => [
			'user_id' => '1',
			'title' => 'title',
			'body' => 'body'
		]];
		$result = $TestModel->create($data) && $TestModel->validates();
		$this->assertTrue($result);

		$data = ['TestValidate' => [
			'user_id' => '1',
			'title' => '0',
			'body' => 'body'
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertTrue($result);

		$data = ['TestValidate' => [
			'user_id' => '1',
			'title' => 0,
			'body' => 'body'
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertTrue($result);

		$TestModel->validate['modified'] = ['allowEmpty' => true, 'rule' => 'date'];

		$data = ['TestValidate' => [
			'user_id' => '1',
			'title' => 0,
			'body' => 'body',
			'modified' => ''
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertTrue($result);

		$data = ['TestValidate' => [
			'user_id' => '1',
			'title' => 0,
			'body' => 'body',
			'modified' => '2007-05-01'
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertTrue($result);

		$data = ['TestValidate' => [
			'user_id' => '1',
			'title' => 0,
			'body' => 'body',
			'modified' => 'invalid-date-here'
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = ['TestValidate' => [
			'user_id' => '1',
			'title' => 0,
			'body' => 'body',
			'modified' => 0
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = ['TestValidate' => [
			'user_id' => '1',
			'title' => 0,
			'body' => 'body',
			'modified' => '0'
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$TestModel->validate['modified'] = ['allowEmpty' => false, 'rule' => 'date'];

		$data = ['TestValidate' => ['modified' => null]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = ['TestValidate' => ['modified' => false]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = ['TestValidate' => ['modified' => '']];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = ['TestValidate' => [
			'modified' => '2007-05-01'
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertTrue($result);

		$TestModel->validate['slug'] = ['allowEmpty' => false, 'rule' => ['maxLength', 45]];

		$data = ['TestValidate' => [
			'user_id' => '1',
			'title' => 0,
			'body' => 'body',
			'slug' => ''
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = ['TestValidate' => [
			'user_id' => '1',
			'title' => 0,
			'body' => 'body',
			'slug' => 'slug-right-here'
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertTrue($result);

		$data = ['TestValidate' => [
			'user_id' => '1',
			'title' => 0,
			'body' => 'body',
			'slug' => 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz'
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$TestModel->validate = [
			'number' => [
				'rule' => 'validateNumber',
				'min' => 3,
				'max' => 5
			],
			'title' => [
				'allowEmpty' => false,
				'rule' => 'notBlank'
		]];

		$data = ['TestValidate' => [
			'title' => 'title',
			'number' => '0'
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = ['TestValidate' => [
			'title' => 'title',
			'number' => 0
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = ['TestValidate' => [
			'title' => 'title',
			'number' => '3'
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertTrue($result);

		$data = ['TestValidate' => [
			'title' => 'title',
			'number' => 3
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertTrue($result);

		$TestModel->validate = [
			'number' => [
				'rule' => 'validateNumber',
				'min' => 5,
				'max' => 10
			],
			'title' => [
				'allowEmpty' => false,
				'rule' => 'notBlank'
		]];

		$data = ['TestValidate' => [
			'title' => 'title',
			'number' => '3'
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = ['TestValidate' => [
			'title' => 'title',
			'number' => 3
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$TestModel->validate = [
			'title' => [
				'allowEmpty' => false,
				'rule' => 'validateTitle'
		]];

		$data = ['TestValidate' => ['title' => '']];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = ['TestValidate' => ['title' => 'new title']];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = ['TestValidate' => ['title' => 'title-new']];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertTrue($result);

		$TestModel->validate = ['title' => [
			'allowEmpty' => true,
			'rule' => 'validateTitle'
		]];
		$data = ['TestValidate' => ['title' => '']];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertTrue($result);

		$TestModel->validate = [
			'title' => [
				'length' => [
					'allowEmpty' => true,
					'rule' => ['maxLength', 10]
		]]];
		$data = ['TestValidate' => ['title' => '']];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertTrue($result);

		$TestModel->validate = [
			'title' => [
				'rule' => ['userDefined', 'Article', 'titleDuplicate']
		]];
		$data = ['TestValidate' => ['title' => 'My Article Title']];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = ['TestValidate' => [
			'title' => 'My Article With a Different Title'
		]];
		$result = $TestModel->create($data);
		$this->assertEquals($data, $result);
		$result = $TestModel->validates();
		$this->assertTrue($result);

		$TestModel->validate = [
			'title' => [
				'tooShort' => ['rule' => ['minLength', 50]],
				'onlyLetters' => ['rule' => '/^[a-z]+$/i']
			],
		];
		$data = ['TestValidate' => [
			'title' => 'I am a short string'
		]];
		$TestModel->create($data);
		$result = $TestModel->validates();
		$this->assertFalse($result);
		$result = $TestModel->validationErrors;
		$expected = [
			'title' => ['tooShort']
		];
		$this->assertEquals($expected, $result);

		$TestModel->validate = [
			'title' => [
				'tooShort' => [
					'rule' => ['minLength', 50],
					'last' => false
				],
				'onlyLetters' => ['rule' => '/^[a-z]+$/i']
			],
		];
		$data = ['TestValidate' => [
			'title' => 'I am a short string'
		]];
		$TestModel->create($data);
		$result = $TestModel->validates();
		$this->assertFalse($result);
		$result = $TestModel->validationErrors;
		$expected = [
			'title' => ['tooShort', 'onlyLetters']
		];
		$this->assertEquals($expected, $result);
		$result = $TestModel->validationErrors;
		$this->assertEquals($expected, $result);
	}

/**
 * test that validates() still performs correctly when useTable = false on the model.
 *
 * @return void
 */
	public function testValidatesWithNoTable() {
		$TestModel = new TheVoid();
		$TestModel->validate = [
			'title' => [
				'notEmpty' => [
					'rule' => ['notBlank'],
					'required' => true,
				],
				'tooShort' => [
					'rule' => ['minLength', 10],
				],
			],
		];
		$data = [
			'TheVoid' => [
				'title' => 'too short',
			],
		];
		$TestModel->create($data);
		$result = $TestModel->validates();
		$this->assertFalse($result);

		$data = [
			'TheVoid' => [
				'id' => '1',
				'title' => 'A good title',
			],
		];
		$TestModel->create($data);
		$result = $TestModel->validates();
		$this->assertTrue($result);
	}

/**
 * test that validates() checks all the 'with' associations as well for validation
 * as this can cause partial/wrong data insertion.
 *
 * @return void
 */
	public function testValidatesWithAssociations() {
		$this->loadFixtures('Something', 'SomethingElse', 'JoinThing');
		$data = [
			'Something' => [
				'id' => 5,
				'title' => 'Extra Fields',
				'body' => 'Extra Fields Body',
				'published' => '1'
			],
			'SomethingElse' => [
				['something_else_id' => 1, 'doomed' => '']
			]
		];

		$Something = new Something();
		$JoinThing = $Something->JoinThing;

		$JoinThing->validate = ['doomed' => ['rule' => 'notBlank']];

		$expectedError = ['doomed' => ['This field cannot be left blank']];

		$Something->create();
		$result = $Something->save($data);
		$this->assertFalse($result, 'Save occurred even when with models failed. %s');
		$this->assertEquals($expectedError, $JoinThing->validationErrors);
		$count = $Something->find('count', ['conditions' => ['Something.id' => $data['Something']['id']]]);
		$this->assertSame(0, $count);

		$data = [
			'Something' => [
				'id' => 5,
				'title' => 'Extra Fields',
				'body' => 'Extra Fields Body',
				'published' => '1'
			],
			'SomethingElse' => [
				['something_else_id' => 1, 'doomed' => 1],
				['something_else_id' => 1, 'doomed' => '']
			]
		];
		$Something->create();
		$result = $Something->save($data);
		$this->assertFalse($result, 'Save occurred even when with models failed. %s');

		$joinRecords = $JoinThing->find('count', [
			'conditions' => ['JoinThing.something_id' => $data['Something']['id']]
		]);
		$this->assertEquals(0, $joinRecords, 'Records were saved on the join table. %s');
	}

/**
 * Test that if a behavior modifies the model's whitelist validation gets triggered
 * properly for those fields.
 *
 * @return void
 */
	public function testValidateWithFieldListAndBehavior() {
		$TestModel = new ValidationTest1();
		$TestModel->validate = [
			'title' => [
				'rule' => 'notBlank',
			],
			'name' => [
				'rule' => 'notBlank',
		]];
		$TestModel->Behaviors->attach('ValidationRule', ['fields' => ['name']]);

		$data = [
			'title' => '',
			'name' => '',
		];
		$result = $TestModel->save($data, ['fieldList' => ['title']]);
		$this->assertFalse($result);

		$expected = ['title' => ['This field cannot be left blank'], 'name' => ['This field cannot be left blank']];
		$this->assertEquals($expected, $TestModel->validationErrors);
	}

/**
 * test that saveAll and with models with validation interact well
 *
 * @return void
 */
	public function testValidatesWithModelsAndSaveAll() {
		$this->loadFixtures('Something', 'SomethingElse', 'JoinThing');
		$data = [
			'Something' => [
				'id' => 5,
				'title' => 'Extra Fields',
				'body' => 'Extra Fields Body',
				'published' => '1'
			],
			'SomethingElse' => [
				['something_else_id' => 1, 'doomed' => '']
			]
		];
		$Something = new Something();
		$JoinThing = $Something->JoinThing;

		$JoinThing->validate = ['doomed' => ['rule' => 'notBlank']];
		$expectedError = ['doomed' => ['This field cannot be left blank']];

		$Something->create();
		$result = $Something->saveAll($data, ['validate' => 'only']);
		$this->assertFalse($result);
		$result = $Something->validateAssociated($data);
		$this->assertFalse($result);
		$this->assertEquals($expectedError, $JoinThing->validationErrors);
		$result = $Something->validator()->validateAssociated($data);
		$this->assertFalse($result);

		$Something->create();
		$result = $Something->saveAll($data, ['validate' => 'first']);
		$this->assertFalse($result);
		$this->assertEquals($expectedError, $JoinThing->validationErrors);

		$count = $Something->find('count', ['conditions' => ['Something.id' => $data['Something']['id']]]);
		$this->assertSame(0, $count);

		$joinRecords = $JoinThing->find('count', [
			'conditions' => ['JoinThing.something_id' => $data['Something']['id']]
		]);
		$this->assertEquals(0, $joinRecords, 'Records were saved on the join table. %s');
	}

/**
 * test that saveAll and with models at initial insert (no id has set yet)
 * with validation interact well
 *
 * @return void
 */
	public function testValidatesWithModelsAndSaveAllWithoutId() {
		$this->loadFixtures('Post', 'Author');

		$data = [
			'Author' => [
				'name' => 'Foo Bar',
			],
			'Post' => [
				['title' => 'Hello'],
				['title' => 'World'],
			]
		];
		$Author = new Author();
		$Post = $Author->Post;

		$Post->validate = ['author_id' => ['rule' => 'numeric']];

		$Author->create();
		$result = $Author->saveAll($data, ['validate' => 'only']);
		$this->assertTrue($result);
		$result = $Author->validateAssociated($data);
		$this->assertTrue($result);
		$this->assertTrue($result);

		$Author->create();
		$result = $Author->saveAll($data, ['validate' => 'first']);
		$this->assertTrue($result);
		$this->assertNotNull($Author->id);

		$id = $Author->id;
		$count = $Author->find('count', ['conditions' => ['Author.id' => $id]]);
		$this->assertSame(1, $count);

		$count = $Post->find('count', [
			'conditions' => ['Post.author_id' => $id]
		]);
		$this->assertEquals($count, count($data['Post']));
	}

/**
 * Test that missing validation methods trigger errors in development mode.
 * Helps to make development easier.
 *
 * @expectedException PHPUnit_Framework_Error
 * @return void
 */
	public function testMissingValidationErrorTriggering() {
		Configure::write('debug', 2);

		$TestModel = new ValidationTest1();
		$TestModel->create(['title' => 'foo']);
		$TestModel->validate = [
			'title' => [
				'rule' => ['thisOneBringsThePain'],
				'required' => true
			]
		];
		$TestModel->invalidFields(['fieldList' => ['title']]);
	}

/**
 * Test placeholder replacement when validation message is an array
 *
 * @return void
 */
	public function testValidationMessageAsArray() {
		$TestModel = new ValidationTest1();
		$TestModel->validate = [
			'title' => [
				'minLength' => [
					'rule' => ['minLength', 6],
					'required' => true,
					'message' => 'Minimum length allowed is %d chars',
					'last' => false
				],
				'between' => [
					'rule' => ['lengthBetween', 5, 15],
					'message' => ['You may enter up to %s chars (minimum is %s chars)', 14, 6]
				]
			]
		];

		$TestModel->create();
		$expected = [
			'title' => [
				'Minimum length allowed is 6 chars',
			]
		];
		$TestModel->invalidFields();
		$this->assertEquals($expected, $TestModel->validationErrors);

		$TestModel->create(['title' => 'foo']);
		$expected = [
			'title' => [
				'Minimum length allowed is 6 chars',
				'You may enter up to 14 chars (minimum is 6 chars)'
			]
		];
		$TestModel->invalidFields();
		$this->assertEquals($expected, $TestModel->validationErrors);
	}

/**
 * Test validation message translation
 *
 * @return void
 */
	public function testValidationMessageTranslation() {
		$lang = Configure::read('Config.language');
		Configure::write('Config.language', 'en');
		App::build([
			'Locale' => [CAKE . 'Test' . DS . 'test_app' . DS . 'Locale' . DS],
		], App::RESET);

		$TestModel = new ValidationTest1();
		$TestModel->validationDomain = 'validation_messages';
		$TestModel->validate = [
			'title' => [
				[
					'rule' => ['customValidationMethod', 'arg1'],
					'required' => true,
					'message' => 'Validation failed: %s'
				]
			]
		];

		$TestModel->create();
		$expected = [
			'title' => [
				'Translated validation failed: Translated arg1',
			]
		];
		$TestModel->invalidFields();
		$this->assertEquals($expected, $TestModel->validationErrors);

		$TestModel->validationDomain = 'default';
		Configure::write('Config.language', $lang);
		App::build();
	}

/**
 * Test for 'on' => [create|update] in validation rules.
 *
 * @return void
 */
	public function testStateValidation() {
		$this->loadFixtures('Article');
		$Article = new Article();

		$data = [
			'Article' => [
				'title' => '',
				'body' => 'Extra Fields Body',
				'published' => '1'
			]
		];

		$Article->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'on' => 'create'
				]
			]
		];

		$Article->create($data);
		$this->assertFalse($Article->validates());

		$Article->save(null, ['validate' => false]);
		$data['Article']['id'] = $Article->id;
		$Article->set($data);
		$this->assertTrue($Article->validates());

		unset($data['Article']['id']);
		$Article->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'on' => 'update'
				]
			]
		];

		$Article->create($data);
		$this->assertTrue($Article->validates());

		$Article->save(null, ['validate' => false]);
		$data['Article']['id'] = $Article->id;
		$Article->set($data);
		$this->assertFalse($Article->validates());
	}

/**
 * Test for 'required' => [create|update] in validation rules.
 *
 * @return void
 */
	public function testStateRequiredValidation() {
		$this->loadFixtures('Article');
		$Article = new Article();

		// no title field present
		$data = [
			'Article' => [
				'body' => 'Extra Fields Body',
				'published' => '1'
			]
		];

		$Article->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'required' => 'create'
				]
			]
		];

		$Article->create($data);
		$this->assertFalse($Article->validates());

		$Article->save(null, ['validate' => false]);
		$data['Article']['id'] = $Article->id;
		$Article->set($data);
		$this->assertTrue($Article->validates());

		unset($data['Article']['id']);
		$Article->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'required' => 'update'
				]
			]
		];

		$Article->create($data);
		$this->assertTrue($Article->validates());

		$Article->save(null, ['validate' => false]);
		$data['Article']['id'] = $Article->id;
		$Article->set($data);
		$this->assertFalse($Article->validates());
	}

/**
 * Test that 'required' and 'on' are not conflicting
 *
 * @return void
 */
	public function testOnRequiredConflictValidation() {
		$this->loadFixtures('Article');
		$Article = new Article();

		// no title field present
		$data = [
			'Article' => [
				'body' => 'Extra Fields Body',
				'published' => '1'
			]
		];

		$Article->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'required' => 'create',
					'on' => 'create'
				]
			]
		];

		$Article->create($data);
		$this->assertFalse($Article->validates());

		$Article->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'required' => 'update',
					'on' => 'create'
				]
			]
		];

		$Article->create($data);
		$this->assertTrue($Article->validates());

		$Article->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'required' => 'create',
					'on' => 'update'
				]
			]
		];

		$Article->create($data);
		$this->assertTrue($Article->validates());

		$Article->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'required' => 'update',
					'on' => 'update'
				]
			]
		];

		$Article->create($data);
		$this->assertTrue($Article->validates());

		$Article->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'required' => 'create',
					'on' => 'create'
				]
			]
		];

		$Article->save(null, ['validate' => false]);
		$data['Article']['id'] = $Article->id;
		$Article->set($data);
		$this->assertTrue($Article->validates());

		$Article->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'required' => 'update',
					'on' => 'create'
				]
			]
		];

		$Article->set($data);
		$this->assertTrue($Article->validates());

		$Article->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'required' => 'create',
					'on' => 'update'
				]
			]
		];

		$Article->set($data);
		$this->assertTrue($Article->validates());

		$Article->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'required' => 'update',
					'on' => 'update'
				]
			]
		];

		$Article->set($data);
		$this->assertFalse($Article->validates());
	}

/**
 * testSaveAllDeepValidateOnly
 * tests the validate methods with deeper recursive data
 *
 * @return void
 */
	public function testSaveAllDeepValidateOnly() {
		$this->loadFixtures('Article', 'Comment', 'User', 'Attachment');
		$TestModel = new Article();
		$TestModel->hasMany['Comment']['order'] = ['Comment.created' => 'ASC'];
		$TestModel->hasAndBelongsToMany = [];
		$TestModel->Comment->Attachment->validate['attachment'] = 'notBlank';
		$TestModel->Comment->validate['comment'] = 'notBlank';

		$data = [
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => 'newuser', 'password' => 'newuserpass']],
				['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertTrue($result);
		$result = $TestModel->validateAssociated($data, ['deep' => true]);
		$this->assertTrue($result);

		$data = [
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => '', 'password' => 'newuserpass']],
				['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertFalse($result);
		$result = $TestModel->validateAssociated($data, ['deep' => true]);
		$this->assertFalse($result);

		$data = [
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => 'newuser', 'password' => 'newuserpass']],
				['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
			]
		];
		$expected = [
			'Article' => true,
			'Comment' => [
				true,
				true
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$this->assertSame($expected, $result);
		$result = $TestModel->validateAssociated($data, ['atomic' => false, 'deep' => true]);
		$this->assertSame($expected, $result);

		$data = [
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => '', 'password' => 'newuserpass']],
				['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
			]
		];
		$expected = [
			'Article' => true,
			'Comment' => [
				false,
				true
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$this->assertSame($expected, $result);
		$result = $TestModel->validateAssociated($data, ['atomic' => false, 'deep' => true]);
		$this->assertSame($expected, $result);

		$data = [
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 5],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => 'deepsaved']]
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertTrue($result);
		$result = $TestModel->validateAssociated($data, ['deep' => true]);
		$this->assertTrue($result);

		$data = [
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 5],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => '']]
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertFalse($result);
		$result = $TestModel->validateAssociated($data, ['deep' => true]);
		$this->assertFalse($result);

		$data = [
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 5],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => 'deepsave']]
			]
		];
		$expected = [
			'Article' => true,
			'Comment' => [
				true,
				true
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$this->assertSame($expected, $result);
		$result = $TestModel->validateAssociated($data, ['atomic' => false, 'deep' => true]);
		$this->assertSame($expected, $result);

		$data = [
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 5],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => '']]
			]
		];
		$expected = [
			'Article' => true,
			'Comment' => [
				true,
				false
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$this->assertSame($expected, $result);
		$result = $TestModel->validateAssociated($data, ['atomic' => false, 'deep' => true]);
		$this->assertSame($expected, $result);

		$expected = [
			'Comment' => [
				1 => [
					'Attachment' => [
						'attachment' => ['This field cannot be left blank']
					]
				]
			]
		];
		$result = $TestModel->validationErrors;
		$this->assertSame($expected, $result);

		$data = [
			'Attachment' => [
				'attachment' => 'deepsave insert',
			],
			'Comment' => [
				'comment' => 'First comment deepsave insert',
				'published' => 'Y',
				'user_id' => 5,
				'Article' => [
					'title' => 'First Article deepsave insert',
					'body' => 'First Article Body deepsave insert',
					'User' => [
						'user' => 'deepsave',
						'password' => 'magic'
					],
				],
			]
		];

		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertTrue($result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['deep' => true]);
		$this->assertTrue($result);

		$expected = [
			'Attachment' => true,
			'Comment' => true
		];
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$this->assertSame($expected, $result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['atomic' => false, 'deep' => true]);
		$this->assertSame($expected, $result);

		$data = [
			'Attachment' => [
				'attachment' => 'deepsave insert',
			],
			'Comment' => [
				'comment' => 'First comment deepsave insert',
				'published' => 'Y',
				'user_id' => 5,
				'Article' => [
					'title' => 'First Article deepsave insert',
					'body' => 'First Article Body deepsave insert',
					'User' => [
						'user' => '',
						'password' => 'magic'
					],
				],
			]
		];

		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertFalse($result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['deep' => true]);
		$this->assertFalse($result);

		$result = $TestModel->Comment->Attachment->validationErrors;
		$expected = [
			'Comment' => [
				'Article' => [
					'User' => [
						'user' => ['This field cannot be left blank']
					]
				]
			]
		];
		$this->assertSame($expected, $result);

		$expected = [
			'Attachment' => true,
			'Comment' => false
		];
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$this->assertEquals($expected, $result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['atomic' => false, 'deep' => true]);
		$this->assertEquals($expected, $result);

		$data['Comment']['Article']['body'] = '';
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertFalse($result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['deep' => true]);
		$this->assertFalse($result);

		$result = $TestModel->Comment->Attachment->validationErrors;
		$expected = [
			'Comment' => [
				'Article' => [
					'body' => ['This field cannot be left blank'],
					'User' => [
						'user' => ['This field cannot be left blank']
					]
				]
			]
		];
		$this->assertSame($expected, $result);

		$expected = [
			'Attachment' => true,
			'Comment' => false
		];
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$this->assertEquals($expected, $result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['atomic' => false, 'deep' => true]);
		$this->assertEquals($expected, $result);

		$data['Comment']['comment'] = '';
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertFalse($result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['deep' => true]);
		$this->assertFalse($result);

		$result = $TestModel->Comment->Attachment->validationErrors;
		$expected = [
			'Comment' => [
				'comment' => ['This field cannot be left blank'],
				'Article' => [
					'body' => ['This field cannot be left blank'],
					'User' => [
						'user' => ['This field cannot be left blank']
					]
				]
			]
		];
		$this->assertSame($expected, $result);

		$expected = [
			'Attachment' => true,
			'Comment' => false
		];
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$this->assertEquals($expected, $result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['atomic' => false, 'deep' => true]);
		$this->assertEquals($expected, $result);

		$data['Attachment']['attachment'] = '';
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertFalse($result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['deep' => true]);
		$this->assertFalse($result);

		$result = $TestModel->Comment->Attachment->validationErrors;
		$expected = [
			'attachment' => ['This field cannot be left blank'],
			'Comment' => [
				'comment' => ['This field cannot be left blank'],
				'Article' => [
					'body' => ['This field cannot be left blank'],
					'User' => [
						'user' => ['This field cannot be left blank']
					]
				]
			]
		];
		$this->assertSame($expected, $result);

		$result = $TestModel->Comment->validationErrors;
		$expected = [
			'comment' => ['This field cannot be left blank'],
			'Article' => [
					'body' => ['This field cannot be left blank'],
					'User' => [
						'user' => ['This field cannot be left blank']
					]
				]
		];
		$this->assertSame($expected, $result);

		$expected = [
			'Attachment' => false,
			'Comment' => false
		];
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$this->assertEquals($expected, $result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['atomic' => false, 'deep' => true]);
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAllNotDeepValidateOnly
 * tests the validate methods to not validate deeper recursive data
 *
 * @return void
 */
	public function testSaveAllNotDeepValidateOnly() {
		$this->loadFixtures('Article', 'Comment', 'User', 'Attachment');
		$TestModel = new Article();
		$TestModel->hasMany['Comment']['order'] = ['Comment.created' => 'ASC'];
		$TestModel->hasAndBelongsToMany = [];
		$TestModel->Comment->Attachment->validate['attachment'] = 'notBlank';
		$TestModel->Comment->validate['comment'] = 'notBlank';

		$data = [
			'Article' => ['id' => 2, 'body' => ''],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => '', 'password' => 'newuserpass']],
				['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only', 'deep' => false]);
		$this->assertFalse($result);
		$result = $TestModel->validateAssociated($data, ['deep' => false]);
		$this->assertFalse($result);

		$expected = ['body' => ['This field cannot be left blank']];
		$result = $TestModel->validationErrors;
		$this->assertSame($expected, $result);

		$data = [
			'Article' => ['id' => 2, 'body' => 'Ignore invalid user data'],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => '', 'password' => 'newuserpass']],
				['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only', 'deep' => false]);
		$this->assertTrue($result);
		$result = $TestModel->validateAssociated($data, ['deep' => false]);
		$this->assertTrue($result);

		$data = [
			'Article' => ['id' => 2, 'body' => 'Ignore invalid user data'],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => '', 'password' => 'newuserpass']],
				['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
			]
		];
		$expected = [
			'Article' => true,
			'Comment' => [
				true,
				true
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => false]);
		$this->assertSame($expected, $result);
		$result = $TestModel->validateAssociated($data, ['atomic' => false, 'deep' => false]);
		$this->assertSame($expected, $result);

		$data = [
			'Article' => ['id' => 2, 'body' => 'Ignore invalid attachment data'],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 5],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => '']]
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only', 'deep' => false]);
		$this->assertTrue($result);
		$result = $TestModel->validateAssociated($data, ['deep' => false]);
		$this->assertTrue($result);

		$data = [
			'Article' => ['id' => 2, 'body' => 'Ignore invalid attachment data'],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 5],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => '']]
			]
		];
		$expected = [
			'Article' => true,
			'Comment' => [
				true,
				true
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => false]);
		$this->assertSame($expected, $result);
		$result = $TestModel->validateAssociated($data, ['atomic' => false, 'deep' => false]);
		$this->assertSame($expected, $result);

		$expected = [];
		$result = $TestModel->validationErrors;
		$this->assertSame($expected, $result);

		$data = [
			'Attachment' => [
				'attachment' => 'deepsave insert',
			],
			'Comment' => [
				'comment' => 'First comment deepsave insert',
				'published' => 'Y',
				'user_id' => 5,
				'Article' => [
					'title' => 'First Article deepsave insert ignored',
					'body' => 'First Article Body deepsave insert',
					'User' => [
						'user' => '',
						'password' => 'magic'
					],
				],
			]
		];

		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => false]);
		$this->assertTrue($result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['deep' => false]);
		$this->assertTrue($result);

		$result = $TestModel->Comment->Attachment->validationErrors;
		$expected = [];
		$this->assertSame($expected, $result);

		$expected = [
			'Attachment' => true,
			'Comment' => true
		];
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => false]);
		$this->assertEquals($expected, $result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['atomic' => false, 'deep' => false]);
		$this->assertEquals($expected, $result);

		$data['Comment']['Article']['body'] = '';
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => false]);
		$this->assertTrue($result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['deep' => false]);
		$this->assertTrue($result);

		$result = $TestModel->Comment->Attachment->validationErrors;
		$expected = [];
		$this->assertSame($expected, $result);

		$expected = [
			'Attachment' => true,
			'Comment' => true
		];
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => false]);
		$this->assertEquals($expected, $result);
		$result = $TestModel->Comment->Attachment->validateAssociated($data, ['atomic' => false, 'deep' => false]);
		$this->assertEquals($expected, $result);
	}

/**
 * testValidateAssociated method
 *
 * @return void
 */
	public function testValidateAssociated() {
		$this->loadFixtures('Comment', 'Attachment', 'Article', 'User');
		$TestModel = new Comment();
		$TestModel->Attachment->validate = ['attachment' => 'notBlank'];

		$data = [
			'Comment' => [
				'comment' => 'This is the comment'
			],
			'Attachment' => [
				'attachment' => ''
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only']);
		$this->assertFalse($result);
		$result = $TestModel->validateAssociated($data);
		$this->assertFalse($result);

		$fieldList = [
			'Attachment' => ['comment_id']
		];
		$result = $TestModel->saveAll($data, [
			'fieldList' => $fieldList, 'validate' => 'only'
		]);
		$this->assertTrue($result);
		$this->assertEmpty($TestModel->validationErrors);
		$result = $TestModel->validateAssociated($data, ['fieldList' => $fieldList]);
		$this->assertTrue($result);
		$this->assertEmpty($TestModel->validationErrors);

		$TestModel->validate = ['comment' => 'notBlank'];
		$record = [
			'Comment' => [
				'user_id' => 1,
				'article_id' => 1,
				'comment' => '',
			],
			'Attachment' => [
				'attachment' => ''
			]
		];
		$result = $TestModel->saveAll($record, ['validate' => 'only']);
		$this->assertFalse($result);
		$result = $TestModel->validateAssociated($record);
		$this->assertFalse($result);

		$fieldList = [
			'Comment' => ['id', 'article_id', 'user_id'],
			'Attachment' => ['comment_id']
		];
		$result = $TestModel->saveAll($record, [
			'fieldList' => $fieldList, 'validate' => 'only'
		]);
		$this->assertTrue($result);
		$this->assertEmpty($TestModel->validationErrors);
		$result = $TestModel->validateAssociated($record, ['fieldList' => $fieldList]);
		$this->assertTrue($result);
		$this->assertEmpty($TestModel->validationErrors);

		$TestModel = new Article();
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = [];
		$TestModel->Comment->validate = ['comment' => 'notBlank'];
		$data = [
			'Article' => ['id' => 2],
			'Comment' => [
				[
					'id' => 1,
					'comment' => '',
					'published' => 'Y',
					'user_id' => 1,
				],
				[
					'id' => 2,
					'comment' =>
					'comment',
					'published' => 'Y',
					'user_id' => 1
				],
				[
					'id' => 3,
					'comment' => '',
					'published' => 'Y',
					'user_id' => 1
		]]];
		$result = $TestModel->saveAll($data, ['validate' => 'only']);
		$this->assertFalse($result);
		$result = $TestModel->validateAssociated($data);
		$this->assertFalse($result);

		$expected = [
			'Article' => true,
			'Comment' => [false, true, false]
		];
		$result = $TestModel->saveAll($data, ['atomic' => false, 'validate' => 'only']);
		$this->assertSame($expected, $result);
		$result = $TestModel->validateAssociated($data, ['atomic' => false]);
		$this->assertSame($expected, $result);

		$expected = ['Comment' => [
			0 => ['comment' => ['This field cannot be left blank']],
			2 => ['comment' => ['This field cannot be left blank']]
		]];
		$this->assertEquals($expected['Comment'], $TestModel->Comment->validationErrors);

		$model = new Comment();
		$model->deleteAll(true);
		$model->validate = ['comment' => 'notBlank'];
		$model->Attachment->validate = ['attachment' => 'notBlank'];
		$model->Attachment->bindModel(['belongsTo' => ['Comment']]);
		$expected = [
			'comment' => ['This field cannot be left blank'],
			'Attachment' => [
				'attachment' => ['This field cannot be left blank']
			]
		];

		$data = [
			'Comment' => ['comment' => '', 'article_id' => 1, 'user_id' => 1],
			'Attachment' => ['attachment' => '']
		];
		$result = $model->saveAll($data, ['validate' => 'only']);
		$this->assertFalse($result);
		$result = $model->validateAssociated($data);
		$this->assertFalse($result);
		$this->assertEquals($expected, $model->validationErrors);
		$this->assertEquals($expected['Attachment'], $model->Attachment->validationErrors);
	}

/**
 * testValidateMany method
 *
 * @return void
 */
	public function testValidateMany() {
		$TestModel = new Article();
		$TestModel->validate = ['title' => 'notBlank'];
		$data = [
			0 => ['title' => ''],
			1 => ['title' => 'title 1'],
			2 => ['title' => 'title 2'],
		];
		$expected = [
			0 => ['title' => ['This field cannot be left blank']],
		];

		$result = $TestModel->saveAll($data, ['validate' => 'only']);
		$this->assertFalse($result);
		$this->assertEquals($expected, $TestModel->validationErrors);
		$result = $TestModel->validateMany($data);
		$this->assertFalse($result);
		$this->assertEquals($expected, $TestModel->validationErrors);

		$data = [
			0 => ['title' => 'title 0'],
			1 => ['title' => ''],
			2 => ['title' => 'title 2'],
		];
		$expected = [
			1 => ['title' => ['This field cannot be left blank']],
		];
		$result = $TestModel->saveAll($data, ['validate' => 'only']);
		$this->assertFalse($result);
		$this->assertEquals($expected, $TestModel->validationErrors);
		$result = $TestModel->validateMany($data);
		$this->assertFalse($result);
		$this->assertEquals($expected, $TestModel->validationErrors);
	}

/**
 * testGetMethods method
 *
 * @return void
 */
	public function testGetMethods() {
		$this->loadFixtures('Article', 'Comment');
		$TestModel = new Article();
		$Validator = $TestModel->validator();

		$result = $Validator->getMethods();

		$expected = array_map('strtolower', get_class_methods('Article'));
		$this->assertEquals($expected, array_keys($result));
	}

/**
 *  Tests that methods are refreshed when the list of behaviors change
 *
 * @return void
 */
	public function testGetMethodsRefresh() {
		$this->loadFixtures('Article', 'Comment');
		$TestModel = new Article();
		$Validator = $TestModel->validator();

		$result = $Validator->getMethods();

		$expected = array_map('strtolower', get_class_methods('Article'));
		$this->assertEquals($expected, array_keys($result));

		$TestModel->Behaviors->load('Containable');
		$newList = [
			'contain',
			'resetbindings',
			'containments',
			'fielddependencies',
			'containmentsmap'
		];
		$this->assertEquals(array_merge($expected, $newList), array_keys($Validator->getMethods()));

		$TestModel->Behaviors->unload('Containable');
		$this->assertEquals($expected, array_keys($Validator->getMethods()));
	}

/**
 * testSetValidationDomain method
 *
 * @return void
 */
	public function testSetValidationDomain() {
		$this->loadFixtures('Article', 'Comment');
		$TestModel = new Article();
		$Validator = $TestModel->validator();

		$result = $Validator->setValidationDomain('default');
		$this->assertEquals('default', $TestModel->validationDomain);

		$result = $Validator->setValidationDomain('other');
		$this->assertEquals('other', $TestModel->validationDomain);
	}

/**
 * testGetModel method
 *
 * @return void
 */
	public function testGetModel() {
		$TestModel = new Article();
		$Validator = $TestModel->validator();

		$result = $Validator->getModel();
		$this->assertInstanceOf('Article', $result);
	}

/**
 * Tests it is possible to get validation sets for a field using an array inteface
 *
 * @return void
 */
	public function testArrayAccessGet() {
		$TestModel = new Article();
		$Validator = $TestModel->validator();

		$titleValidator = $Validator['title'];
		$this->assertEquals('title', $titleValidator->field);
		$this->assertCount(1, $titleValidator->getRules());
		$rule = current($titleValidator->getRules());
		$this->assertEquals('notBlank', $rule->rule);

		$titleValidator = $Validator['body'];
		$this->assertEquals('body', $titleValidator->field);
		$this->assertCount(1, $titleValidator->getRules());
		$rule = current($titleValidator->getRules());
		$this->assertEquals('notBlank', $rule->rule);

		$titleValidator = $Validator['user_id'];
		$this->assertEquals('user_id', $titleValidator->field);
		$this->assertCount(1, $titleValidator->getRules());
		$rule = current($titleValidator->getRules());
		$this->assertEquals('numeric', $rule->rule);
	}

/**
 * Tests it is possible to check for validation sets for a field using an array inteface
 *
 * @return void
 */
	public function testArrayAccessExists() {
		$TestModel = new Article();
		$Validator = $TestModel->validator();

		$this->assertTrue(isset($Validator['title']));
		$this->assertTrue(isset($Validator['body']));
		$this->assertTrue(isset($Validator['user_id']));
		$this->assertFalse(isset($Validator['other']));
	}

/**
 * Tests it is possible to set validation rules for a field using an array inteface
 *
 * @return void
 */
	public function testArrayAccessSet() {
		$TestModel = new Article();
		$Validator = $TestModel->validator();

		$set = [
			'numeric' => ['rule' => 'numeric', 'allowEmpty' => false],
			'between' => ['rule' => ['lengthBetween', 1, 5], 'allowEmpty' => false],
		];
		$Validator['other'] = $set;
		$rules = $Validator['other'];
		$this->assertEquals('other', $rules->field);

		$validators = $rules->getRules();
		$this->assertCount(2, $validators);
		$this->assertEquals('numeric', $validators['numeric']->rule);
		$this->assertEquals(['lengthBetween', 1, 5], $validators['between']->rule);

		$Validator['new'] = new CakeValidationSet('new', $set, []);
		$rules = $Validator['new'];
		$this->assertEquals('new', $rules->field);

		$validators = $rules->getRules();
		$this->assertCount(2, $validators);
		$this->assertEquals('numeric', $validators['numeric']->rule);
		$this->assertEquals(['lengthBetween', 1, 5], $validators['between']->rule);
	}

/**
 * Tests it is possible to unset validation rules
 *
 * @return void
 */
	public function testArrayAccessUset() {
		$TestModel = new Article();
		$Validator = $TestModel->validator();

		$this->assertTrue(isset($Validator['title']));
		unset($Validator['title']);
		$this->assertFalse(isset($Validator['title']));
	}

/**
 * Tests it is possible to iterate a validation object
 *
 * @return void
 */
	public function testIterator() {
		$TestModel = new Article();
		$Validator = $TestModel->validator();

		$i = 0;
		foreach ($Validator as $field => $rules) {
			if ($i === 0) {
				$this->assertEquals('user_id', $field);
			}
			if ($i === 1) {
				$this->assertEquals('title', $field);
			}
			if ($i === 2) {
				$this->assertEquals('body', $field);
			}
			$this->assertInstanceOf('CakeValidationSet', $rules);
			$i++;
		}
		$this->assertEquals(3, $i);
	}

/**
 * Tests countable interface in ModelValidator
 *
 * @return void
 */
	public function testCount() {
		$TestModel = new Article();
		$Validator = $TestModel->validator();
		$this->assertCount(3, $Validator);

		$set = [
			'numeric' => ['rule' => 'numeric', 'allowEmpty' => false],
			'range' => ['rule' => ['lengthBetween', 1, 5], 'allowEmpty' => false],
		];
		$Validator['other'] = $set;
		$this->assertCount(4, $Validator);

		unset($Validator['title']);
		$this->assertCount(3, $Validator);
		unset($Validator['body']);
		$this->assertCount(2, $Validator);
	}

/**
 * Tests it is possible to add validation rules
 *
 * @return void
 */
	public function testAddRule() {
		$TestModel = new Article();
		$Validator = $TestModel->validator();

		$Validator->add('other', 'numeric', ['rule' => 'numeric', 'allowEmpty' => false]);
		$Validator->add('other', 'between', ['rule' => ['lengthBetween', 1, 5], 'allowEmpty' => false]);
		$rules = $Validator['other'];
		$this->assertEquals('other', $rules->field);

		$validators = $rules->getRules();
		$this->assertCount(2, $validators);
		$this->assertEquals('numeric', $validators['numeric']->rule);
		$this->assertEquals(['lengthBetween', 1, 5], $validators['between']->rule);
	}

/**
 * Tests it is possible to remove validation rules
 *
 * @return void
 */
	public function testRemoveRule() {
		$TestModel = new Article();
		$Validator = $TestModel->validator();

		$this->assertTrue(isset($Validator['title']));
		$Validator->remove('title');
		$this->assertFalse(isset($Validator['title']));

		$Validator->add('other', 'numeric', ['rule' => 'numeric', 'allowEmpty' => false]);
		$Validator->add('other', 'between', ['rule' => ['lengthBetween', 1, 5], 'allowEmpty' => false]);
		$this->assertTrue(isset($Validator['other']));

		$Validator->remove('other', 'numeric');
		$this->assertTrue(isset($Validator['other']));
		$this->assertFalse(isset($Validator['other']['numeric']));
		$this->assertTrue(isset($Validator['other']['between']));
	}

/**
 * Tests validation callbacks are triggered
 *
 * @return void
 */
	public function testValidateCallbacks() {
		$TestModel = $this->getMock('Article', ['beforeValidate', 'afterValidate']);
		$TestModel->expects($this->once())->method('beforeValidate');
		$TestModel->expects($this->once())->method('afterValidate');

		$TestModel->set(['title' => '', 'body' => 'body']);
		$TestModel->validates();
	}

/**
 * Tests that altering data in a beforeValidate callback will lead to saving those
 * values in database
 *
 * @return void
 */
	public function testValidateFirstWithBeforeValidate() {
		$this->loadFixtures('Article', 'User');
		$model = new CustomArticle();
		$model->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'required' => true,
					'allowEmpty' => false
				]
			]
		];
		$data = [
			'CustomArticle' => [
				'body' => 'foo0'
			]
		];
		$result = $model->saveAll($data, ['validate' => 'first']);
		$this->assertTrue($result);

		$this->assertFalse($model->findMethods['unPublished'], 'beforeValidate was run twice');

		$model->findMethods['unPublished'] = true;
		$data = [
			'CustomArticle' => [
				'body' => 'foo1'
			]
		];
		$result = $model->saveAll($data, ['validate' => 'first', 'deep' => true]);
		$this->assertTrue($result);
		$title = $model->field('title', ['body' => 'foo1']);
		$this->assertEquals('foo', $title);
		$this->assertFalse($model->findMethods['unPublished'], 'beforeValidate was run twice');

		$data = [
			['body' => 'foo2'],
			['body' => 'foo3'],
			['body' => 'foo4']
		];

		$result = $model->saveAll($data, ['validate' => 'first', 'deep' => true]);
		$this->assertTrue($result);

		$this->assertEquals('foo', $model->field('title', ['body' => 'foo2']));
		$this->assertEquals('foo', $model->field('title', ['body' => 'foo3']));
		$this->assertEquals('foo', $model->field('title', ['body' => 'foo4']));
	}

/**
 * Tests that altering data in a beforeValidate callback will lead to saving those
 * values in database
 *
 * @return void
 */
	public function testValidateFirstAssociatedWithBeforeValidate() {
		$this->loadFixtures('Article', 'User');
		$model = new CustomArticle();
		$model->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'required' => true
				]
			]
		];
		$articles = [
			['body' => 'foo1'],
			['body' => 'foo2'],
			['body' => 'foo3']
		];
		$user = new User();
		$user->bindModel(['hasMany' => ['CustomArticle']]);
		$data = [
			'User' => ['user' => 'foo', 'password' => 'bar'],
			'CustomArticle' => $articles
		];
		$result = $user->saveAll($data, ['validate' => 'first']);
		$this->assertTrue($result);

		$this->assertEquals('foo', $model->field('title', ['body' => 'foo1']));
		$this->assertEquals('foo', $model->field('title', ['body' => 'foo2']));
		$this->assertEquals('foo', $model->field('title', ['body' => 'foo3']));
	}

/**
 * testValidateFirstWithDefaults method
 *
 * @return void
 */
	public function testFirstWithDefaults() {
		$this->loadFixtures('Article', 'Tag', 'Comment', 'User', 'ArticlesTag');
		$TestModel = new Article();

		$result = $TestModel->find('first', [
			'conditions' => ['Article.id' => 1]
		]);
		$expected = [
			'Article' => [
				'id' => 1,
				'user_id' => 1,
				'title' => 'First Article',
				'body' => 'First Article Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:39:23'
			],
		];
		unset($result['Article']['updated']);
		$this->assertEquals($expected['Article'], $result['Article']);

		$data = [
			'Article' => [
				'id' => 1,
				'title' => 'First Article (modified)'
			],
			'Comment' => [
				['comment' => 'Article comment', 'user_id' => 1]
			]
		];
		$result = $TestModel->saveAll($data, ['validate' => 'first']);
		$this->assertTrue($result);

		$result = $TestModel->find('first', [
			'conditions' => ['Article.id' => 1]
		]);
		$expected['Article']['title'] = 'First Article (modified)';
		unset($result['Article']['updated']);
		$this->assertEquals($expected['Article'], $result['Article']);
	}

	public function testAddMultipleRules() {
		$TestModel = new Article();
		$Validator = $TestModel->validator();

		$set = [
			'numeric' => ['rule' => 'numeric', 'allowEmpty' => false],
			'between' => ['rule' => ['lengthBetween', 1, 5], 'allowEmpty' => false],
		];

		$Validator->add('other', $set);
		$rules = $Validator['other'];
		$this->assertEquals('other', $rules->field);

		$validators = $rules->getRules();
		$this->assertCount(2, $validators);
		$this->assertEquals('numeric', $validators['numeric']->rule);
		$this->assertEquals(['lengthBetween', 1, 5], $validators['between']->rule);

		$set = new CakeValidationSet('other', [
			'a' => ['rule' => 'numeric', 'allowEmpty' => false],
			'b' => ['rule' => ['lengthBetween', 1, 5], 'allowEmpty' => false],
		]);

		$Validator->add('other', $set);
		$this->assertSame($set, $Validator->getField('other'));
	}

/**
 * Test that rules are parsed correctly when calling getField()
 *
 * @return void
 */
	public function testValidator() {
		$TestModel = new Article();
		$Validator = $TestModel->validator();

		$result = $Validator->getField();
		$expected = ['user_id', 'title', 'body'];
		$this->assertEquals($expected, array_keys($result));
		$this->assertTrue($result['user_id'] instanceof CakeValidationSet);

		$result = $TestModel->validator()->getField('title');
		$this->assertTrue($result instanceof CakeValidationSet);
	}

/**
 * Test that validator override works as expected
 *
 * @return void
 */
	public function testValidatorOverride() {
		$TestModel = new Article();
		$ValidatorA = new ModelValidator($TestModel);
		$ValidatorB = new ModelValidator($TestModel);

		$TestModel->validator($ValidatorA);
		$TestModel->validator($ValidatorB);

		$this->assertSame($ValidatorB, $TestModel->validator());
		$this->assertNotSame($ValidatorA, $TestModel->validator());
	}

/**
 * Test that type hint exception is thrown
 *
 * @expectedException PHPUnit_Framework_Error
 * @throws PHPUnit_Framework_Error
 * @return void
 */
	public function testValidatorTypehintException() {
		try {
			new ModelValidator('asdasds');
		} catch (Throwable $t) {
			throw new PHPUnit_Framework_Error($t);
		}
	}

/**
 * Tests that altering data in a beforeValidate callback will lead to saving those
 * values in database, this time with belongsTo associations
 *
 * @return void
 */
	public function testValidateFirstAssociatedWithBeforeValidate2() {
		$this->loadFixtures('Article', 'User');
		$model = new CustomArticle();
		$model->validate = [
			'title' => [
				'notBlank' => [
					'rule' => 'notBlank',
					'required' => true
				]
			]
		];

		$data = [
			'User' => ['user' => 'foo', 'password' => 'bar'],
			'CustomArticle' => [
				'body' => 'a test'
			]
		];
		$result = $model->saveAll($data, ['validate' => 'first']);
		$this->assertTrue($result);

		$this->assertEquals('foo', $model->field('title', ['body' => 'a test']));
	}

/**
 * Testing you can dynamically add rules to a field, added this to dispel doubts
 * after a presentation made to show off this new feature
 *
 * @return void
 */
	public function testDynamicValidationRuleBuilding() {
		$model = new Article;
		$validator = $model->validator();
		$validator->add('body', 'isSpecial', ['rule' => 'special']);
		$rules = $validator['body']->getRules();
		$this->assertCount(2, $rules);
		$this->assertEquals('special', $rules['isSpecial']->rule);
		$validator['body']->setRule('isAwesome', ['rule' => 'awesome']);
		$rules = $validator['body']->getRules();
		$this->assertCount(3, $rules);
		$this->assertEquals('awesome', $rules['isAwesome']->rule);
	}

/**
 * Test to ensure custom validation methods work with CakeValidationSet
 *
 * @return void
 */
	public function testCustomMethodsWithCakeValidationSet() {
		$TestModel = new TestValidate();
		$Validator = $TestModel->validator();

		$Validator->add('title', 'validateTitle', [
			'rule' => 'validateTitle',
			'message' => 'That aint right',
		]);
		$data = ['title' => 'notatitle'];
		$result = $Validator->getField('title')->validate($data);
		$expected = [0 => 'That aint right'];
		$this->assertEquals($expected, $result);

		$data = ['title' => 'title-is-good'];
		$result = $Validator->getField('title')->validate($data);
		$expected = [];
		$this->assertEquals($expected, $result);
	}

	public function testCustomMethodWithEmptyValue() {
		$this->loadFixtures('Article');

		$model = $this->getMock('Article', ['isLegit']);
		$model->validate = [
			'title' => [
				'custom' => [
					'rule' => ['isLegit'],
					'message' => 'is no good'
				]
			]
		];
		$model->expects($this->once())
			->method('isLegit')
			->will($this->returnValue(false));

		$model->set(['title' => '']);
		$this->assertFalse($model->validates());
	}

/**
 * Test validateAssociated with atomic=false & deep=true
 *
 * @return void
 */
	public function testValidateAssociatedAtomicFalseDeepTrueWithErrors() {
		$this->loadFixtures('Comment', 'Article', 'User', 'Attachment');
		$Attachment = ClassRegistry::init('Attachment');
		$Attachment->Comment->validator()->add('comment', [
			['rule' => 'notBlank']
		]);
		$Attachment->Comment->User->bindModel([
			'hasMany' => [
				'Article',
				'Comment'
			]],
			false
		);

		$data = [
			'Attachment' => [
				'attachment' => 'text',
				'Comment' => [
					'comment' => '',
					'published' => 'N',
					'User' => [
						'user' => 'Foo',
						'password' => 'mypassword',
						'Comment' => [
							[
								'comment' => ''
							]
						]
					]
				]
			]
		];
		$result = $Attachment->validateAssociated($data, ['atomic' => false, 'deep' => true]);

		$result = $Attachment->validationErrors;
		$expected = [
			'Comment' => [
				'comment' => [
					0 => 'This field cannot be left blank',
				],
				'User' => [
					'Comment' => [
						0 => [
							'comment' => [
								0 => 'This field cannot be left blank',
							],
						],
					],
				],
			],
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Test validateMany with atomic=false & deep=true
 *
 * @return void
 */
	public function testValidateManyAtomicFalseDeepTrueWithErrors() {
		$this->loadFixtures('Comment', 'Article', 'User');
		$Article = ClassRegistry::init('Article');
		$Article->Comment->validator()->add('comment', [
			['rule' => 'notBlank']
		]);

		$data = [
			[
				'Article' => [
					'user_id' => 1,
					'title' => 'Foo',
					'body' => 'text',
					'published' => 'N'
				],
				'Comment' => [
					[
						'user_id' => 1,
						'comment' => 'Baz',
						'published' => 'N',
					]
				],
			],
			[
				'Article' => [
					'user_id' => 1,
					'title' => 'Bar',
					'body' => 'text',
					'published' => 'N'
				],
				'Comment' => [
					[
						'user_id' => 1,
						'comment' => '',
						'published' => 'N',
					]
				],
			],
		];
		$Article->validateMany($data, ['atomic' => false, 'deep' => true]);

		$result = $Article->validationErrors;
		$expected = [
			1 => [
				'Comment' => [
					0 => [
						'comment' => [
							0 => 'This field cannot be left blank',
						],
					],
				],
			],
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Test the isUnique method when used as a validator for multiple fields.
 *
 * @return void
 */
	public function testIsUniqueValidator() {
		$this->loadFixtures('Article');
		$Article = ClassRegistry::init('Article');
		$Article->validate = [
			'user_id' => [
				'duplicate' => [
					'rule' => ['isUnique', ['user_id', 'title'], false]
				]
			]
		];
		$data = [
			'user_id' => 1,
			'title' => 'First Article',
		];
		$Article->create($data);
		$this->assertFalse($Article->validates(), 'Contains a dupe');

		$data = [
			'user_id' => 1,
			'title' => 'Unique Article',
		];
		$Article->create($data);
		$this->assertTrue($Article->validates(), 'Should pass');

		$Article->validate = [
			'user_id' => [
				'duplicate' => [
					'rule' => ['isUnique', ['user_id', 'title']]
				]
			]
		];
		$data = [
			'user_id' => 1,
			'title' => 'Unique Article',
		];
		$Article->create($data);
		$this->assertFalse($Article->validates(), 'Should fail, conditions are combined with or');
	}

/**
 * Test backward compatibility of the isUnique method when used as a validator for a single field.
 *
 * @return void
 */
	public function testBackwardCompatIsUniqueValidator() {
		$this->loadFixtures('Article');
		$Article = ClassRegistry::init('Article');
		$Article->validate = [
			'title' => [
				'duplicate' => [
					'rule' => 'isUnique',
					'message' => 'Title must be unique',
				],
				'minLength' => [
					'rule' => ['minLength', 1],
					'message' => 'Title cannot be empty',
				],
			]
		];
		$data = [
			'title' => 'First Article',
		];
		$data = $Article->create($data);
		$this->assertFalse($Article->validates(), 'Contains a dupe');
	}

}

/**
 * Behavior for testing validation rules.
 */
class ValidationRuleBehavior extends ModelBehavior {

	public function setup(Model $Model, $config = []) {
		$this->settings[$Model->alias] = $config;
	}

	public function beforeValidate(Model $Model, $options = []) {
		$fields = $this->settings[$Model->alias]['fields'];
		foreach ($fields as $field) {
			$Model->whitelist[] = $field;
		}
	}

}
