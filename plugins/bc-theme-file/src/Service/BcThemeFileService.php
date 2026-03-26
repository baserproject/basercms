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

        $resolvedBase = realpath($baseDir);
        if ($resolvedBase !== false) {
            $resolvedBase = rtrim($resolvedBase, DS) . DS;
            $targetDir = realpath(dirname($fullPath));
            if ($targetDir === false) {
                $targetDir = $this->normalizePath(dirname($fullPath));
            }
            if (!str_starts_with(rtrim($targetDir, DS) . DS, $resolvedBase)) {
                throw new BcException(__d('baser_core', 'パスにテーマディレクトリ外への参照が含まれています。'));
            }
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
}
