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

namespace BaserCore\Model\Validation;

use Cake\Log\Log;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use BaserCore\Utility\BcUtil;
use Cake\Validation\Validation;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcValidation
 * @package BaserCore\Model\Validation
 */
class BcValidation extends Validation
{

    /**
     * 英数チェックプラス
     *
     * ハイフンアンダースコアを許容
     *
     * @param string $value チェック対象文字列
     * @param array $context 他に許容する文字列
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function alphaNumericPlus($value, $context = null)
    {
        if (!$value) {
            return true;
        }
        if (!is_null($context)) {
            if (is_array($context)) {
                if (array_key_exists('data', $context)) {
                    $context = [];
                }
            } else {
                $context = [$context];
            }
            $context = preg_quote(implode('', $context), '/');
        }
        if (preg_match("/^[a-zA-Z0-9\-_" . $context . "]+$/", $value)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 半角英数字+アンダーバー＋ハイフンのチェック
     *
     * @param string $value 確認する値を含む配列。先頭の要素のみチェックされる
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function alphaNumericDashUnderscore($value)
    {
        return (bool) preg_match('|^[0-9a-zA-Z_-]*$|', $value);
    }

    /**
     * 削除文字チェック
     *
     * BcUtile::urlencode で、削除される文字のみで構成されているかチェック(結果ブランクになるためnotBlankになる確認)
     *
     * @param string $value チェック対象文字列
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function bcUtileUrlencodeBlank($value)
    {
        if (!$value) {
            return true;
        }

        if (preg_match("/^[\\'\|`\^\"\(\)\{\}\[\];\/\?:@&=\+\$,%<>#! 　]+$/", $value)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 最短の長さチェック
     * - 対象となる値の長さが、指定した最短値より長い場合、trueを返す
     *
     * @param mixed $value 対象となる値
     * @param int $min 値の最短値
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function minLength($value, $min): bool
    {
        $value = (is_array($value))? current($value) : $value;
        $length = mb_strlen($value, Configure::read('App.encoding'));
        return ($length >= $min);
    }

    /**
     * 最長の長さチェック
     * - 対象となる値の長さが、指定した最長値より短い場合、trueを返す
     *
     * @param mixed $value 対象となる値
     * @param int $max 値の最長値
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function maxLength($value, $max): bool
    {
        $value = (is_array($value))? current($value) : $value;
        $length = mb_strlen($value, Configure::read('App.encoding'));
        return ($length <= $max);
    }

    /**
     * 最大のバイト数チェック
     * - 対象となる値のサイズが、指定した最大値より短い場合、true を返す
     *
     * @param mixed $value 対象となる値
     * @param int $max バイト数の最大値
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function maxByte($value, $max)
    {
        $value = (is_array($value))? current($value) : $value;
        $byte = strlen($value);
        return ($byte <= $max);
    }

    /**
     * リストチェック
     * 対象となる値がリストに含まれる場合はエラー
     *
     * @param string $value 対象となる値
     * @param array $list リスト
     * @return boolean Succcess
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function notInList($value, $list)
    {
        return !in_array($value, $list);
    }

    /**
     * ファイルサイズチェック
     *
     * @param array $value チェック対象データ
     * @param int $size 最大のファイルサイズ
     * @return boolean
     * @link http://php.net/manual/ja/features.file-upload.errors.php
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function fileCheck($value, $size)
    {
        // post_max_size オーバーチェック
        // POSTを前提の検証としているため全ての受信データを検証
        // データの更新時は必ず$_POSTにデータが入っていることを前提とする
        if (!BcUtil::isConsole() && empty($_POST)) {
            Log::error('アップロードされたファイルは、PHPの設定 post_max_size ディレクティブの値を超えています。');
            return false;
        }
        $file = $value;
        // input[type=file] 自体が送信されていない場合サイズ検証を終了
        if ($file === null || !is_array($file)) {
            return true;
        }

        // upload_max_filesizeと$sizeを比較し小さい数値でファイルサイズチェック
        $uploadMaxSize = BcUtil::convertSize(ini_get('upload_max_filesize'));
        $size = min([$size, $uploadMaxSize]);

        $fileErrorCode = Hash::get($file, 'error');
        if ($fileErrorCode) {
            // ファイルアップロード時のエラーメッセージを取得する
            switch($fileErrorCode) {
                // アップロード成功
                case 0:
                    // UPLOAD_ERR_OK
                    break;
                case 1:
                    // UPLOAD_ERR_INI_SIZE
                    Log::error('CODE: ' . $fileErrorCode . ' アップロードされたファイルは、php.ini の upload_max_filesize ディレクティブの値を超えています。');
                    return __('ファイルサイズがオーバーしています。 %s MB以内のファイルをご利用ください。', BcUtil::convertSize($size, 'M'));
                case 2:
                    // UPLOAD_ERR_FORM_SIZE
                    Log::error('CODE: ' . $fileErrorCode . ' アップロードされたファイルは、HTMLで指定された MAX_FILE_SIZE を超えています。');
                    return __('ファイルサイズがオーバーしています。 %s MB以内のファイルをご利用ください。', BcUtil::convertSize($size, 'M'));
                case 3:
                    // UPLOAD_ERR_PARTIAL
                    Log::error('CODE: ' . $fileErrorCode . ' アップロードされたファイルが不完全です。');
                    return __('何らかの原因でファイルをアップロードできませんでした。Webサイトの管理者に連絡してください。');
                // アップロードされなかった場合の検証は必須チェックを仕様すること
                case 4:
                    // UPLOAD_ERR_NO_FILE
                    // Log::error('CODE: ' . $fileErrorCode . ' ファイルがアップロードされませんでした。');
                    break;
                case 6:
                    // UPLOAD_ERR_NO_TMP_DIR
                    Log::error('CODE: ' . $fileErrorCode . ' 一時書込み用のフォルダがありません。テンポラリフォルダの書込み権限を見直してください。');
                    return __('何らかの原因でファイルをアップロードできませんでした。Webサイトの管理者に連絡してください。');
                case 7:
                    // UPLOAD_ERR_CANT_WRITE
                    Log::error('CODE: ' . $fileErrorCode . ' ディスクへの書き込みに失敗しました。');
                    return __('何らかの原因でファイルをアップロードできませんでした。Webサイトの管理者に連絡してください。');
                case 8:
                    // UPLOAD_ERR_EXTENSION
                    Log::error('CODE: ' . $fileErrorCode . ' PHPの拡張モジュールがファイルのアップロードを中止しました。');
                    return __('何らかの原因でファイルをアップロードできませんでした。Webサイトの管理者に連絡してください。');
                default:
                    break;
            }
        }

        if (!empty($file['name'])) {
            // サイズが空の場合は、HTMLのMAX_FILE_SIZEの制限によりサイズオーバー
            if (!$file['size']) {
                return __('ファイルサイズがオーバーしています。 %s MB以内のファイルをご利用ください。', BcUtil::convertSize($size, 'M'));
            }
            if ($file['size'] > $size) {
                return __('ファイルサイズがオーバーしています。 %s MB以内のファイルをご利用ください。', BcUtil::convertSize($size, 'M'));
            }
        }
        return true;
    }

    /**
     * ファイルの拡張子チェック
     *
     * @param array $value チェック対象データ
     * @param mixed $exts 許可する拡張子
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function fileExt($value, $exts)
    {
        $file = $value;
        if (!empty($file['name'])) {
            if (!is_array($exts)) {
                $exts = explode(',', $exts);
            }
            $ext = BcUtil::decodeContent($file['type'], $file['name']);
            if (in_array($ext, $exts)) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * ファイルが送信されたかチェックするバリデーション
     *
     * @param array $value
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     * @unitTest
     */
    public static function notFileEmpty($value)
    {
        $file = $value;
        if (empty($file) || (is_array($file) && $file['size'] === 0)) {
            return false;
        }
        return true;
    }

    /**
     * ２つのフィールド値を確認する
     *
     * @param string $value 対象となる値
     * @param mixed $fields フィールド名
     * @param array $context
     * @return    boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function confirm($value, $fields, $context)
    {
        $value1 = $value2 = '';
        if (is_array($fields) && count($fields) > 1) {
            if (isset($context['data'][$fields[0]]) &&
                isset($context['data'][$fields[1]])) {
                $value1 = $context['data'][$fields[0]];
                $value2 = $context['data'][$fields[1]];
            } else {
                return false;
            }
        } elseif ($fields) {
            if (is_array($fields)) {
                $fields = $fields[0];
            }
            if (isset($value) && isset($context['data'][$fields])) {
                $value1 = $value;
                $value2 = $context['data'][$fields];
            } else {
                return false;
            }
        } else {
            return false;
        }
        if ($value1 != $value2) {
            return false;
        }
        return true;
    }

    /**
     * 複数のEメールチェック（カンマ区切り）
     *
     * @param string $value 複数のメールアドレス
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function emails($value)
    {
        $emails = [];
        if (strpos($value, ',') !== false) {
            $emails = explode(',', $value);
        }
        if (!$emails) {
            $emails = [$value];
        }
        $result = true;
        foreach($emails as $email) {
            if (!Validation::email($email)) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * HABTM 用マルチチェックボックスの未選択チェック
     * @param mixed $value
     * @param array $context
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function notEmptyMultiple($value, $context)
    {
        if (isset($value['_ids'])) {
            $value = $value['_ids'];
        }
        if (!is_array($value)) {
            return false;
        }
        foreach($value as $v) {
            if ($v) {
                return true;
            }
        }
        return false;
    }

    /**
     * 半角チェック
     *
     * @param string $value 確認する値を含む配列
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function halfText($value)
    {
        $len = strlen($value);
        $mbLen = mb_strlen($value, 'UTF-8');
        if ($len != $mbLen) {
            return false;
        }
        return true;
    }

    /**
     * 日付の正当性チェック
     *
     * @param string $value 確認する値
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function checkDate($value)
    {
        if (!$value instanceof FrozenTime) return false;
        return true;
    }

    /**
     * 日時チェック
     * - 開始日時が終了日時より過去の場合、true を返す
     *
     * @param mixed $value 対象となる値
     * @param string $begin 開始日時フィールド名
     * @param string $end 終了日時フィールド名
     * @param array $context
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function checkDateRange($value, $fields, $context)
    {
        if (!empty($context['data'][$fields[0]]) &&
            !empty($context['data'][$fields[1]])) {
            if (strtotime($context['data'][$fields[0]]) >=
                strtotime($context['data'][$fields[1]])) {
                return false;
            }
        }
        return true;
    }

    /**
     * 指定した日付よりも新しい日付かどうかチェックする
     *
     * @param FrozenTime $fieldValue 対象となる日付
     * @param array $context
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function checkDateAfterThan($fieldValue, $target, $context)
    {
        if ($fieldValue instanceof FrozenTime && !empty($context['data'][$target])) {
            return $fieldValue->greaterThan($context['data'][$target]);
        }
        return true;
    }

    /**
     * スクリプトが埋め込まれているかチェックする
     * - 管理グループの場合は無条件に true を返却
     * - 管理グループ以外の場合に許可されている場合は無条件に true を返却
     *
     * @param string $value
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function containsScript($value)
    {
        if(!$value) return true;
        $events = ['onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove',
            'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup', 'onload', 'onunload',
            'onfocus', 'onblur', 'onsubmit', 'onreset', 'onselect', 'onchange'];
        if (BcUtil::isAdminUser() || Configure::read('BcApp.allowedPhpOtherThanAdmins')) {
            return true;
        }
        if (preg_match('/(<\?=|<\?php|<script)/i', $value)) {
            return false;
        }
        if (preg_match('/<[^>]+?(' . implode('|', $events) . ')=("|\')[^>]*?>/i', $value)) {
            return false;
        }
        if (preg_match('/href=\s*?("|\')[^"\']*?javascript\s*?:/i', $value)) {
            return false;
        }
        return true;
    }

    /**
     * 全角カタカナチェック
     *
     * @param mixed $value 対象となる値
     * @param string $addAllow 追加で許可する文字（初期値: 半角スペース・全角スペース）
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     *
     * 半角と全角のスペースを許容しない場合はvaliadtionのrule設定で空の文字列を渡す
     * 'rule' => ['checkKatakana', '']
     */
    public static function checkKatakana($value, $addAllow = '\s　') : bool
    {
        if (!is_string($addAllow)) {
            $addAllow = '\s　';
        }

        if ($value === '') {
            return true;
        }
        if(preg_match("/^[ァ-ヾ" . $addAllow . "]+$/u", $value)){
            return true;
        }
        return false;
    }

    /**
     * 全角ひらがなチェック
     *
     * @param mixed $value 対象となる値
     * @param string $addAllow 追加で許可する文字（初期値: 半角スペース・全角スペース・長音）
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     *
     * 半角と全角のスペースを許容しない場合はvaliadtionのrule設定で空の文字列を渡す
     * 'rule' => ['checkHiragana', '']
     *
     */
    public static function checkHiragana($value, $addAllow = '\s　ー') : bool
    {
        if (!is_string($addAllow)) {
            $addAllow = '\s　ー';
        }

        if ($value === '') {
            return true;
        }
        if(preg_match("/^[ぁ-ゞ" . $addAllow . "]+$/u", $value)){
            return true;
        }
        return false;
    }
}
