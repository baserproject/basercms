<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.1.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBurgerEditor\Lib;

use Cake\Core\Configure;
use Cake\Core\Plugin;

class BurgerEditorUtil
{

    /**
     * 全面拒否用の .htaccess の内容
     *
     * mod_authz_core の有無で Apache 2.4 / 2.2 の記法を出し分ける。
     * php_flag は PHP-FPM 環境で「Invalid command」となり 500 を招くため使用しない。
     */
    const HTACCESS_DENY_ALL = <<<'HTACCESS'
# BurgerEditor が自動生成したファイルです。
# このディレクトリのファイルは BurgerEditor の dl アクション経由でのみ配信するため、
# 直接の HTTP アクセスを拒否します。
<IfModule mod_authz_core.c>
	Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
	Order allow,deny
	Deny from all
</IfModule>

HTACCESS;

    /**
     * 実行され得る拡張子のみ拒否する .htaccess のテンプレート
     *
     * 画像は直接配信する必要があるため全面拒否にはできない。
     * %s には Bge.deniedExtension から生成した拡張子の正規表現が入る。
     */
    const HTACCESS_DENY_EXECUTABLE = <<<'HTACCESS'
# BurgerEditor が自動生成したファイルです。
# 画像は直接配信するため、サーバーサイドで実行され得る拡張子のみ拒否します。
# 一覧は BurgerEditor の設定 Bge.deniedExtension から生成しています。
<FilesMatch "(?i)\.(%s)$">
	<IfModule mod_authz_core.c>
		Require all denied
	</IfModule>
	<IfModule !mod_authz_core.c>
		Order allow,deny
		Deny from all
	</IfModule>
</FilesMatch>

HTACCESS;

    /**
     * GoogleMapAPI Keyを取得
     *
     * @return string
     */
    public static function getGoogleMapApiKey()
    {
        $googleMapsApiKey = \BaserCore\Utility\BcSiteConfig::get('google_maps_api_key');
        return $googleMapsApiKey;
    }

    /**
     * 静的ファイルに対するサフィックスを取得する
     *
     * @param string filePath
     * @return string
     */
    public static function getSuffix($filePath)
    {
        if (!Configure::read('Bge.enableStaticFileSuffix')) {
            return '';
        }

        $modifiedTime = filemtime($filePath);
        if (!$modifiedTime) {
            return '';
        }
        $suffix = '?' . $modifiedTime;

        $suffixText = Configure::read('Bge.staticFileSuffix');
        if ($suffixText) {
            return $suffix .= '-' . $suffixText;
        }

        return $suffix;
    }

    /**
     * Addon のパスを取得する
     * @return string[]
     */
    public static function getAddonPath()
    {
        $addonDir = [dirname(dirname(dirname(__FILE__))) . DS . 'Addon' . DS];
        $enableAddonPlugin = Configure::read('Bge.enableAddonPlugin');
        if ($enableAddonPlugin) {
            foreach($enableAddonPlugin as $plugin) {
                if (!Plugin::isLoaded($plugin)) {
                    continue;
                }
                $plguinPath = Plugin::path($plugin);
                $pluginAddonPath = $plguinPath . 'BurgerAddon' . DS;
                if (is_dir($pluginAddonPath)) {
                    $addonDir[] = $pluginAddonPath;
                }
            }
        }
        return $addonDir;
    }

    /**
     * アップロードを常に拒否する拡張子の一覧を取得する
     *
     * Bge.deniedExtension を正規化して返す。アップロード時の判定と
     * .htaccess の生成の双方がこれを参照する（一覧の重複定義を避けるため）。
     * 配列とカンマ区切り文字列のどちらでも指定できるようにしている。
     *
     * @return string[]
     */
    public static function getDeniedExts()
    {
        $config = Configure::read('Bge.deniedExtension');
        if (is_string($config)) {
            $config = explode(',', $config);
        }
        if (!is_array($config)) {
            return [];
        }
        $exts = [];
        foreach($config as $ext) {
            if (!is_string($ext)) {
                continue;
            }
            $ext = strtolower(trim($ext));
            if ($ext === '') {
                continue;
            }
            $exts[] = $ext;
        }
        return array_values(array_unique($exts));
    }

    /**
     * アップロードディレクトリに .htaccess を設置する
     *
     * アップロード時の拡張子制限に加えた多層の措置。
     * files/bgeditor/other/ は dl アクション経由でのみ配信するため全面拒否とし、
     * files/bgeditor/img/ は直接配信が必要なため該当する拡張子のみ拒否する。
     * files/bgeditor/ 直下はサンプルPDF等を直リンクしているため対象外とする。
     *
     * 既存の .htaccess は上書きしない（サイト固有の設定を壊さないため）。
     *
     * @return void
     */
    public static function ensureUploadDirProtection()
    {
        $baseDir = WWW_ROOT . 'files' . DS . 'bgeditor' . DS;
        $targets = ['other' => self::HTACCESS_DENY_ALL];

        // 設定に不正な値が混ざっても Apache が構文エラーで 500 にならないよう、
        // 正規表現に埋め込む拡張子は英数字のみに限定する
        $exts = array_filter(self::getDeniedExts(), function($ext) {
            return preg_match('/^[a-z0-9]+$/', $ext) === 1;
        });
        if ($exts) {
            $targets['img'] = sprintf(self::HTACCESS_DENY_EXECUTABLE, implode('|', $exts));
        }

        foreach($targets as $dirName => $content) {
            $dir = $baseDir . $dirName . DS;
            if (!is_dir($dir) || !is_writable($dir)) {
                continue;
            }
            $htaccess = $dir . '.htaccess';
            if (file_exists($htaccess)) {
                continue;
            }
            file_put_contents($htaccess, $content);
        }
    }

    /**
     * タイプのパスを取得する
     * @param string $typeName
     * @return bool|string
     */
    public static function getTypePath($typeName)
    {
        $addonPath = self::getAddonPath();
        foreach($addonPath as $path) {
            $path = $path . 'type' . DS . $typeName . DS;
            if (is_dir($path)) {
                return $path;
            }
        }
        return false;
    }

    /**
     * ブロックのパスを取得する
     * @param string $typeName
     * @return bool|string
     */
    public static function getBlockPath($blockName)
    {
        $addonPath = self::getAddonPath();
        foreach($addonPath as $path) {
            $path = $path . 'block' . DS . $blockName . DS;
            if (is_dir($path)) {
                return $path;
            }
        }
        return false;
    }

        /**
     * 拡張子取得
     *
     * @param string $filename
     * @return boolean
     */
    public static function getExtension($filename)
    {
        $nameAry = explode(".", $filename);
        if (!is_array($nameAry)) return false;
        return array_pop($nameAry);
    }

    /**
     * getFileNameNoExtension
     *
     * @param $filename
     * @return false|string
     */
    public static function getFileNameNoExtension($filename)
    {
        $nameAry = explode(".", $filename);
        if (!is_array($nameAry)) return false;
        array_pop($nameAry);

        return implode('.', $nameAry);
    }

    /**
     * マルチバイト対応 basename
     */
    public static function mb_basename($str, $suffix = null)
    {
        $tmp = preg_split('/[\/\\\\]/', $str);
        $res = end($tmp);
        if ($suffix !== null && strlen($suffix)) {
            $suffix = preg_quote($suffix);
            $res = preg_replace("/({$suffix})$/u", "", $res);
        }
        return $res;
    }

    /**
     * baserCMS標準のbase64UrlsafeEncodeが連続ドットのファイル名を禁止した特定サーバで
     * 動作しないため独自定義
     *
     * @param string $str
     * @return string
     */
    public static function b64e($str)
    {
        $str = base64_encode($str);
        $ret = str_replace("..", "-D-", str_replace(['+', '/', '='], ['_', '-', '.'], $str));

        //末尾のドットをエンコード
        if (mb_substr($ret, -1) === ".") {
            return str_replace(".", "-d-", $ret);
        } else {
            return $ret;
        }
    }

    /**
     * baserCMS標準のbase64UrlsafeEncodeが連続ドットのファイル名を禁止した特定サーバで
     * 動作しないため独自定義のdecode版
     * @param string $str
     * @return string
     */
    public static function b64d($str)
    {
        $str = str_replace("-d-", ".", $str);
        $str = str_replace(['_', '-', '.'], ['+', '/', '='], str_replace("-D-", "..", $str));
        return base64_decode($str);
    }
}
