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

use Cake\Log\Log;
use Psr\Log\LogLevel;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcUpdateLog
 */
class BcUpdateLog
{

    /**
     * アップデートメッセージ
     * @var array
     * @checked
     * @unitTest
     * @noTodo
     */
    private static $message = [];

    /**
     * アップデートメッセージを一時領域にセットする
     * @param $message
     * @checked
     * @unitTest
     * @noTodo
     */
    public static function set($message)
    {
        self::$message[] = $message;
    }

    /**
     * 一時領域のアップデートメッセージを取得する
     * @return array
     * @checked
     * @unitTest
     * @noTodo
     */
    public static function get()
    {
        return self::$message;
    }

    /**
     * アップデートメッセージを保存する
     * @checked
     * @unitTest
     * @noTodo
     */
    public static function save()
    {
        if(self::$message) {
            foreach(self::$message as $value) {
                Log::write(LogLevel::INFO, $value, 'update');
            }
            self::clear();
        }
    }

    /**
     * 一時領域のアップデートメッセージを削除する
     */
    public static function clear()
    {
        self::$message = [];
    }

}
