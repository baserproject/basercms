<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcTextHelper', 'View/Helper');

/**
 * メールデータヘルパー
 *
 * @package Mail.View.Helper
 * @property BcBaserHelper $BcBaser
 *
 */
class MaildataHelper extends BcTextHelper {

	public $helpers = array('BcTime', 'BcBaser');

/**
 * メール表示用のデータを出力する
 * ※互換性維持用
 *
 * @param string $type コントロールタイプ
 * @param mixed $value 変換前の値
 * @param array|string $options コントロールソース
 * @return string メール用データ
 */
	public function control($type, $value, $options = "") {
		return ' ' . $this->toDisplayString($type, $value, $options);
	}

/**
 * メール表示用のデータを出力する
 *
 * @param string $type コントロールタイプ
 * @param mixed $value 変換前の値
 * @param array|string $options コントロールソース
 * @return string メール用データ
 */
	public function toDisplayString($type, $value, $options = "") {
		// コントロールソースの配列変換
		if (!is_array($options)) {
			$options = explode("|", $options);
		}
		$options = am(array(0 => ""), $options);

		switch ($type) {
			case 'text':
			case 'tel':
			case 'textarea':
			case 'email':
			case 'hidden':
			case 'check':
			case 'radio':
			case 'select':
				return $value;

			case 'pref':
				$prefs = $this->prefList();
				$options = array();
				foreach($prefs as $pref) {
					$options[$pref] = $pref;
				}
				if (isset($options[$value])) {
					return $options[$value];
				}
				return '';

			case 'multi_check':
				if (empty($value)) {
					return '';
				}

				if (!is_array($value)) {
					$value = explode("|", $value);
				}

				$out = '';
				foreach ($value as $key => $data) {
					if ($key != 0) {
						$out .= " ";
					}
					$out .= "・" . $data . PHP_EOL;
				}
				return $out;

			case 'file':
				if (empty($value)) {
					return '';
				}

				$mailContent = $this->_View->get('mailContent');
				$aryFile = explode('/', $value);
				$file = $aryFile[count($aryFile) - 1];
				$ext = decodeContent(null, $file);
				$link = array_merge(array('admin' => true, 'controller' => 'mail_messages', 'action' => 'attachment', $mailContent['MailContent']['id']), $aryFile);
				if (in_array($ext, array('gif', 'jpg', 'png'))) {
					return $this->BcBaser->getLink($this->BcBaser->getImg($link, array('width' => 400)), $link, array('target' => '_blank'));
				}

				return $this->BcBaser->getLink($file, $link);

			case 'date_time_calender':
				if (is_array($value)) {
					$value = $this->dateTime($value);
				}
				if ($value) {
					return date(__d('baser', 'Y年 m月 d日'), strtotime($value));
				}
				return '';

			case 'date_time_wareki':
				if (!is_array($value)) {
					$value = $this->BcTime->convertToWarekiArray($value);
				}
				return $this->dateTimeWareki($value);

			case 'autozip':
				if (strlen($value) == 7) {
					return substr($value, 0, 3) . '-' . substr($value, 3, 7);
				}
				return $value;

			default:
				return $value;
		}
	}
}
