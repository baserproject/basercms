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

use BaserCore\Utility\BcContainerTrait;
use Cake\Http\Client\Request;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use BaserCore\Utility\BcUtil;
use Cake\Validation\Validation;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Laminas\Diactoros\UploadedFile;

/**
 * Class BcValidation
 */
class BcValidation extends Validation
{

    /**
     * BcContainerTrait
     */
    use BcContainerTrait;

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
     * @param mixed $check 対象となる値
     * @param int $min 値の最短値
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function minLength($check, int $min): bool
    {
        $check = (is_array($check))? current($check) : $check;
        $length = mb_strlen($check, Configure::read('App.encoding'));
        return ($length >= $min);
    }

    /**
     * 最長の長さチェック
     * - 対象となる値の長さが、指定した最長値より短い場合、trueを返す
     *
     * @param mixed $check 対象となる値
     * @param int $max 値の最長値
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function maxLength($check, int $max): bool
    {
        $check = (is_array($check))? current($check) : $check;
        $length = mb_strlen($check, Configure::read('App.encoding'));
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
            Log::error(__d('baser_core', 'アップロードされたファイルは、PHPの設定 post_max_size ディレクティブの値を超えています。'));
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
                    Log::error(__d('baser_core', 'CODE: {0} アップロードされたファイルは、php.ini の upload_max_filesize ディレクティブの値を超えています。 {1} MB以内のファイルをご利用ください。', $fileErrorCode, BcUtil::convertSize($size, 'M')));
                    return false;
                case 2:
                    // UPLOAD_ERR_FORM_SIZE
                    Log::error(__d('baser_core', 'CODE: {0} アップロードされたファイルは、HTMLで指定された MAX_FILE_SIZE を超えています。 {1} MB以内のファイルをご利用ください。', $fileErrorCode, BcUtil::convertSize($size, 'M')));
                    return false;
                case 3:
                    // UPLOAD_ERR_PARTIAL
                    Log::error(__d('baser_core', 'CODE: {0} アップロードされたファイルが不完全です。', $fileErrorCode));
                    return false;
                case 4:
                    // UPLOAD_ERR_NO_FILE
                    Log::error(__d('baser_core', 'CODE: {0} ファイルがアップロードされませんでした。', $fileErrorCode));
                    break;
                case 6:
                    // UPLOAD_ERR_NO_TMP_DIR
                    Log::error(__d('baser_core', 'CODE: {0} 一時書込み用のフォルダがありません。テンポラリフォルダの書込み権限を見直してください。', $fileErrorCode));
                    return false;
                case 7:
                    // UPLOAD_ERR_CANT_WRITE
                    Log::error(__d('baser_core', 'CODE: {0} ディスクへの書き込みに失敗しました。', $fileErrorCode));
                    return __d('baser_core', '何らかの原因でファイルをアップロードできませんでした。Webサイトの管理者に連絡してください。');
                case 8:
                    // UPLOAD_ERR_EXTENSION
                    Log::error(__d('baser_core', 'CODE: {0} PHPの拡張モジュールがファイルのアップロードを中止しました。', $fileErrorCode));
                    return false;
                default:
                    break;
            }
        }

        if (!empty($file['name'])) {
            // サイズが空の場合は、HTMLのMAX_FILE_SIZEの制限によりサイズオーバー
            if (!$file['size']) {
                Log::error(__d('baser_core', 'ファイルサイズがオーバーしています。 {0} MB以内のファイルをご利用ください。', BcUtil::convertSize($size, 'M')));
                return false;
            }
            if ($file['size'] > $size) {
                Log::error(__d('baser_core', 'ファイルサイズがオーバーしています。 {0} MB以内のファイルをご利用ください。', BcUtil::convertSize($size, 'M')));
                return false;
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
    public static function fileExt($file, $exts)
    {
        if (!is_array($exts)) $exts = explode(',', $exts);
        if($file instanceof UploadedFile) {
            $fileName = $file->getClientFilename();
            $type = $file->getClientMediaType();
        } elseif(is_array($file)) {
            $fileName = $file['name'];
            $type = $file['type'];
            if ($file['error']) {
                return __d('baser_core', 'ファイルのアップロードに失敗したため、拡張子の判定に失敗しました。');
            }
        } else {
            $fileName = $file;
            $type = null;
        }
        if (empty($fileName)) return true;

        // FILES形式のチェック
        if ($type) {
            $ext = BcUtil::decodeContent($type, $fileName);
            if (!in_array($ext, $exts)) {
                return false;
            }
        } else {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if (!in_array($ext, $exts)) {
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
     * @param string $fieldValue 対象となる日付
     * @param array $context
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function checkDateAfterThan($fieldValue, $target, $context)
    {
        if (!empty($fieldValue) && !empty($context['data'][$target])) {
            try {
                $startDate = new FrozenTime($fieldValue);
                $endDate = new FrozenTime($context['data'][$target]);
            } catch (\Exception) {
                return false;
            }
            return $startDate->greaterThan($endDate);
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
        $value = (string)$value;
        if ($value === '') return true;
        // 管理者、または管理者以外のPHP等を許可する設定の場合は無条件で許可
        if (BcUtil::isAdminUser() || Configure::read('BcApp.allowedPhpOtherThanAdmins')) {
            return true;
        }

        // PHPタグ・生のscriptタグはDOM解析では検出しづらいため、文字列で先に検査する
        if (preg_match('/<\?(php|=|\s|$)|<script/i', $value)) {
            return false;
        }

        // (A) URI系属性値に、実体参照などで難読化された危険スキームが含まれていないか
        if (self::containsDangerousUriScheme($value)) {
            return false;
        }

        // (B) HTMLPurifier で浄化した結果、許可外の要素・属性・スキームが除去されていないか
        //     （エラーメッセージではなく、浄化前後の構造差分で判定する）
        if (self::purifyRemovesUnsafeStructure($value)) {
            return false;
        }

        return true;
    }

    /**
     * containsScript 用の HTMLPurifier 設定を構築する
     *
     * 許可する要素・属性・CSSプロパティは setting.php の BcApp.containsScript から読み込む。
     *
     * @return \HTMLPurifier_Config
     * @noTodo
     * @checked
     */
    protected static function getContainsScriptPurifierConfig(): \HTMLPurifier_Config
    {
        $settings = (array)Configure::read('BcApp.containsScript');
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Core.CollectErrors', false);
        $config->set('Cache.DefinitionImpl', null);
        $config->set('HTML.DefinitionID', 'basercms-containsScript');
        $config->set('HTML.DefinitionRev', 2);
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', $settings['safeIframeRegexp'] ?? '%^(https?:)?//%');
        $config->set('Attr.EnableID', !empty($settings['enableId']));
        if (!empty($settings['allowedFrameTargets'])) {
            $config->set('Attr.AllowedFrameTargets', $settings['allowedFrameTargets']);
        }
        if (!empty($settings['allowedRel'])) {
            $config->set('Attr.AllowedRel', $settings['allowedRel']);
        }
        if (!empty($settings['allowedCssProperties'])) {
            $config->set('CSS.AllowedProperties', $settings['allowedCssProperties']);
        }
        if ($def = $config->maybeGetRawHTMLDefinition()) {
            foreach (($settings['allowedElements'] ?? []) as $name => $params) {
                $params = array_values((array)$params) + ['Block', 'Flow', 'Common', []];
                $def->addElement($name, $params[0], $params[1], $params[2], $params[3]);
            }
            foreach (($settings['allowedAttributes'] ?? []) as $key => $attrType) {
                if (strpos((string)$key, '.') === false) continue;
                [$tag, $attr] = explode('.', $key, 2);
                $def->addAttribute($tag, $attr, $attrType);
            }
        }
        return $config;
    }

    /**
     * HTML断片を DOMDocument に読み込む
     *
     * @param string $html
     * @return \DOMDocument|null
     * @noTodo
     * @checked
     */
    protected static function loadHtmlFragment(string $html): ?\DOMDocument
    {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $loaded = $doc->loadHTML(
            '<?xml encoding="UTF-8"><body>' . $html . '</body>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        return $loaded ? $doc : null;
    }

    /**
     * 属性値を実体参照・数値文字参照・制御文字まで踏み込んでデコード・正規化する
     *
     * html_entity_decode に ENT_HTML5 を付与し &colon; &Tab; &NewLine; などの
     * HTML5名前付き参照に対応させ、さらにセミコロン無しの数値文字参照もデコードする。
     *
     * @param string $value
     * @return string
     * @noTodo
     * @checked
     */
    protected static function normalizeAttrValueForSchemeCheck(string $value): string
    {
        $value = preg_replace_callback('/&#x([0-9a-f]+);?/i', fn($m) => (string)mb_chr(hexdec($m[1]), 'UTF-8'), $value);
        $value = preg_replace_callback('/&#(\d+);?/', fn($m) => (string)mb_chr((int)$m[1], 'UTF-8'), $value);
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return preg_replace('/[\s\x00-\x20\x7f]+/u', '', $value);
    }

    /**
     * URI系属性値に危険なスキームが含まれるか（実体参照による難読化を含めて検査する）
     *
     * @param string $html
     * @return bool
     * @noTodo
     * @checked
     */
    protected static function containsDangerousUriScheme(string $html): bool
    {
        $settings = (array)Configure::read('BcApp.containsScript');
        $uriAttributes = $settings['uriAttributes'] ?? ['href', 'src', 'action', 'formaction', 'codebase', 'data', 'xlink:href', 'poster', 'background', 'dynsrc', 'lowsrc', 'cite'];
        $schemes = $settings['dangerousSchemes'] ?? ['javascript', 'vbscript', 'livescript', 'mocha', 'data'];
        $doc = self::loadHtmlFragment($html);
        if (!$doc) return false;
        foreach ($doc->getElementsByTagName('*') as $el) {
            if (!$el->hasAttributes()) continue;
            foreach ($el->attributes as $attr) {
                if (!in_array(strtolower($attr->name), $uriAttributes, true)) continue;
                $decoded = self::normalizeAttrValueForSchemeCheck($attr->value);
                foreach ($schemes as $scheme) {
                    if (preg_match('/^' . preg_quote($scheme, '/') . ':/i', $decoded)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * HTMLから要素名・属性名・URIスキームの集合を抽出する
     *
     * @param string $html
     * @return array
     * @noTodo
     * @checked
     */
    protected static function extractHtmlStructureSets(string $html): array
    {
        $elements = $attributes = $schemes = [];
        $doc = self::loadHtmlFragment($html);
        if (!$doc) return compact('elements', 'attributes', 'schemes');
        $uriAttributes = Configure::read('BcApp.containsScript.uriAttributes')
            ?? ['href', 'src', 'action', 'formaction', 'codebase', 'data', 'xlink:href', 'poster', 'background', 'dynsrc', 'lowsrc', 'cite'];
        foreach ($doc->getElementsByTagName('*') as $node) {
            $elements[strtolower($node->nodeName)] = true;
            if (!$node->hasAttributes()) continue;
            foreach ($node->attributes as $attr) {
                $name = strtolower($attr->name);
                $attributes[$name] = true;
                if (in_array($name, $uriAttributes, true)
                    && preg_match('/^\s*([a-z0-9.+\-]+)\s*:/i', $attr->value, $m)) {
                    $schemes[strtolower($m[1])] = true;
                }
            }
        }
        return compact('elements', 'attributes', 'schemes');
    }

    /**
     * HTMLPurifier で浄化した結果、許可外の危険な要素・属性・スキームが除去されたか
     *
     * 浄化前後の「要素名・属性名・URIスキーム」の集合を比較し、除去されたものがあれば
     * 危険または不正なHTMLとみなす。属性の除去は safeRemovedAttributePrefixes（data-* 等）を除外する。
     *
     * @param string $html
     * @return bool
     * @noTodo
     * @checked
     */
    protected static function purifyRemovesUnsafeStructure(string $html): bool
    {
        $config = self::getContainsScriptPurifierConfig();
        $purifier = new \HTMLPurifier($config);
        $clean = $purifier->purify($html);

        $raw = self::extractHtmlStructureSets($html);
        $cleaned = self::extractHtmlStructureSets($clean);

        // 要素が除去された
        if (array_diff_key($raw['elements'], $cleaned['elements'])) {
            return true;
        }
        // 属性が除去された（安全リストのプレフィックスに一致するものは除く）
        $safePrefixes = Configure::read('BcApp.containsScript.safeRemovedAttributePrefixes') ?? ['data-'];
        foreach (array_diff_key($raw['attributes'], $cleaned['attributes']) as $name => $_) {
            $isSafe = false;
            foreach ($safePrefixes as $prefix) {
                if (str_starts_with($name, $prefix)) {
                    $isSafe = true;
                    break;
                }
            }
            if (!$isSafe) return true;
        }
        // URIスキームが除去・無害化された
        if (array_diff_key($raw['schemes'], $cleaned['schemes'])) {
            return true;
        }
        return false;
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
    public static function checkKatakana($value, $addAllow = '\s　'): bool
    {
        if (!is_string($addAllow)) {
            $addAllow = '\s　';
        }

        if ($value === '') {
            return true;
        }
        if (preg_match("/^[ァ-ヾ" . $addAllow . "]+$/u", $value)) {
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
    public static function checkHiragana($value, $addAllow = '\s　ー'): bool
    {
        if (!is_string($addAllow)) {
            $addAllow = '\s　ー';
        }

        if ($value === '') {
            return true;
        }
        if (preg_match("/^[ぁ-ゞ" . $addAllow . "]+$/u", $value)) {
            return true;
        }
        return false;
    }

    /**
     * 主にデータベースの予約語として利用できないフィールドかどうか判定
     *
     * @param $value
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function reserved($value): bool
    {
        if (in_array($value, Configure::read('BcApp.reservedWords'))) {
            return false;
        }
        return true;
    }

    /**
     * 選択リストに同じ項目を複数登録するかをチェック
     *
     * @param $value
     * @return bool
     * @checked
     * @notodo
     * @unitTest
     */
    public static function checkSelectList($value): bool
    {
        $data = preg_split("/\r\n|\n|\r/", $value);
        $result = max(array_count_values($data));
        return ($result < 2);
    }

    /**
     * 範囲を指定しての長さチェック
     *
     * @param mixed $value 対象となる値
     * @param int $min 値の最短値
     * @param int $max 値の最長値
     * @param boolean
     * @unitTest
     */
    public static function between($value, $min, $max)
    {
        $length = mb_strlen($value, Configure::read('App.encoding'));
        return ($length >= $min && $length <= $max);
    }

    /**
     * スペースしかない文字列
     *
     * @param $string
     * @return bool
     * @checked
     * @notodo
     * @unitTest
     */
    public static function notBlankOnlyString($string): bool
    {
        return (preg_replace("/( |　)/", '', $string) !== '');
    }

    /**
     * 16進数カラーコードチェック
     *
     * @param string $value 対象となる値
     * @return bool
     * @checked
     * @notodo
     * @unitTest
     */
    public static function hexColorPlus($value): bool
    {
        return preg_match('/\A([0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})\z/i', $value);
    }

    /**
     * Jsonをバリデーション
     * 半角小文字英数字とアンダースコアを許容
     * @param $string
     * @param $key
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function checkWithJson($string, $key, $regex)
    {
        $value = json_decode($string, true);
        $keys = explode('.', $key);

        foreach ($keys as $k) {
            $value = $value[$k] ?? '';
        }

        //入力チェックした項目だけバリデーション
        $request = Router::getRequest();
        $validate = $request->getData('validate');
        if (is_array($validate) && !in_array(strtoupper($k), $validate))
            return true;

        if (empty($value) || preg_match($regex, $value)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * url形式 (ルートパスと空白許容)であることを確認する
     * @param $string
     * @return bool
     */
    public static function rootPath($value):bool
    {

        // 文字列チェック
        if(!is_string($value)) {
            return false;
        }

        // 入力値が空文字は許容
        if ($value === '') {
            return true;
        }

        // `parse_url` でパス部分を取得
        $parsedUrl = parse_url($value);


        // `path` の存在を確認し、スラッシュから始まっているかをチェック
        return isset($parsedUrl['path']) && preg_match('/^\/[a-zA-Z0-9\-_\/]*$/', $parsedUrl['path']);
    }

}
