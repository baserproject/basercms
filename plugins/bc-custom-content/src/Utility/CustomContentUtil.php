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
     */
    public static function getPluginSetting(string $plugin, string $name = '')
    {
        if($name) {
            return Configure::read("BcCustomContent.fieldTypes.$plugin.$name");
        } else {
            return Configure::read("BcCustomContent.fieldTypes.$plugin");
        }
    }

}
