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

namespace BaserCore\Model\Validation;

use Cake\ORM\TableRegistry;
use Cake\Validation\Validation;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class SiteValidation
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

    /**
     * エイリアスと同名のコンテンツが存在するか確認する
     * @param string $alias
     * @return bool
     * @unitTest
     * @noTodo
     * @checked
     */
    public static function checkContentExists($alias, $context)
    {
        $url = '/' . $alias;
        $contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $query = $contents->find()->where(['or' => [
            ['url' => $url],
            ['url' => $url . '/']
        ]]);
        if(!empty($context['data']['id'])) {
            $query = $query->where(['site_id IS NOT' => $context['data']['id']]);
        }
        return !((bool) $query->count());
    }

}
