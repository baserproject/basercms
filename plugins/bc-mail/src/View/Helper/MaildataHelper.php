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
     * @param array|string $options コントロールソース
     * @return string メール用データ
     */
    public function toDisplayString($type, $value)
    {
        switch ($type) {
            case 'text':
            case 'radio':
            case 'select':
            case 'email':
            case 'tel':
            case 'password':
                return ' ' . $value;

            case 'pref':
                $prefs = $this->prefList();
                $options = [];
                foreach ($prefs as $pref) {
                    $options[$pref] = $pref;
                }
                if (isset($options[$value])) {
                    return ' ' . $options[$value];
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
                    $out .= " ・" . $data . PHP_EOL;
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
                $link = array_merge(
                    [
                        'admin' => true,
                        'controller' => 'mail_messages',
                        'action' => 'attachment',
                        $mailContent['MailContent']['id']
                    ],
                    $aryFile
                );
                if (in_array($ext, ['gif', 'jpg', 'png'])) {
                    return $this->BcBaser->getLink(
                        $this->BcBaser->getImg($link, ['width' => 400]),
                        $link,
                        ['target' => '_blank']
                    );
                }

                return $this->BcBaser->getLink($file, $link);

            case 'date_time_calender':
                if (is_array($value)) {
                    $value = $this->dateTime($value);
                }
                if ($value) {
                    return ' ' . date(__d('baser_core', 'Y年 m月 d日'), strtotime($value));
                }
                return '';

            case 'date_time_wareki':
                if (!is_array($value)) {
                    $value = $this->BcTime->convertToWarekiArray($value);
                }
                return ' ' . $this->dateTimeWareki($value);

            case 'autozip':
                if (strlen($value) == 7) {
                    return substr($value, 0, 3) . '-' . substr($value, 3, 7);
                }
                return ' ' . $value;

            default:
                return $value;
        }
    }
}
