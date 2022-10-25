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

namespace BaserCore\Utility;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Trait BcContainerTrait
 * @package BaserCore\Utility
 */
trait BcContainerTrait
{
    /**
     * Get Service
     * @param $service
     * @return array|mixed|object
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getService($service)
    {
        return BcContainer::get()->get($service);
    }

    /**
     * Has Service
     * @param $service
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function hasService($service)
    {
        return BcContainer::get()->has($service);
    }

}
