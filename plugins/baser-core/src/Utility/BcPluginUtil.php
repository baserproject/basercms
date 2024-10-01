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

namespace BaserCore\Utility;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcPluginUtil
 */
class BcPluginUtil
{

    /**
     * プラグインのconfig内容を取得する
     * @param string $name
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getPluginConfig(string $name): array
    {
        $path = BcUtil::getPluginPath($name) . 'config.php';
        $config = [
            'type' => [],
            'title' => '',
            'description' => '',
            'author' => '',
            'url' => '',
            'adminLink' => '',
            'installMessage' => '',
        ];
        if (file_exists($path)) {
        	$config = array_merge($config, include $path);
        	if(!is_array($config['type'])) {
        	    if(strpos($config['type'], ',') !== false) {
        	        $config['type'] = array_map(function($value){
                        return trim($value);
                    }, explode(',', $config['type']));
        	    } else {
                    $config['type'] = [$config['type']];
                }
        	}
        }
        return $config;
    }

    /**
     * プラグインかどうかを判定する
     * @param string $name
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isPlugin(string $name): bool
    {
        if($name === 'BaserCore') return true;
        $config = self::getPluginConfig($name);
        if(in_array('Plugin', $config['type'])) return true;
        if(in_array('CorePlugin', $config['type'])) return true;
        return false;
    }

    /**
     * テーマかどうかを判定する
     * @param string $name
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isTheme(string $name): bool
    {
        $config = self::getPluginConfig($name);
        if(in_array('Theme', $config['type'])) return true;
        if(in_array('AdminTheme', $config['type'])) return true;
        return false;
    }

}
