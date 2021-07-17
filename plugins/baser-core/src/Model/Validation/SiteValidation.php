<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Model\Validation;

use Cake\Validation\Validation;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class SiteValidation
 * @package BaserCore\Model\Validation
 */
class SiteValidation extends Validation
{
    /**
     * エイリアスのスラッシュをチェックする
     *
     * - 連続してスラッシュは入力できない
     * - 先頭と末尾にスラッシュは入力できない
     * @param string $alias
     * @return bool
     * @unitTest
     * @noTodo
     * @checked
     */
    public static function aliasSlashChecks($alias)
    {
        if (preg_match('/(^\/|[\/]{2,}|\/$)/', $alias)) {
            return false;
        }
        return true;
    }
}
