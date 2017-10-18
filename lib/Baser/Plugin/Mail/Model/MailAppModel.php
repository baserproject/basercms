<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * メールプラグインモデル根底クラス
 *
 * @package			Mail.Model
 */
class MailAppModel extends AppModel {

/**
 * データの消毒をおこなう
 * @return array
 */
	public function sanitizeData($datas) {
		return $this->sanitizeRecord($datas);
	}

/**
 * サニタイズされたデータを復元する
 * @return array
 */
	public function restoreData($datas) {
		foreach ($datas as $key => $data) {
			if (!is_array($data)) {
				$data = str_replace("<br />", "", $data);
				$data = str_replace("<br>", "", $data);
				$data = str_replace('&lt;', '<', $data);
				$data = str_replace('&gt;', '>', $data);
				$data = str_replace('&amp;', '&', $data);
				$data = str_replace('&quot;', '"', $data);
				$datas[$key] = $data;
			}
		}
		return $datas;
	}

}
