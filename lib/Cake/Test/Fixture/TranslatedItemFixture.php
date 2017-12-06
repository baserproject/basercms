<?php
/**
 * Short description for file.
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
 * @package       Cake.Test.Fixture
 * @since         CakePHP(tm) v 1.2.0.5669
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Class TranslatedItemFixture
 *
 * @package       Cake.Test.Fixture
 */
class TranslatedItemFixture extends CakeTestFixture {

/**
 * fields property
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'key' => 'primary'],
		'translated_article_id' => ['type' => 'integer'],
		'slug' => ['type' => 'string', 'null' => false]
	];

/**
 * records property
 *
 * @var array
 */
	public $records = [
		['translated_article_id' => 1, 'slug' => 'first_translated'],
		['translated_article_id' => 1, 'slug' => 'second_translated'],
		['translated_article_id' => 1, 'slug' => 'third_translated']
	];
}
