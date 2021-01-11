<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class ContentLink
 *
 * リンク モデル
 *
 * @package Baser.Model
 */
class ContentLink extends AppModel
{

	/**
	 * Behavior Setting
	 *
	 * @var array
	 */
	public $actsAs = ['BcContents'];

	/**
	 * ContentLink constructor.
	 *
	 * @param bool $id
	 * @param null $table
	 * @param null $ds
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		$this->validate = [
			'url' => [
				['rule' => ['notBlank'], 'message' => __d('baser', 'リンク先URLを入力してください。')]]
		];
	}

}
