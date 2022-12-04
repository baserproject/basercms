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

use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class MailField
 * @property int $id
 * @property int $mail_content_id
 * @property int $no
 * @property string $name
 * @property string $field_name
 * @property string $type
 * @property string $head
 * @property string $attention
 * @property string $before_attachment
 * @property string $after_attachment
 * @property string $source
 * @property int $size
 * @property int $text_rows
 * @property int $maxlength
 * @property string $options
 * @property string $class
 * @property string $delimiter
 * @property string $default_value
 * @property string $description
 * @property string $group_field
 * @property string $group_valid
 * @property string $valid
 * @property string $valid_ex
 * @property string $auto_convert
 * @property bool $not_empty
 * @property bool $use_field
 * @property bool $no_send
 * @property int $sort
 * @property FrozenTime $created
 * @property FrozenTime $modified
 */
class MailField extends Entity
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
