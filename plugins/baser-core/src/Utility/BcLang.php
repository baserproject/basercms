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
 * Class BcLang
 *
 */
class BcLang extends BcAbstractDetector
{

    /**
     * 検出器タイプ
     *
     * @var string
     */
    public $type = 'lang';

    /**
     * 設定ファイルのキー名
     *
     * @var string
     */
    protected static $_configName = 'BcLang';

    /**
     * 設定
     *
     * @param array $config 設定の配列
     * @return void
     * @checked
     * @noTodo
     */
    protected function _setConfig(array $config)
    {
        $this->decisionKeys = $config['langs'];
    }

    /**
     * デフォルトの設定値を取得
     *
     * @return array
     * @checked
     * @noTodo
     */
    protected function _getDefaultConfig()
    {
        return [
            'langs' => []
        ];
    }

    /**
     * 判定用正規表現を取得
     *
     * @return string
     * @checked
     * @noTodo
     */
    public function getDetectorRegex()
    {
        $regex = '/' . str_replace('\|\|', '|', preg_quote(implode('||', $this->decisionKeys), '/')) . '/i';
        return $regex;
    }

    /**
     * キーワードを含むかどうかを判定
     *
     * @return bool
     * @checked
     * @noTodo
     */
    public function isMatchDecisionKey()
    {
        $key = self::parseLang((isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null);
        $regex = $this->getDetectorRegex();
        return (bool)preg_match($regex, $key);
    }

    /**
     * 言語データを解析する
     *
     * 最優先の言語のみ対応
     *
     * @param $acceptLanguage
     * @return array|string
     * @checked
     * @noTodo
     */
    static public function parseLang($acceptLanguage)
    {
        if (!$acceptLanguage) {
            return 'ja';
        }
        $keys = explode(',', $acceptLanguage);
        $langs = [];
        if ($keys) {
            foreach($keys as $key) {
                [$lang] = explode(';', $key);
                $lang = preg_replace('/-.*$/', '', $lang);
                if (!in_array($lang, $langs)) {
                    $langs[] = $lang;
                }
            }
        }
        if (!$langs) {
            $langs = ['ja'];
        }
        return $langs[0];
    }
    
}
