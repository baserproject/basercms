<?php

/**
 * メールデータヘルパー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::uses('BcTextHelper', 'View/Helper');

/**
 * メールデータヘルパー
 *
 * @package Mail.View.Helper
 *
 */
class MaildataHelper extends BcTextHelper {

	public $helpers = array('BcTime', 'BcBaser');

/**
 * メール表示用のデータを出力する
 * 
 * @param string $type コントロールタイプ
 * @param mixed $value 変換前の値
 * @param array $options コントロールソース
 * @return string メール用データ
 */
	public function control($type, $value, $options = "") {
		// コントロールソースの配列変換
		if (!is_array($options)) {
			$options = explode("|", $options);
		}
		$options = am(array(0 => ""), $options);

		$out = "";

		switch ($type) {

			case 'text':
			case 'email':
				$out = " " . $value;
				break;

			case 'radio':
				if (isset($options[$value])) {
					$out = " " . $options[$value];
				} else {
					$out = " ";
				}
				break;

			case 'select':
				if (isset($options[$value])) {
					$out = " " . $options[$value];
				} else {
					$out = " ";
				}
				break;

			case 'pref':
				$options = $pref = $this->prefList();
				if (isset($options[$value])) {
					$out = " " . $options[$value];
				} else {
					$out = " ";
				}
				break;

			case 'check':
				if ($options) {
					if (isset($options[$value])) {
						$out = $options[$value];
					} else {
						$out = " ";
					}
				} else {
					$out = " " . $value;
				}
				break;

			case 'multi_check':
				$out = "";
				if ($value) {
					if (!is_array($value)) {
						$value = explode("|", $value);
					}
					foreach ($value as $data) {
						if (isset($options[$data])) {
							$out .= "・" . $options[$data] . "\n";
						}
					}
				}
				break;
			
			case 'file':
				$out = '';
				if($value) {
					$mailContent = $this->_View->get('mailContent');
					$aryFile = explode('/', $value);
					$file = $aryFile[count($aryFile) - 1];
					$ext = decodeContent(null, $file);
					$link = array_merge(array('admin' => true, 'controller' => 'mail_messages', 'action' => 'attachment', $mailContent['MailContent']['id']), $aryFile);
					if(in_array($ext, array('gif', 'jpg', 'png'))) {
						$out = " " . $this->BcBaser->getLink($this->BcBaser->getImg($link, array('width' => 400)), $link, array('target' => '_blank'));
					} else {
						$out = " " . $this->BcBaser->getLink($file, $link);
					}
				}
				break;
				
			case 'date_time_calender':
				if (is_array($value)) {
					$value = $this->dateTime($value);
				}
				if ($value) {
					$out = " " . date('Y年 m月 d日', strtotime($value));
				}
				break;

			case 'date_time_wareki':
				if (!is_array($value)) {
					$value = $this->BcTime->convertToWarekiArray($value);
				}
				$out = " " . $this->dateTimeWareki($value);
				break;

			case 'textarea':
				$out = " " . $value;
				break;

			case 'autozip':
				if (strlen($value) == 7) {
					$out = " " . substr($value, 0, 3) . '-' . substr($value, 3, 7);
				} else {
					$out = " " . $value;
				}
				break;

			case 'hidden':
				$out = " " . $value;
				break;

			default:
				$out = " " . $value;
				break;
		}

		return $out;
	}

}
