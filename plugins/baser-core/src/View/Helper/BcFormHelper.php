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

namespace BaserCore\View\Helper;
use \Cake\View\Helper\FormHelper;

/**
 * FormHelper 拡張クラス
 *
 * @package Baser.View.Helper
 */
class BcFormHelper extends FormHelper
{
    /**
     * Other helpers used by FormHelper
     *
     * @var array
     */
    public $helpers = ['Url', 'Html', 'BcTime', 'BcText', 'Js', 'BcUpload', 'BcCkeditor'];

    public function dispatchAfterForm($type = '') {

    }

    /**
     * widget
     * @param string $name
     * @param array $data
     * @return string
     */
    public function widget(string $name, array $data = []): string
    {
        return parent::widget($name, $data);
    }
}
