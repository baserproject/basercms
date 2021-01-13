<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.Model
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

/**
 * ファイルアップローダー設定モデル
 *
 * @package         Uploader.Model
 */
class UploaderConfig extends AppModel
{

	/**
	 * プラグイン名
	 *
	 * @var        string
	 * @access    public
	 */
	public $plugin = 'Uploader';

	/**
	 * バリデート
	 *
	 * @var        array
	 * @access    public
	 */
	public $validate = [
		'large_width' => [['rule' => ['notBlank'],
			'message' => 'PCサイズ（大）[幅] を入力してください。']],
		'large_height' => [['rule' => ['notBlank'],
			'message' => 'PCサイズ（大）[高さ] を入力してください。']],
		'midium_width' => [['rule' => ['notBlank'],
			'message' => 'PCサイズ（中）[幅] を入力してください。']],
		'midium_height' => [['rule' => ['notBlank'],
			'message' => 'PCサイズ（中）[高さ] を入力してください。']],
		'small_width' => [['rule' => ['notBlank'],
			'message' => 'PCサイズ（小）[幅] を入力してください。']],
		'small_height' => [['rule' => ['notBlank'],
			'message' => 'PCサイズ（小）[高さ] を入力してください。']],
		'mobile_large_width' => [['rule' => ['notBlank'],
			'message' => '携帯サイズ（大）[幅] を入力してください。']],
		'mobile_large_height' => [['rule' => ['notBlank'],
			'message' => '携帯サイズ（大）[高さ] を入力してください。']],
		'mobile_small_width' => [['rule' => ['notBlank'],
			'message' => '携帯サイズ（小）[幅] を入力してください。']],
		'mobile_small_height' => [['rule' => ['notBlank'],
			'message' => '携帯サイズ（小）[幅] を入力してください。']]
	];
}
