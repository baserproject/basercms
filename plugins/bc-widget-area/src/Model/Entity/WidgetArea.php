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

namespace BcWidgetArea\Model\Entity;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcUtil;
use Cake\I18n\FrozenTime;

/**
 * WidgetArea
 *
 * @property int $id
 * @property string $name
 * @property array $widgets
 * @property FrozenTime $created
 * @property FrozenTime $modified
 */
class WidgetArea extends \Cake\ORM\Entity {

    /**
     * Accessible
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * ウィジェットを取り出す
     * @return array|mixed
     */
    protected function _getWidgets()
    {
        if(!empty($this->_fields['widgets'])) {
            return BcUtil::unserialize($this->_fields['widgets']);
        } else {
            return [];
        }
    }

    /**
     * ウィジェット数を取得する
     * @return int
     */
    protected function _getCount()
    {
        return count($this->widgets);
    }

}
