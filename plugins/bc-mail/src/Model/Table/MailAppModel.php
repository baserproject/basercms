<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * メールプラグインモデル根底クラス
 *
 * @package         Mail.Model
 */
class MailAppModel extends AppModel
{

	/**
	 * データの消毒をおこなう
	 * @return array
	 * @deprecated 5.0.0 since 4.1.3 htmlspecialchars を利用してください。
	 */
	public function sanitizeData($datas)
	{
		trigger_error(
			deprecatedMessage(
				'メソッド：BcAppModel::sanitizeData()',
				'4.0.0',
				'5.0.0',
				'htmlspecialchars を利用してください。'
			),
			E_USER_DEPRECATED
		);
		return $this->sanitizeRecord($datas);
	}

	/**
	 * サニタイズされたデータを復元する
	 * @return array
	 * @deprecated 5.0.0 since 4.1.3 htmlspecialchars_decode を利用してください。
	 */
	public function restoreData($datas)
	{
		trigger_error(
			deprecatedMessage(
				'メソッド：MailAppModel::restoreData()',
				'4.1.3',
				'5.0.0',
				'htmlspecialchars_decode を利用してください。'
			),
			E_USER_DEPRECATED
		);
		foreach($datas as $key => $data) {
			if (is_array($data)) {
				continue;
			}
			$datas[$key] = str_replace(
				['<br />', '<br>', '&lt;', '&gt;', '&amp;', '&quot;'],
				['', '', '<', '>', '&', '"'],
				$data
			);
		}
		return $datas;
	}

}
