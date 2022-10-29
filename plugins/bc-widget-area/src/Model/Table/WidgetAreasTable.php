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

namespace BcWidgetArea\Model\Table;

use BaserCore\Model\Table\AppTable;

/**
 * Class WidgetArea
 *
 * ウィジェットエリアモデル
 *
 * @package Baser.Model
 */
class WidgetAreasTable extends AppTable
{

    /**
     * ビヘイビア
     *
     * @var array
     */
    public $actsAs = ['BcCache'];

    /**
     * WidgetArea constructor.
     *
     * @param bool $id
     * @param null $table
     * @param null $ds
     */
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->validate = [
            'name' => [
                'notBlank' => ['rule' => ['notBlank'], 'message' => __d('baser', 'ウィジェットエリア名を入力してください。')],
                'maxLength' => ['rule' => ['maxLength', 255], 'message' => __d('baser', 'ウィジェットエリア名は255文字以内で入力してください。')]]
        ];
    }

    /**
     * コントロールソース取得
     * @param string $field
     * @return array
     */
    public function getControlSource($field)
    {
        $controllSource['id'] = $this->find('list');
        if (isset($controllSource[$field])) {
            return $controllSource[$field];
        } else {
            return [];
        }
    }

}
