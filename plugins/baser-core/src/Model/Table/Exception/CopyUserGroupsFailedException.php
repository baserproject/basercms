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

namespace BaserCore\Model\Table\Exception;
use Cake\Core\Exception\Exception;

/**
 * Class CopyUserGroupsException
 * @package BaserCore\Model\Table\Execption
 * @property array $errors
 */
class CopyUserGroupsFailedException extends Exception
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
     */
    public function setErrors(?array $errors = null)
    {
        $this->errors = $errors;
    }

    /**
     * getErrors
     *
     * @return void
     */
    public function getErrors()
    {
        return $this->errors;
    }
}