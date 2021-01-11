<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcThemeConfigReader', 'Configure');

/**
 * Class Theme
 *
 * テーマモデル
 *
 * @package Baser.Model
 */
class Theme extends AppModel
{

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
	 * Theme constructor.
	 *
	 * @param bool $id
	 * @param null $table
	 * @param null $ds
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		$this->validate = [
			'name' => [
				['rule' => ['notBlank'], 'message' => __d('baser', 'テーマ名を入力してください。')],
				['rule' => 'alphaNumericPlus', 'message' => __d('baser', 'テーマ名は半角英数字、ハイフン、アンダーバーのみで入力してください。')],
				['rule' => 'themeDuplicate', 'message' => __d('baser', '既に存在するテーマ名です。')]],
			'url' => [
				['rule' => 'halfText', 'message' => __d('baser', 'URLは半角英数字のみで入力してください。')],
				['rule' => 'url', 'message' => __d('baser', 'URLの形式が間違っています。')]]
		];
	}

	/**
	 * テーマ名の重複チェック
	 *
	 * @param string $check チェックするテーマ名
	 * @return bool
	 */
	public function themeDuplicate($check)
	{
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
	 * @param array $data 保存するデータの配列
	 * @param bool|array $validate 真偽値または配列
	 *   真偽値の場合はバリデーションするかを示す
	 *   配列の場合は下記のキーを含むことが可能:
	 *
	 *   - validate: trueまたはfalseに設定してバリデーションを有効化・無効化する
	 *   - fieldList: 保存を許すフィールドの配列
	 *   - callbacks: falseに設定するとコールバックを無効にする. 'before' または 'after'
	 *      に設定するとそれぞれのコールバックだけが有効になる
	 *   - `counterCache`: Boolean to control updating of counter caches (if any)
	 *
	 * @param array $fieldList 保存を許すフィールドの配列
	 * @return bool
	 */
	public function save($data = null, $validate = true, $fieldList = [])
	{
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

		$path = WWW_ROOT . 'theme' . DS;
		if ($path . $data['old_name'] != $path . $data['name']) {
			if (!rename($path . $data['old_name'], $path . $data['name'])) {
				return false;
			}
		}

		$reader = new BcThemeConfigReader();
		$data = array_merge($reader->read($data['name']), $data);
		$reader->dump($data['name'], $data);

		return true;
	}

}
