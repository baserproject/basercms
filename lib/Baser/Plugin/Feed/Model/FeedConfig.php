<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Feed.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('FeedAppModel', 'Feed.Model');

/**
 * フィード設定モデル
 *
 * @package Feed.Model
 */
class FeedConfig extends FeedAppModel
{

	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = ['BcCache'];

	/**
	 * hasMany
	 *
	 * @var array
	 */
	public $hasMany = ["FeedDetail" =>
		["className" => "Feed.FeedDetail",
			"conditions" => "",
			"order" => "FeedDetail.id ASC",
			"foreignKey" => "feed_config_id",
			"dependent" => true,
			"exclusive" => false,
			"finderQuery" => ""]];

	/**
	 * FeedConfig constructor.
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
				['rule' => ['notBlank'], 'message' => __d('baser', 'フィード設定名を入力してください。'), 'required' => true],
				['rule' => ['maxLength', 50], 'message' => __d('baser', 'フィード設定名は50文字以内で入力してください。')]],
			'feed_title_index' => [
				['rule' => ['maxLength', 255], 'message' => __d('baser', 'フィードタイトルリストは255文字以内で入力してください。')]],
			'category_index' => [
				['rule' => ['maxLength', 255], 'message' => __d('baser', 'カテゴリリストは255文字以内で入力してください。')]],
			'display_number' => [
				['rule' => 'numeric', 'message' => __d('baser', '数値を入力してください。'), 'required' => true]],
			'template' => [
				['rule' => ['notBlank'], 'message' => __d('baser', 'テンプレート名を入力してください。')],
				['rule' => 'halfText', 'message' => __d('baser', 'テンプレート名は半角のみで入力してください。')],
				['rule' => ['maxLength', 50], 'message' => __d('baser', 'テンプレート名は50文字以内で入力してください。')]]
		];
	}

	/**
	 * 初期値を取得
	 *
	 * @return array
	 */
	public function getDefaultValue()
	{
		$data['FeedConfig']['display_number'] = '5';
		$data['FeedConfig']['template'] = 'default';
		return $data;
	}

}
