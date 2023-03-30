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

namespace BaserCore\Model\Table\Exception;

use Cake\Core\Exception\Exception;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;

/**
 * Class CopyFailedException
 * @property array $errors
 */
class CopyFailedException extends Exception
{
    /**
     * validation errors
     *
     * @var array
     */
    public $errors;

    /**
     * setErrors
     *
     * @param array $errors validation errors
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setErrors(?array $errors = null)
    {
        $this->errors = $errors;
    }

    /**
     * getErrors
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
