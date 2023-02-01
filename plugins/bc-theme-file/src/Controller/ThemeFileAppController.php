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

namespace BcThemeFile\Controller;

use App\Controller\AppController;
use BaserCore\Utility\BcUtil;
use BcThemeFile\Service\Admin\ThemeFilesAdminServiceInterface;
use BcThemeFile\Service\Admin\ThemeFoldersAdminServiceInterface;
use BcThemeFile\Service\ThemeFilesServiceInterface;
use BcThemeFile\Service\ThemeFoldersServiceInterface;
use BcThemeFile\Utility\BcThemeFileUtil;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class ThemeFileAppController
 *
 */
class ThemeFileAppController extends AppController
{

    /**
     * 引き数を解析する
     *
     * @param array $args
     * @return array
     * @checked
     * @noTodo
     */
    protected function parseArgs($args)
    {
        $data = [
            'plugin' => '',
            'theme' => '',
            'type' => '',
            'path' => '',
            'fullpath' => '',
            'assets' => false
        ];
        $assets = [
            'css',
            'js',
            'img'
        ];

        if ($args[0] instanceof ThemeFilesAdminServiceInterface ||
            $args[0] instanceof ThemeFoldersAdminServiceInterface ||
            $args[0] instanceof ThemeFilesServiceInterface ||
            $args[0] instanceof ThemeFoldersServiceInterface
        ) {
            unset($args[0]);
            $args = array_merge($args);
        }
        if (!empty($args[1]) && !BcThemeFileUtil::getTemplateTypeName($args[1])) {
            $folder = new Folder(BASER_PLUGINS);
            $files = $folder->read(true, true);
            foreach ($files[0] as $file) {
                if ($args[1] !== Inflector::camelize($file, '-')) continue;
                $data['plugin'] = $args[1];
                unset($args[1]);
                break;
            }
        }

        if ($data['plugin']) {
            if (!empty($args[0])) {
                $data['theme'] = $args[0];
                unset($args[0]);
            }
            if (!empty($args[2])) {
                $data['type'] = $args[2];
                unset($args[2]);
            }
        } else {
            if (!empty($args[0])) {
                $data['theme'] = $args[0];
                unset($args[0]);
            }
            if (!empty($args[1])) {
                $data['type'] = $args[1];
                unset($args[1]);
            }
        }

        if (empty($data['type'])) $data['type'] = 'layout';
        if (!empty($args)) $data['path'] = rawurldecode(implode(DS, $args));

        if ($data['plugin']) {
            if (in_array($data['type'], $assets)) {
                $data['assets'] = true;
                $viewPath = BcUtil::getExistsWebrootDir($data['plugin'], '', 'front');
            } else {
                $viewPath = BcUtil::getExistsTemplateDir($data['plugin'], '', 'front');
            }
            if (!$viewPath) {
                if (in_array($data['type'], $assets)) {
                    $viewPath = Plugin::path($data['theme']) . 'webroot' . DS . Inflector::underscore($data['plugin']) . DS;
                } else {
                    $viewPath = Plugin::templatePath($data['theme']) . 'plugin' . DS . $data['plugin'] . DS;
                }
            }
        } else {
            if (in_array($data['type'], $assets)) {
                $viewPath = Plugin::path($data['theme']) . 'webroot' . DS;
            } else {
                $viewPath = Plugin::templatePath($data['theme']);
            }
        }

        if ($data['type'] !== 'etc') {
            $data['fullpath'] = $viewPath . $data['type'] . DS . $data['path'];
        } else {
            $data['fullpath'] = $viewPath . $data['path'];
        }

        if ($data['path'] && is_dir($data['fullpath']) && !preg_match('/\/$/', $data['fullpath'])) {
            $data['fullpath'] .= DS;
        }
        return $data;
    }

    /**
     *  APIのパラメーターを変換する処理
     *
     * @param array $args
     * @return array
     * @checked
     * @noTodo
     */
    protected function convertApiDataToArgs($postData)
    {
        return [
            $postData['theme'],
            $postData['type'],
            $postData['path']
        ];
    }
}
