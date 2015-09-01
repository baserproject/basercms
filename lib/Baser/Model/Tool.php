<?php

/**
 * ツールモデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
App::uses('CakeSchema', 'Model');

/**
 * ツールモデル
 * @package Baser.Model
 */
class Tool extends AppModel {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Tool';

/**
 * テーブル
 * 
 * @var string
 * @access public
 */
	public $useTable = false;

/**
 * コントロールソースを取得する
 *
 * @param string フィールド名
 * @return array コントロールソース
 * @access public
 */
	public function getControlSource($field) {
		// スキーマ用モデルリスト
		$controlSources['connection'] = array('baser' => 'baser（コア）', 'plugin' => 'plugin（プラグイン）');
		$controlSources = $this->getListModels($field);
		if (isset($controlSources)) {
			return $controlSources;
		} else {
			return false;
		}
	}

/**
 * データソースを指定してモデルリストを取得する
 * 
 * @param string $configKeyName データソース名
 * @return array
 * @access public
 */
	public function getListModels($configKeyName = 'baser') {
		$db = ConnectionManager::getDataSource($configKeyName);
		$listSources = $db->listSources();
		if (!$listSources) {
			return array();
		}
		$sources = array();
		foreach ($listSources as $source) {
			if (preg_match("/^" . $db->config['prefix'] . "([^_].+)$/", $source, $matches) &&
				!preg_match("/^" . Configure::read('BcEnv.pluginDbPrefix') . "[^_].+$/", $matches[1])) {
				$sources[] = $source;
			}
		}
		return $sources;
	}

/**
 * スキーマを書き出す
 * 
 * @param array $data
 * @param string $path スキーマファイルの生成場所
 * @return boolean
 * @access public
 */
	public function writeSchema($data, $path) {
		if (isset($data['Tool'])) {
			$data = $data['Tool'];
		}
		if (!$data['baser'] && !$data['plugin']) {
			return false;
		}

		$result = true;

		if ($data['baser']) {
			if (!$this->_writeSchema('baser', $data['baser'], $path)) {
				$result = false;
			}
		}
		if ($data['plugin']) {
			if (!$this->_writeSchema('plugin', $data['plugin'], $path)) {
				$result = false;
			}
		}
		return $result;
	}

/**
 * スキーマファイルを読み込む
 * 
 * @param array $data
 * @param string $tmpPath 
 * @return boolean
 * @access public
 */
	public function loadSchemaFile($data, $tmpPath) {
		$path = $tmpPath . $data['Tool']['schema_file']['name'];
		if (move_uploaded_file($data['Tool']['schema_file']['tmp_name'], $path)) {
			include $path;
			$schemaName = basename(Inflector::classify(basename($path)), '.php') . 'Schema';
			$Schema = new $schemaName();
			$db = ConnectionManager::getDataSource($Schema->connection);
			if ($db->loadSchema(array('type' => $data['Tool']['schema_type'], 'path' => $tmpPath, 'file' => $data['Tool']['schema_file']['name']))) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

/**
 * スキーマを書き出す
 *
 * @param string $field
 * @param array $values
 * @param string $path スキーマファイルの生成場所
 * @return boolean
 * @access protected
 */
	protected function _writeSchema($field, $values, $path) {
		$db = ConnectionManager::getDataSource($field);
		$prefix = $db->config['prefix'];
		$tableList = $this->getControlSource($field);
		$modelList = array();
		foreach ($tableList as $key => $table) {
			if (preg_match('/^' . $prefix . '([a-z][a-z_]*?)$/is', $table, $maches)) {
				$model = Inflector::camelize(Inflector::singularize($maches[1]));
				$modelList[$key] = $model;
			}
		}

		$result = true;
		foreach ($values as $value) {
			if (!$db->writeSchema(array('model' => $modelList[$value], 'path' => $path))) {
				$result = false;
			} else {
				// pathプロパティを削除
				$filename = $path . Inflector::tableize($modelList[$value]) . '.php';
				$File = new File($filename);
				$content = file_get_contents($filename);
				$reg = '/(\r\n\r\n|\n\n)[^\n]+?\$path[^\n]+?;/is';
				$content = preg_replace($reg, '', $content);
				$File->write($content, 'w+');
				$File->close();
			}
		}
		return $result;
	}

}
