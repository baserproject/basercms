<?php

/**
 * テーマモデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */

app::uses('BcConfigString', 'Lib');
/**
 * テーマモデル
 *
 * @package Baser.Model
 */
class Theme extends AppModel {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'Theme';

/**
 * テーブル
 *
 * @var string
 */
	public $useTable = false;

/**
 * バリデーション
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			array('rule' => array('notEmpty'),
				'message' => 'テーマ名を入力してください。'),
			array('rule' => 'alphaNumericPlus',
				'message' => 'テーマ名は半角英数字、ハイフン、アンダーバーのみで入力してください。'),
			array('rule' => 'themeDuplicate',
				'message' => '既に存在するテーマ名です。')
		),
		'url' => array(
			array('rule' => 'halfText',
				'message' => 'URLは半角英数字のみで入力してください。'),
			array('rule' => 'url',
				'message' => 'URLの形式が間違っています。'),
		)
	);

/**
 * テーマ名の重複チェック
 *
 * @param string $check チェックする文字列
 * @return bool
 */
	public function themeDuplicate($check) {
		$value = $check[key($check)];
		if (!$value) {
			return true;
		}
		if ($value == $this->data['Theme']['old_name']) {
			return true;
		}
		if (!is_dir(WWW_ROOT . 'theme' . DS . $value)) {
			return true;
		} else {
			return false;
		}
	}

/**
 * 保存
 *
 * @param array $data テーマのデータ
 * @param bool $validate バリデーションを行うかどうか
 * @param array $fieldList
 * @return bool
 */
	public function save($data = null, $validate = true, $fieldList = array()) {
		if (!$data) {
			$data = $this->data;
		} else {
			$this->set($data);
		}

		if ($validate) {
			if (!$this->validates()) {
				return false;
			}
		}

		if (isset($data['Theme'])) {
			$data = $data['Theme'];
		}

		if ($data['old_name'] !== $data['name'] &&
			!rename($this->getDirpath($data['old_name']), $this->getDirPath($data['name']))) {
				return false;
		}

		$configData = array(
			'title' => $data['title'],
			'description' => $data['description'],
			'author' => $data['author'],
			'url' => $data['url']
		);

		$file = $this->getConfigFile($data['name']);
		$configString = new BcConfigString($file->read());
		$configString->upsertMany($configData);
		$file->write($configString->content, 'w');
		$file->close();

		return true;
	}

	public function getDirPath($name) {
		return WWW_ROOT . 'theme' . DS . $name;
	}

	public function getConfigFilePath($name) {
		return $this->getDirPath($name) . DS . 'config.php';
	}

	public function getConfigFile($name) {
		return new File($this->getConfigFilePath($name));
	}

}
