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

namespace BaserCore\Model\Table;

/**
 * Class ContentLink
 *
 * リンク モデル
 *
 * @package Baser.Model
 */
class ContentLinksTable extends AppTable
{

    /**
     * Behavior Setting
     *
     * @var array
     */
    public $actsAs = ['BcContents'];

    /**
     * ContentLink constructor.
     *
     * @param bool $id
     * @param null $table
     * @param null $ds
     */
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->validate = [
            'url' => [
                ['rule' => ['notBlank'], 'message' => __d('baser', 'リンク先URLを入力してください。')]]
        ];
    }

}
