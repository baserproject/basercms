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

namespace BaserCore\Service\Admin;

use BaserCore\Service\SiteConfigsTrait;
use BaserCore\Service\UserGroupsService;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UserGroupManageService
 * @package BaserCore\Service
 */
class UserGroupManageService extends UserGroupsService implements UserGroupManageServiceInterface
{

    /**
     * SiteConfigsTrait
     */
    use SiteConfigsTrait;

}
