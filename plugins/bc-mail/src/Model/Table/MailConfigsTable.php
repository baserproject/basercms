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

namespace BcMail\Model\Table;
/**
 * メール設定モデル
 *
 * @package Mail.Model
 *
 */
class MailConfigsTable extends MailAppTable
{

    /**
     * クラス名
     *
     * @var string
     */
    public $name = 'MailConfig';

    /**
     * ビヘイビア
     *
     * @var array
     */
    public $actsAs = ['BcCache'];
}
