<?php
/**
 * BaserTestCase
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Lib.TestSuite.Fixture
 * @since			baserCMS v 3.1.0-dev
 * @license			http://basercms.net/license/index.html
 */

class BaserTestFixture extends CakeTestFixture {

/**
 * {@inheritDoc}
 */
	public function init() {
		if (empty($this->fields)) {
			$this->fields = $this->getSchema($this->name);
		}
		parent::init();
	}

/**
 * lib/Baser/Config/Schema以下のスキーマクラスからスキーマを取得
 *
 * @param string $name Model名
 * @throws RuntimeException
 * @return array
 */
	public function getSchema($name) {
		$tableName = Inflector::tableize($name);
		$schemaFile = BASER_CONFIGS . 'Schema' . DS . $tableName . '.php';

		if (!file_exists($schemaFile)) {
			throw new RuntimeException('Schemaファイルが見つかりません');
		}
		require_once $schemaFile;

		$schemaClass = Inflector::camelize($tableName) . 'Schema';
		$schema = new $schemaClass();
		return $schema->tables[$tableName];
	}

}