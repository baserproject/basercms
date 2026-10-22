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

namespace BcCustomContent\Utility;

use Cake\Core\Configure;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomContentUtil
 */
class CustomContentUtil
{

    /**
     * カスタムコンテンツ用プラグインの設定値を取得する
     *
     * @param $plugin
     * @param string $name
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getPluginSetting(string $plugin, string $name = '')
    {
        if ($name) {
            return Configure::read("BcCustomContent.fieldTypes.$plugin.$name");
        } else {
            return Configure::read("BcCustomContent.fieldTypes.$plugin");
        }
    }

    /**
     * アップロードを常に拒否する拡張子の一覧を取得する
     *
     * @return array 小文字の拡張子
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getDeniedFileExts(): array
    {
        $config = Configure::read('BcCustomContent.deniedExtension');
        if (is_string($config)) {
            $config = explode(',', $config);
        }
        if (!is_array($config)) {
            return [];
        }
        $exts = [];
        foreach($config as $ext) {
            if (!is_string($ext)) continue;
            $ext = strtolower(trim($ext));
            if ($ext === '') continue;
            $exts[] = $ext;
        }
        return $exts;
    }

    /**
     * 拡張子の一覧から、常に拒否する拡張子を取り除く
     *
     * @param array $exts
     * @return array 小文字の拡張子
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function filterDeniedFileExts(array $exts): array
    {
        $denied = self::getDeniedFileExts();
        $result = [];
        foreach($exts as $ext) {
            if (!is_string($ext)) continue;
            $ext = strtolower(trim($ext));
            if ($ext === '' || in_array($ext, $denied, true)) continue;
            $result[] = $ext;
        }
        return $result;
    }

}
