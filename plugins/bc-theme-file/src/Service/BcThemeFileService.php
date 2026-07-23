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

namespace BcThemeFile\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BaserCore\Utility\BcUtil;
use BcThemeFile\Utility\BcThemeFileUtil;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Utility\Inflector;

/**
 * BcThemeFileService
 *
 */
class BcThemeFileService implements BcThemeFileServiceInterface
{
    /**
     * fullpathを作成
     * @param string $theme
     * @param string $type
     * @param string $path
     * @return string
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getFullpath(string $theme, string $plugin, string $type, string $path)
    {
        // パストラバーサル対策(GHSA-2pj4-v76f-wjvx):
        // $type を許可リスト(BcThemeFileUtil::getTemplateTypeName)で検証する。
        // $type が未検証だと $baseDir = $viewPath . $type . DS のベースディレクトリ自体を
        // 攻撃者入力で移動でき（例: $type = '../../../../tmp'）、後続の境界チェックが
        // 「移動後のベース配下」に収まってしまい無効化される。
        if (!BcThemeFileUtil::getTemplateTypeName($type)) {
            throw new BcException(__d('baser_core', 'テンプレートタイプが不正です。'));
        }

        $assets = [
            'css',
            'js',
            'img'
        ];

        if ($plugin) {
            if (in_array($type, $assets)) {
                $viewPath = BcUtil::getExistsWebrootDir($theme, $plugin, '', 'front');
            } else {
                $viewPath = BcUtil::getExistsTemplateDir($theme, $plugin, '', 'front');
            }
            if(!$viewPath) {
                if (in_array($type, $assets)) {
                    $viewPath = Plugin::path($theme) . 'webroot' . DS . Inflector::underscore($plugin) . DS;
                } else {
                    $viewPath = Plugin::templatePath($theme) . 'plugin' . DS . $plugin . DS;
                }
            }
        } else {
            if (in_array($type, $assets)) {
                $viewPath = Plugin::path($theme) . 'webroot' . DS;
            } else {
                $viewPath = Plugin::templatePath($theme);
            }
        }
        $baseDir = $viewPath . $type . DS;
        $fullPath = $baseDir . $path;

        // パストラバーサル対策:
        // ベースディレクトリがディスク上に存在せず realpath() が false を返す場合でも
        // 検証をスキップせず、必ず正規化したパスで境界チェックを行う。
        $resolvedBase = realpath($baseDir);
        if ($resolvedBase === false) {
            $resolvedBase = $this->normalizePath($baseDir);
        }
        $resolvedBase = rtrim($resolvedBase, DS) . DS;
        // $fullPath は作成先の親ディレクトリ（$path が空の場合は baseDir 自身）を指すため、
        // dirname() ではなく $fullPath 自身がテーマディレクトリ内に収まっているかを検証する
        $targetDir = realpath($fullPath);
        if ($targetDir === false) {
            $targetDir = $this->normalizePath($fullPath);
        }
        if (!str_starts_with(rtrim($targetDir, DS) . DS, $resolvedBase)) {
            throw new BcException(__d('baser_core', 'パスにテーマディレクトリ外への参照が含まれています。'));
        }

        return $fullPath;
    }

    /**
     * パスを正規化する（../ を解決する）
     * @param string $path
     * @return string
     */
    private function normalizePath(string $path): string
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
     * 指定したパスがテーマ（プラグイン）ディレクトリ配下に収まっているか検証する
     *
     * パストラバーサル対策(GHSA-2pj4-v76f-wjvx)の最終防衛線。
     * Admin web 経路の create()/update() はリクエストの fullpath を直接書き込み先に使い
     * getFullpath() の境界チェックを通らないため、実際の書き込み sink でも
     * 「テーマ配置ルート(App::path('plugins'))配下」であることを必ず検証する。
     * 配下でなければ BcException を投げる。
     *
     * @param string $path 検証対象のパス（ファイル or ディレクトリ）
     * @return void
     * @throws BcException
     * @checked
     * @noTodo
     */
    protected function assertWithinThemeDir(string $path): void
    {
        // 実在すれば realpath、未作成なら正規化で .. を解決する
        $resolved = realpath($path);
        if ($resolved === false) {
            $resolved = $this->normalizePath($path);
        }
        $resolved = rtrim($resolved, DS) . DS;

        foreach (App::path('plugins') as $root) {
            $realRoot = realpath($root);
            if ($realRoot === false) {
                $realRoot = $this->normalizePath($root);
            }
            $realRoot = rtrim($realRoot, DS) . DS;
            if (str_starts_with($resolved, $realRoot)) {
                return;
            }
        }
        throw new BcException(__d('baser_core', 'パスにテーマディレクトリ外への参照が含まれています。'));
    }
}
