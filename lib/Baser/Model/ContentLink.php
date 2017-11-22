<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * リンク モデル
 *
 * @package Baser.Model
 */
class ContentLink extends AppModel {

/**
 * Behavior Setting
 *
 * @var array
 */
	public $actsAs = ['BcContents'];

	/**
	 * バリデーション
	 *
	 * @var array
	 */
	public $validate = [
		'url' => [
			['rule' => ['notBlank'],
			 'message' => 'リンク先URLを入力してください。']
	]];
}
