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

namespace BcMail\View\Helper;

use BaserCore\View\Helper\BcPluginBaserHelperInterface;
use Cake\View\Helper;

/**
 * MailBaserHelper
 *
 * BcBaserHelper より透過的に呼び出されるヘルパー
 */
class BcMailBaserHelper extends Helper implements BcPluginBaserHelperInterface
{

    /**
     * ヘルパー
     * @var array
     */
    public $helpers = [
        'BcMail.Mail',
        'BcMail.Mailform'
    ];

    /**
     * メソッド一覧取得
     *
     * @return array[]
     */
    public function methods(): array
    {
        return [
            'isMail' => ['Mail', 'isMail'],
            'mailFormDescriptionExists' => ['Mail', 'descriptionExists'],
            'mailFormDescription' => ['Mail', 'description'],
            'freezeMailForm' => ['Mailform' => 'freeze']
        ];
    }

}
