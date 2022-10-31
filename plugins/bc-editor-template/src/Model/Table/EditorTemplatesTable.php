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

namespace BcEditorTemplate\Model\Table;

use BaserCore\Model\Table\AppTable;

/**
 * Class EditorTemplate
 *
 * エディタテンプレート　モデル
 *
 * @package Baser.Model
 */
class EditorTemplatesTable extends AppTable
{

    /**
     * behaviors
     *
     * @var    array
     */
    public $actsAs = [
        'BcUpload' => [
            'saveDir' => "editor",
            'fields' => [
                'image' => [
                    'type' => 'image',
                    'namefield' => 'id',
                    'nameadd' => false,
                    'imageresize' => ['prefix' => 'template', 'width' => '100', 'height' => '100']
                ]
            ]
        ]
    ];

    /**
     * EditorTemplate constructor.
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
                ['rule' => ['notBlank'], 'message' => __d('baser', 'テンプレート名を入力してください。')]],
            'image' => [
                ['rule' => ['fileExt', ['gif', 'jpg', 'jpeg', 'jpe', 'jfif', 'png']], 'message' => __d('baser', '許可されていないファイルです。')]]
        ];
    }

}
