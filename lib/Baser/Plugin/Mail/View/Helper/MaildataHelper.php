<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.View.Helper
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcTextHelper', 'View/Helper');

/**
 * メールデータヘルパー
 *
 * @package Mail.View.Helper
 * @property BcBaserHelper $BcBaser
 *
 */
class MaildataHelper extends BcTextHelper
{

	public $helpers = ['BcTime', 'BcBaser'];

	/**
	 * メール表示用のデータを出力する
	 * ※互換性維持用
	 *
	 * @param string $type コントロールタイプ
	 * @param mixed $value 変換前の値
	 * @param array|string $options コントロールソース
	 * @param bool $escape エスケープ処理を行うかどうか （初期値 : true）
	 * @return string メール用データ
	 */
	public function control($type, $value, $escape = true)
	{
		$toDisplayString = $this->toDisplayString($type, $value);
		return $escape? h($toDisplayString) : $toDisplayString;
	}

	/**
	 * メール表示用のデータを出力する
	 *
	 * @param string $type コントロールタイプ
	 * @param mixed $value 変換前の値
	 * @param array|string $options コントロールソース
	 * @return string メール用データ
	 */
	public function toDisplayString($type, $value, $prefixSpace = true)
	{
		$result = '';
		switch($type) {
			case 'text':
			case 'radio':
			case 'select':
			case 'email':
			case 'tel':
			case 'number':
			case 'password':
				$result = $value;
				break;

			case 'pref':
				$prefs = $this->prefList();
				$options = [];
				foreach($prefs as $pref) {
					$options[$pref] = $pref;
				}
				if (isset($options[$value])) $result = $options[$value];
				break;

			case 'multi_check':
				if ($value === '') break;
				if (!is_array($value)) $value = explode("|", $value);
				$result = $value;
				break;

			case 'file':
				$prefixSpace = false;
				if (empty($value)) break;
				$mailContent = $this->_View->get('mailContent');
				$aryFile = explode('/', $value);
				$file = $aryFile[count($aryFile) - 1];
				$ext = decodeContent(null, $file);
				$link = array_merge([
						'admin' => true,
						'controller' => 'mail_messages',
						'action' => 'attachment',
						$mailContent['MailContent']['id']
				], $aryFile);
				if (in_array($ext, ['gif', 'jpg', 'png'])) {
					$result = $this->BcBaser->getLink(
						$this->BcBaser->getImg($link, ['width' => 400]), $link, ['target' => '_blank']
					);
				} else {
					$result = $this->BcBaser->getLink($file, $link);
				}
				break;

			case 'date_time_calender':
				if (is_array($value)) $value = $this->dateTime($value);
				if ($value) $result = date(__d('baser', 'Y年 m月 d日'), strtotime($value));
				break;

			case 'date_time_wareki':
				if (!is_array($value)) $value = $this->BcTime->convertToWarekiArray($value);
				$result = $this->dateTimeWareki($value);
				break;

			case 'autozip':
				if (strlen($value) == 7) {
					$result = substr($value, 0, 3) . '-' . substr($value, 3, 7);
				} else {
					$result = $value;
				}
				break;

			default:
				$prefixSpace = false;
				$result = $value;
		}

		if(is_array($result)) {
			$out = '';
			foreach($result as $v) {
				$v = "・" . $v . PHP_EOL;
				if($prefixSpace) $v = ' ' . $v;
				$out .= $v;
			}
			$result = $out;
		} else {
			if($prefixSpace && $result) $result = ' ' . $result;
		}
		return $result;
	}
}
