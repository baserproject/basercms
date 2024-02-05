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
 * Class BlogContentValidation
 */
class BlogContentValidation extends Validation
{

    /**
     * アイキャッチ画像サイズバリデーション
     *
     * @return boolean
     * @uses checkEyeCatchSize
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function checkEyeCatchSize($value, $context)
    {
        $data = $context['data'];
        if (
            empty($data['eye_catch_size_thumb_width']) ||
            empty($data['eye_catch_size_thumb_height']) ||
            empty($data['eye_catch_size_mobile_thumb_width']) ||
            empty($data['eye_catch_size_mobile_thumb_height']) ||
            !is_numeric($data['eye_catch_size_thumb_width']) ||
            !is_numeric($data['eye_catch_size_thumb_height']) ||
            !is_numeric($data['eye_catch_size_mobile_thumb_width']) ||
            !is_numeric($data['eye_catch_size_mobile_thumb_height'])
        ) {
            return false;
        }

        return true;
    }

}
