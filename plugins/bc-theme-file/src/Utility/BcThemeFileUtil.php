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

namespace BcThemeFile\Utility;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcThemeFileUtil
 */
class BcThemeFileUtil
{

    /**
     * テンプレートタイプの名称を取得する
     *
     * @param string $type
     * @return false|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getTemplateTypeName(string $type)
    {
        $templateTypes = [
            'layout' => __d('baser_core', 'レイアウトテンプレート'),
            'element' => __d('baser_core', 'エレメントテンプレート'),
            'email' => __d('baser_core', 'Eメールテンプレート'),
            'etc' => __d('baser_core', 'コンテンツテンプレート'),
            'css' => __d('baser_core', 'スタイルシート'),
            'js' => 'Javascript',
            'img' => __d('baser_core', 'イメージ')
        ];
        if (isset($templateTypes[$type])) {
            return $templateTypes[$type];
        } else {
            return false;
        }
    }

    /**
     * パスを正規化する
     *
     * 実在しないパスでも `..` / `.` / 重複区切りを解決した絶対パスを返す。
     * realpath() が false を返す（未作成のパス）場合の境界チェックに用いる。
     *
     * @param string $path
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function normalizePath(string $path): string
    {
        $parts = [];
        foreach (explode(DS, str_replace(['/', '\\'], DS, $path)) as $part) {
            if ($part === '..') {
                array_pop($parts);
            } elseif ($part !== '' && $part !== '.') {
                $parts[] = $part;
            }
        }
        return DS . implode(DS, $parts);
    }

    /**
     * パスが基準ディレクトリ配下に収まっているかを判定する
     *
     * パストラバーサル対策の共通判定。実在すれば realpath()、未作成なら normalizePath() で
     * `..` を解決したうえで、基準ディレクトリを前方一致で比較する。
     * 基準ディレクトリ自身（$path === $baseDir）は配下とみなす。
     *
     * @param string $path 検証対象のパス
     * @param string $baseDir 基準ディレクトリ
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isWithinDir(string $path, string $baseDir): bool
    {
        $resolved = realpath($path);
        if ($resolved === false) {
            $resolved = self::normalizePath($path);
        }
        $resolvedBase = realpath($baseDir);
        if ($resolvedBase === false) {
            $resolvedBase = self::normalizePath($baseDir);
        }
        return str_starts_with(rtrim($resolved, DS) . DS, rtrim($resolvedBase, DS) . DS);
    }

}
