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

namespace BcBlog\Model\Table;

/**
 * ブログ設定モデル
 *
 * @package Blog.Model
 */
class BlogConfigsTable extends BlogAppTable
{

    /**
     * クラス名
     *
     * @var string
     */
    public $name = 'BlogConfig';

    /**
     * ビヘイビア
     *
     * @var array
     */
    public $actsAs = ['BcCache'];
}
