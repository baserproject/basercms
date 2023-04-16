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

namespace BcBlog\Model\Validation;

use Cake\Validation\Validation;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;

/**
 * Class BlogCategoryValidation
 */
class BlogCategoryValidation extends Validation
{

    /**
     * 同じニックネームのカテゴリがないかチェックする
     * 同じブログコンテンツが条件
     *
     * @param array $check
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function duplicateBlogCategory($value, $context)
    {
        $table = $context['providers']['table'];
        $data = $context['data'];
        $newRecord = $context['newRecord'];
        $field = $context['field'];
        $conditions = [
            'BlogCategories.' . $field => $value,
            'BlogCategories.blog_content_id' => $data['blog_content_id']
        ];
        if (!$newRecord) {
            $conditions['NOT'] = ['BlogCategories.id' => $data['id']];
        }
        if ($table->find()->where($conditions)->count()) {
            return false;
        } else {
            return true;
        }
    }

}
