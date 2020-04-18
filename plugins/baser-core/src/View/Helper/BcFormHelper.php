<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

namespace BaserCore\View\Helper;
use \Cake\View\Helper;
use \Cake\Core\Configure;
use \Cake\View\Helper\FormHelper;
use BaserCore\View\Helper\BcHtmlHelper;
use BaserCore\View\Helper\BcTimeHelper;
use BaserCore\View\Helper\BcTextHelper;
use BaserCore\View\Helper\BcCkeditorHelper;
use BaserCore\View\Helper\BcUploadHelper;

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
}
