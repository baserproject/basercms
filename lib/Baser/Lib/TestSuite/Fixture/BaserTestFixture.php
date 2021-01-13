<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Lib.TestSuite.Fixture
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class BaserTestFixture
 *
 * Baser Test Fixture
 *
 * @package Baser.Lib.TestSuite.Fixture
 */
class BaserTestFixture extends CakeTestFixture
{

	/**
	 * {@inheritDoc}
	 */
	public function init()
	{
		if (empty($this->fields)) {
			$this->fields = $this->getSchema($this->name);
		}
		parent::init();
	}

	/**
	 * lib/Baser/Config/Schema以下のスキーマクラスからスキーマを取得
	 *
	 * @param string $name Model名
	 * @return array
	 * @throws RuntimeException
	 */
	public function getSchema($name)
	{
		$tableName = Inflector::tableize($name);
		$plugins = [null];
		$plugins = array_merge($plugins, Configure::read('BcApp.corePlugins'));

		$schemaFile = null;

		foreach($plugins as $plugin) {
			$schemaFile = $this->findSchemaFile($tableName, $plugin);
			if ($schemaFile != null) {
				break;
			}
		}

		if ($schemaFile == null) {
			throw new RuntimeException(__d('baser', 'Schemaファイルが見つかりません'));
		}

		require_once $schemaFile;

		$schemaClass = Inflector::camelize($tableName) . 'Schema';
		$schema = new $schemaClass();
		return $schema->tables[$tableName];
	}

	/**
	 * スキーマファイルを探す
	 *
	 * @param string $tableName テーブル名
	 * @param string $plugin プラグイン名
	 * @return null|string
	 */
	public function findSchemaFile($tableName, $plugin = null)
	{
		$configDir = [];
		if (empty($plugin)) {
			$schemaFile = BASER_CONFIGS . 'Schema' . DS . $tableName . '.php';
			if (file_exists($schemaFile)) {
				return $schemaFile;
			}
		} else {
			foreach(App::path('Plugin') as $pluginPath) {
				$schemaFile = $pluginPath . Inflector::camelize($plugin) . DS . 'Config' . DS . 'Schema' . DS . $tableName . '.php';
				if (file_exists($schemaFile)) {
					return $schemaFile;
				}
			}
		}
		return null;
	}

}
