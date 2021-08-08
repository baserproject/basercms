<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Utility;

/**
 * Class BcAgent
 *
 * @package Baser.Lib
 */
class BcAgent extends BcAbstractDetector
{

    /**
     * 検出器タイプ
     *
     * @var string
     */
    public $type = 'device';

    /**
     * 設定ファイルのキー名
     *
     * @var string
     */
    protected static $_configName = 'BcAgent';

    /**
     * セッションIDを付与するかどうか
     * @var bool
     */
    public $sessionId;

    /**
     * 設定
     *
     * @param array $config 設定の配列
     * @return void
     */
    protected function _setConfig(array $config)
    {
        $this->decisionKeys = $config['agents'];
        $this->sessionId = $config['sessionId'];
    }

    /**
     * デフォルトの設定値を取得
     *
     * @return array
     */
    protected function _getDefaultConfig()
    {
        return [
            'agents' => [],
            'sessionId' => false
        ];
    }

    /**
     * ユーザーエージェントの判定用正規表現を取得
     *
     * @return string
     */
    public function getDetectorRegex()
    {
        $regex = '/' . str_replace('\|\|', '|', preg_quote(implode('||', $this->decisionKeys), '/')) . '/i';
        return $regex;
    }

    /**
     * ユーザーエージェントがキーワードを含むかどうかを判定
     *
     * @return bool
     */
    public function isMatchDecisionKey()
    {
        $key = env('HTTP_USER_AGENT');
        $regex = $this->getDetectorRegex();
        return (bool)preg_match($regex, $key);
    }

}
