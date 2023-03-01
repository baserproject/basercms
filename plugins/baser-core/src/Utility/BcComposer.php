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

use Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcComposer
 */
class BcComposer {

    /**
     * cd コマンド
     *
     * @var string
     */
    public static $cd;

    /**
     * Composer Dir
     *
     * @var string
     */
    public static $composerDir;

    /**
     * export コマンド
     *
     * @var string
     */
    public static $export;

    /**
     * php パス
     *
     * @var string
     */
    public static $php = 'php';

    /**
     * Setup
     *
     * @param string $php
     * @throws Exception
     */
    public static function setup(string $php = '')
    {
        self::checkEnv();
        self::$cd = "cd " . ROOT . DS . ';';
        self::$composerDir = ROOT . DS . 'composer' . DS;
        self::$export = "export HOME=" . self::$composerDir . ";";
        self::$php = ($php)?: 'php';
        try {
            self::checkComposer();
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * Composer がインストールされているかチェックする
     *
     * @throws Exception
     */
    public static function checkComposer()
    {
        if(!file_exists(self::$composerDir . 'composer.phar')) {
            $result = self::installComposer();
            if(!file_exists(self::$composerDir . 'composer.phar')) {
                throw new Exception(__d('baser', 'composer がインストールできません。{0}', implode("\n", $result['out'])));
            }
            self::selfUpdate();
        }
    }

    /**
     * 環境チェック
     *
     * @throws Exception
     */
    public static function checkEnv()
    {
        $error = [];
        if (!is_writable(ROOT . DS . 'composer')) {
            $error[] = __d('baser', '/composer に書き込み権限がありません。書き込み権限を与えてください。');
        }
        if (!is_writable(ROOT . DS . 'vendor')) {
            $error[] = __d('baser', '/vendor に書き込み権限がありません。書き込み権限を与えてください。');
        }
        if (!is_writable(ROOT . DS . 'config')) {
            $error[] = __d('baser', '/config に書き込み権限がありません。書き込み権限を与えてください。');
        }
        if (!is_writable(ROOT . DS . 'tmp')) {
            $error[] = __d('baser', '/tmp に書き込み権限がありません。書き込み権限を与えてください。');
        }
        if (!is_writable(ROOT . DS . 'logs')) {
            $error = __d('baser', '/logs に書き込み権限がありません。書き込み権限を与えてください。');
        }
        if($error) {
            throw new Exception(implode('\n', $error));
        }
    }

    /**
     * Composer のインストール
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function installComposer()
    {
        $command = 'cd ' . self::$composerDir . '; ' . self::$export . ' curl -sS https://getcomposer.org/installer' . ' | ' . self::$php;
        exec($command, $out, $code);
        return [
            'out' => $out,
            'code' => $code
        ];
    }

    /**
     * composer require 実行
     *
     * @param string $package
     * @param string $version
     * @return array
     * @checked
     * @noTodo
     * @checked
     */
    public static function require(string $package, string $version)
    {
        return self::execCommand("require baserproject/{$package}:{$version} --with-all-dependencies 2>&1");
    }

    /**
     * composer install 実行
     *
     * @return array
     */
    public static function install()
    {
        return self::execCommand('install');
    }

    /**
     * composer self-update 実行
     *
     * @return array
     */
    public static function selfUpdate()
    {
        return self::execCommand('self-update');
    }

    /**
     * コマンド実行
     *
     * @param string $command
     * @return array
     */
    public static function execCommand(string $command)
    {
        $command = self::createCommand($command);
        exec($command, $out, $code);
        return [
            'out' => $out,
            'code' => $code
        ];
    }

    /**
     * コマンド作成
     *
     * @param string $command
     * @return string
     */
    public static function createCommand(string $command)
    {
        return  self::$cd . ' ' . self::$export . ' ' . self::$php . ' ' . self::$composerDir . 'composer.phar ' . $command;
    }

}
