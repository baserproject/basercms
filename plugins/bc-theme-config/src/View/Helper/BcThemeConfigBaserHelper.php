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

namespace BcThemeConfig\View\Helper;

use Cake\View\Helper;

/**
 * BcThemeConfigBaserHelper
 *
 * @property BcThemeConfigHelper $BcThemeConfig
 */
class BcThemeConfigBaserHelper extends Helper
{

    /**
     * Helper
     *
     * @var string[]
     */
    public $helpers = ['BcThemeConfig.BcThemeConfig'];

    /**
     * メインイメージを出力する
     *
     * @param array $options
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストは実装しない
     */
    public function logo($options = [])
    {
        $this->BcThemeConfig->logo($options);
    }

    /**
     * ロゴを出力する
     *
     * @param array $options
     * @checked
     * @noTodo
     * @unitTest ラッパーメソッドのためユニットテストは実装しない
     */
    public function mainImage($options = [])
    {
        $this->BcThemeConfig->mainImage($options);
    }

}
