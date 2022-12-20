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

namespace BcMail\Model\Entity;

use BaserCore\Model\Entity\Content;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class MailContent
 * @property int $id
 * @property string $description
 * @property string $sender_1
 * @property string $sender_2
 * @property string $sender_name
 * @property string $subject_user
 * @property string $subject_admin
 * @property string $form_template
 * @property string $mail_template
 * @property string $redirect_url
 * @property int $widget_area
 * @property bool $ssl_on
 * @property bool $save_info
 * @property FrozenTime $publish_begin
 * @property FrozenTime $publish_end
 * @property FrozenTime $created
 * @property FrozenTime $modified
 * @property Content $content
 */
class MailContent extends Entity
{

    /**
     * Accessible
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

}
