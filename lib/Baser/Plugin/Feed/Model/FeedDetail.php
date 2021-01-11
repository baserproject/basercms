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
 * feed_detail
 *
 * @package Feed.Model
 */
class FeedDetail extends FeedAppModel
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'FeedDetail';

	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = ['BcCache'];

	/**
	 * belongsTo
	 *
	 * @var array
	 */
	public $belongsTo = ['FeedConfig' => ['className' => 'Feed.FeedConfig',
		'conditions' => '',
		'order' => '',
		'foreignKey' => 'feed_config_id'
	]];

	/**
	 * FeedDetail constructor.
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
				['rule' => ['notBlank'], 'message' => __d('baser', 'フィード詳細名を入力してください。'), 'required' => true],
				['rule' => ['maxLength', 50], 'message' => __d('baser', 'フィード詳細名は50文字以内で入力してください。')]],
			'url' => [
				['rule' => ['notBlank'], 'message' => __d('baser', 'フィードURLを入力してください。'), 'required' => true],
				['rule' => ['maxLength', 255], 'message' => __d('baser', 'フィードURLは255文字以内で入力してください。')]],
			'category_filter' => [
				['rule' => ['maxLength', 255], 'message' => __d('baser', 'カテゴリフィルタは255文字以内で入力してください。')]]
		];
	}

	/**
	 * コントロールソースを取得する
	 *
	 * @param string $field フィールド名
	 * @return array コントロールソース
	 * @access    public
	 */
	public function getControlSource($field = null)
	{
		$controlSources['cache_time'] = ['+1 minute' => __d('baser', '1分'),
			'+30 minutes' => __d('baser', '30分'),
			'+1 hour' => __d('baser', '1時間'),
			'+6 hours' => __d('baser', '6時間'),
			'+24 hours' => __d('baser', '1日')];
		return $controlSources[$field];
	}

	/**
	 * 初期値を取得する
	 *
	 * @param string $feedDetailId
	 * @retun array $data
	 */
	public function getDefaultValue($feedConfigId)
	{
		$feedConfig = $this->FeedConfig->find('first', ['conditions' => ['FeedConfig.id' => $feedConfigId]]);
		$data[$this->name]['feed_config_id'] = $feedConfigId;
		$data[$this->name]['name'] = $feedConfig['FeedConfig']['name'];
		$data[$this->name]['cache_time'] = '+30 minutes';
		return $data;
	}

}
