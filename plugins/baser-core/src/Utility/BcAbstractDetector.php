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
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcAbstractDetector
 */
abstract class BcAbstractDetector
{

    /**
     * 検出器タイプ
     *
     * @var string
     */
    public $type;

    /**
     * 名前
     * @var string
     */
    public $name;

    /**
     * 判定キーワード
     * @var array
     */
    public $decisionKeys;

    /**
     * 検出器リスト
     *
     * @var array
     */
    protected static $_detectors = null;

    /**
     * 設定ファイルのキー名
     *
     * @var string
     */
    protected static $_configName;

    /**
     * コンストラクタ
     *
     * @param string $name 名前
     * @param array $config 設定の配列
     * @checked
     * @noTodo
     */
    public function __construct($name, array $config)
    {
        $this->name = $name;
        $config = array_merge($this->_getDefaultConfig(), $config);
        $this->_setConfig($config);
    }

    /**
     * 名前をキーとしてインスタンスを探す
     *
     * @param string $name 名前
     * @return BcAbstractDetector|null
     * @checked
     * @noTodo
     */
    public static function find($name)
    {
        $key = static::$_configName . ".{$name}";
        if (!Configure::check($key)) {
            return null;
        }
        return new static($name, Configure::read($key));
    }

    /**
     * 設定ファイルに存在する全てのインスタンスを返す
     *
     * @return BcAbstractDetector[]
     * @checked
     * @noTodo
     */
    public static function findAll()
    {
        if (!BcUtil::isInstalled()) {
            return [];
        }
        if (!empty(static::$_detectors[static::$_configName])) {
            return static::$_detectors[static::$_configName];
        }
        $configs = Configure::read(static::$_configName);
        static::$_detectors[static::$_configName] = [];
        foreach($configs as $name => $config) {
            static::$_detectors[static::$_configName][] = new static($name, $config);
        }
        return static::$_detectors[static::$_configName];
    }

    /**
     * 現在の環境の判定キーの値に合致するインスタンスを返す
     *
     * @return BcAbstractDetector|null
     * @checked
     * @noTodo
     */
    public static function findCurrent()
    {
        $detectors = static::findAll();
        foreach($detectors as $detector) {
            if ($detector->isMatchDecisionKey()) {
                return $detector;
            }
        }
        return null;
    }

    /**
     * デフォルトの設定値を取得
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    abstract protected function _getDefaultConfig();

    /**
     * 設定
     *
     * @param array $config 設定の配列
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    abstract protected function _setConfig(array $config);

    /**
     * キーがキーワードを含むかどうかを判定
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    abstract public function isMatchDecisionKey();

}
