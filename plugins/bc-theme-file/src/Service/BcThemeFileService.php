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
use Cake\Core\Plugin;

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
     */
    public function getFullpath(string $theme, string $type, string $path)
    {
        $assets = [
            'css',
            'js',
            'img'
        ];
        if (in_array($type, $assets)) {
            $viewPath = Plugin::path($theme) . 'webroot' . DS . $type . DS . $path;
        } else {
            $viewPath = Plugin::templatePath($theme) . $type . DS . $path;
        }
        return $viewPath;
    }
}
