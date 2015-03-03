<?php

/**
 * メールプラグインモデル根底クラス
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * includes
 */
App::uses('BcPluginAppModel', 'Model');

/**
 * メールプラグインモデル根底クラス
 *
 * @package			Mail.Model
 */
class MailAppModel extends BcPluginAppModel {

/**
 * データの消毒をおこなう
 * @return array
 */
	public function sanitizeData($datas) {
		foreach ($datas as $key => $data) {

			if (!is_array($data)) {

				// エラー時用のサニタイズ処理を一旦元の形式に復元した上で再度サイニタイズ処理をかける。
				$data = str_replace("&lt;!--", "<!--", $data);

				$data = htmlspecialchars($data);
				//$data = str_replace("\n","<br />",$data);
				$datas[$key] = $data;
			}
		}
		return $datas;
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
