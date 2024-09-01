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

use Cake\Core\Configure;
use Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcComposer
 */
class BcComposer
{

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
     * 現在のディレクトリ
     *
     * @var string
     */
    public static $currentDir;

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
     * @checked
     * @noTodo
     */
    public static function setup(string $php = '', $dir = '')
    {
        self::checkEnv();
        if(!preg_match('/\/$/', $dir)) {
            $dir .= '/';
        }
        self::$currentDir = $dir;
        self::$cd = ($dir)? "cd " . $dir . ';': "cd " . ROOT . DS . ';';
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function checkComposer()
    {
        if (!file_exists(self::$composerDir . 'composer.phar')) {
            $result = self::installComposer();
            if (!file_exists(self::$composerDir . 'composer.phar')) {
                throw new Exception(__d('baser_core', 'composer がインストールできません。{0}', implode("\n", $result['out'])));
            }
            self::selfUpdate();
        }
    }

    /**
     * 環境チェック
     *
     * @throws Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function checkEnv()
    {
        $error = [];
        if (!is_writable(ROOT . DS . 'composer')) {
            $error[] = __d('baser_core', '/composer に書き込み権限がありません。書き込み権限を与えてください。');
        }
        if (!is_writable(ROOT . DS . 'vendor')) {
            $error[] = __d('baser_core', '/vendor に書き込み権限がありません。書き込み権限を与えてください。');
        }
        if (!is_writable(ROOT . DS . 'config')) {
            $error[] = __d('baser_core', '/config に書き込み権限がありません。書き込み権限を与えてください。');
        }
        if (!is_writable(ROOT . DS . 'tmp')) {
            $error[] = __d('baser_core', '/tmp に書き込み権限がありません。書き込み権限を与えてください。');
        }
        if (!is_writable(ROOT . DS . 'logs')) {
            $error = __d('baser_core', '/logs に書き込み権限がありません。書き込み権限を与えてください。');
        }
        if ($error) {
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
        $command = 'cd ' . self::$composerDir . '; ' . self::$export . ' curl -sS https://getcomposer.org/installer' . ' | ' . self::$php . ' 2>&1';
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
     */
    public static function require(string $package, string $version)
    {
        if(strpos($package, '/') === false) {
            $package = 'baserproject/' . $package;
        }
        return self::execCommand("require {$package}:{$version} --with-all-dependencies --ignore-platform-req=ext-xdebug");
    }

    /**
     * composer update 実行
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function update()
    {
        return self::execCommand('update --with-all-dependencies --ignore-platform-req=ext-xdebug');
    }

    /**
     * composer install 実行
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function install()
    {
        return self::execCommand('install --ignore-platform-req=ext-xdebug');
    }

    /**
     * composer self-update 実行
     *
     * @return array
     * @checked
     * @noTodo
     */
    public static function selfUpdate()
    {
        return self::execCommand('self-update');
    }

    /**
     * キャッシュをクリアする
     * @return array
     */
    public static function clearCache()
    {
        return self::execCommand('clear-cache');
    }

    /**
     * コマンド実行
     *
     * @param string $command
     * @return array
     * @checked
     * @noTodo
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function createCommand(string $command)
    {
        return self::$cd . ' ' . self::$export . ' echo y | ' . self::$php . ' ' . self::$composerDir . 'composer.phar ' . $command . ' 2>&1';
    }

    /**
     * 配布用に composer.json をセットアップする
     * @param string $version
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public static function setupComposerForDistribution(string $version)
    {
        self::deleteReplace();
        $result = self::require('baser-core', $version);
        (new BcFolder(self::$currentDir . 'vendor'))->delete();
        mkdir(self::$currentDir . 'vendor');
        (new BcFile(self::$currentDir . 'vendor' . DS . '.gitkeep'))->create();
        return $result;
    }

    /**
     * changeMinimumStabilityToDev
     *
     * @return void
     * @checked
     * @noTodo
     */
    public static function changeMinimumStabilityToDev()
    {
        $file = new BcFile(self::$currentDir . 'composer.json');
        $json = $file->read();

        if(strpos($json, '"minimum-stability"') !== false) {
            $json = preg_replace('/"minimum-stability"\s*:\s*".+?"/', '"minimum-stability": "dev"', $json);
        } else {
            $json = preg_replace('/"require"\s*:\s*{/', '"minimum-stability": "dev",' . "\n" . '    "require": {', $json);
        }
        if(strpos($json, '"prefer-stable"') !== false) {
            $json = preg_replace('/"prefer-stable"\s*:\s*[a-zA-Z]+/', '"prefer-stable": true', $json);
        } else {
            $json = preg_replace('/"require"\s*:\s*{/', '"prefer-stable": true,' . "\n" . '    "require": {', $json);
        }

        $file->write($json);
    }

    /**
     * replace を削除する
     * @return void
     */
    public static function deleteReplace()
    {
        $file = new BcFile(self::$currentDir . 'composer.json');
        $json = $file->read();
        $json = preg_replace('/"replace"\s*:\s*?{[^}]+?},/', '', $json);
        $file->write($json);
    }

}
