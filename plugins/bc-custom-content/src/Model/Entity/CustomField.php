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

namespace BcCustomContent\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomField
 *
 * @property string $title
 * @property string $name
 * @property string $type
 * @property bool $status
 * @property bool $group_valid
 * @property string $default_value
 * @property array $validate
 * @property string $regex
 * @property string $regex_error_message
 * @property bool $counter
 * @property string $auto_convert
 * @property string $placeholder
 * @property int $size
 * @property int $line
 * @property int $max_length
 * @property string $source
 * @property array $meta
 */
class CustomField extends Entity
{

    /**
     * フィールドタイプの表示名を取得する
     *
     * @return string
     */
    public function getTypeTitle(): string
    {
        $types = Configure::read('BcCustomContent.fieldTypes');
        foreach($types as $key => $type){
            if($this->type === $key) return $type['label'];
        }
        return '';
    }

}
