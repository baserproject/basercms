<?php
/**
 * インストール時のデータ検証用モデル
 * DB接続はしない
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 3.1.0-dev
 * @license			http://basercms.net/license/index.html
 */
class Installation extends Model {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Installation';

/**
 * use table
 * 
 * @var bool
 * @access public
 */
	public $useTable = false;

/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'dbName' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'データベース名を入力してください。',
				'required' => true
			),
			array(
				'rule' => 'alphaNumericPlus',
				'message' => 'データベース名を半角英数字、ハイフン、アンダースコアのみで入力してください',
			)
		)
	);

/**
 * 英数チェックプラス
 *
 * ハイフンアンダースコアを許容
 *
 * @param array $check チェック対象配列
 * @return bool
 */
	public function alphaNumericPlus($check) {
		if (!$check[key($check)]) {
			return true;
		}
		return (bool)preg_match("/^[a-zA-Z0-9\-_]+$/", $check[key($check)]);
	}

}
