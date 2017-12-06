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
 * Class TranslateFixture
 *
 * @package       Cake.Test.Fixture
 */
class TranslateFixture extends CakeTestFixture {

/**
 * table property
 *
 * @var string
 */
	public $table = 'i18n';

/**
 * fields property
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'key' => 'primary'],
		'locale' => ['type' => 'string', 'length' => 6, 'null' => false],
		'model' => ['type' => 'string', 'null' => false],
		'foreign_key' => ['type' => 'integer', 'null' => false],
		'field' => ['type' => 'string', 'null' => false],
		'content' => ['type' => 'text']
	];

/**
 * records property
 *
 * @var array
 */
	public $records = [
		['locale' => 'eng', 'model' => 'TranslatedItem', 'foreign_key' => 1, 'field' => 'title', 'content' => 'Title #1'],
		['locale' => 'eng', 'model' => 'TranslatedItem', 'foreign_key' => 1, 'field' => 'content', 'content' => 'Content #1'],
		['locale' => 'deu', 'model' => 'TranslatedItem', 'foreign_key' => 1, 'field' => 'title', 'content' => 'Titel #1'],
		['locale' => 'deu', 'model' => 'TranslatedItem', 'foreign_key' => 1, 'field' => 'content', 'content' => 'Inhalt #1'],
		['locale' => 'cze', 'model' => 'TranslatedItem', 'foreign_key' => 1, 'field' => 'title', 'content' => 'Titulek #1'],
		['locale' => 'cze', 'model' => 'TranslatedItem', 'foreign_key' => 1, 'field' => 'content', 'content' => 'Obsah #1'],
		['locale' => 'eng', 'model' => 'TranslatedItem', 'foreign_key' => 2, 'field' => 'title', 'content' => 'Title #2'],
		['locale' => 'eng', 'model' => 'TranslatedItem', 'foreign_key' => 2, 'field' => 'content', 'content' => 'Content #2'],
		['locale' => 'deu', 'model' => 'TranslatedItem', 'foreign_key' => 2, 'field' => 'title', 'content' => 'Titel #2'],
		['locale' => 'deu', 'model' => 'TranslatedItem', 'foreign_key' => 2, 'field' => 'content', 'content' => 'Inhalt #2'],
		['locale' => 'cze', 'model' => 'TranslatedItem', 'foreign_key' => 2, 'field' => 'title', 'content' => 'Titulek #2'],
		['locale' => 'cze', 'model' => 'TranslatedItem', 'foreign_key' => 2, 'field' => 'content', 'content' => 'Obsah #2'],
		['locale' => 'eng', 'model' => 'TranslatedItem', 'foreign_key' => 3, 'field' => 'title', 'content' => 'Title #3'],
		['locale' => 'eng', 'model' => 'TranslatedItem', 'foreign_key' => 3, 'field' => 'content', 'content' => 'Content #3'],
		['locale' => 'deu', 'model' => 'TranslatedItem', 'foreign_key' => 3, 'field' => 'title', 'content' => 'Titel #3'],
		['locale' => 'deu', 'model' => 'TranslatedItem', 'foreign_key' => 3, 'field' => 'content', 'content' => 'Inhalt #3'],
		['locale' => 'cze', 'model' => 'TranslatedItem', 'foreign_key' => 3, 'field' => 'title', 'content' => 'Titulek #3'],
		['locale' => 'cze', 'model' => 'TranslatedItem', 'foreign_key' => 3, 'field' => 'content', 'content' => 'Obsah #3']
	];
}
