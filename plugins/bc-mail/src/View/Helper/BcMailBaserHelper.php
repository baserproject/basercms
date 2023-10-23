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
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

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
     * @checked
     * @noTodo
     */
    public function methods(): array
    {
        return [
            'isMail' => ['Mail', 'isMail'],
            'mailFormDescriptionExists' => ['Mail', 'descriptionExists'],
            'mailFormDescription' => ['Mail', 'description'],
            'freezeMailForm' => ['Mailform', 'freeze'],
            'createMailForm' => ['Mailform', 'create'],
            'mailFormHidden' => ['Mailform', 'hidden'],
            'mailFormAuthCaptcha' => ['Mailform', 'authCaptcha'],
            'mailFormSubmit' => ['Mailform', 'submit'],
            'endMailForm' => ['Mailform', 'end'],
            'unlockMailFormField' => ['Mailform', 'unlockField'],
            'getMailFormSourceValue' => ['Mailform', 'getSourceValue'],
            'mailFormError' => ['Mailform', 'error'],
            'mailFormControl' => ['Mailform', 'control'],
            'getMailFormGroupValidErrors' => ['Mailform', 'getGroupValidErrors'],
            'isMailFormGroupLastField' => ['Mailform', 'isGroupLastField'],
            'mailFormLabel' => ['Mailform', 'label'],
        ];
    }

}
