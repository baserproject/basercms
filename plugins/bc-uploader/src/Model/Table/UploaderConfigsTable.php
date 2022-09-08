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

 namespace BcUploader\Model\Table;

use BaserCore\Model\Table\AppTable;

/**
 * ファイルアップローダー設定モデル
 *
 * @package         Uploader.Model
 */
class UploaderConfigsTable extends AppTable
{

    /**
     * バリデート
     *
     * @var        array
     * @access    public
     */
    public $validate = [
        'large_width' => [[
            'rule' => ['notBlank'],
            'message' => 'PCサイズ（大）[幅] を入力してください。'
        ]],
        'large_height' => [[
            'rule' => ['notBlank'],
            'message' => 'PCサイズ（大）[高さ] を入力してください。'
        ]],
        'midium_width' => [[
            'rule' => ['notBlank'],
            'message' => 'PCサイズ（中）[幅] を入力してください。'
        ]],
        'midium_height' => [[
            'rule' => ['notBlank'],
            'message' => 'PCサイズ（中）[高さ] を入力してください。'
        ]],
        'small_width' => [[
            'rule' => ['notBlank'],
            'message' => 'PCサイズ（小）[幅] を入力してください。'
        ]],
        'small_height' => [[
            'rule' => ['notBlank'],
            'message' => 'PCサイズ（小）[高さ] を入力してください。'
        ]],
        'mobile_large_width' => [[
            'rule' => ['notBlank'],
            'message' => '携帯サイズ（大）[幅] を入力してください。'
        ]],
        'mobile_large_height' => [[
            'rule' => ['notBlank'],
            'message' => '携帯サイズ（大）[高さ] を入力してください。'
        ]],
        'mobile_small_width' => [[
            'rule' => ['notBlank'],
            'message' => '携帯サイズ（小）[幅] を入力してください。'
        ]],
        'mobile_small_height' => [[
            'rule' => ['notBlank'],
            'message' => '携帯サイズ（小）[幅] を入力してください。'
        ]]
    ];
}
