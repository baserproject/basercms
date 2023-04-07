<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMail\View\Helper;

use BaserCore\Utility\BcUtil;
use BaserCore\View\Helper\BcTextHelper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メールデータヘルパー
 *
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
        return $escape ? h($toDisplayString) : $toDisplayString;
    }

	/**
	 * メール表示用のデータを出力する
	 *
	 * @param string $type コントロールタイプ
	 * @param mixed $value 変換前の値
	 * @param bool $prefixSpace
	 * @return string メール用データ
	 */
    public function toDisplayString(string $type, $value, bool $prefixSpace = true)
    {
    	$result = '';
        switch ($type) {
            case 'text':
            case 'radio':
            case 'select':
            case 'email':
            case 'tel':
            case 'password':
                $result = $value;

            case 'pref':
                $prefs = $this->prefList();
                $options = [];
                foreach ($prefs as $pref) {
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
                $ext = BcUtil::decodeContent(null, $file);
                $link = array_merge([
					'admin' => true,
					'controller' => 'mail_messages',
					'action' => 'attachment',
					$mailContent['MailContent']['id']
				], $aryFile);
                if (in_array($ext, ['gif', 'jpg', 'png'])) {
                    $result = $this->BcBaser->getLink(
                        $this->BcBaser->getImg($link, ['width' => 400]),
                        $link,
                        ['target' => '_blank']
                    );
				} else {
					$result = $this->BcBaser->getLink($file, $link);
                }

                return $this->BcBaser->getLink($file, $link);

            case 'date_time_calender':
				if (is_array($value)) $value = $this->dateTime($value);
				if ($value) $result = date(__d('baser_core', 'Y年 m月 d日'), strtotime($value));
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
