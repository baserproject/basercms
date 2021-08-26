<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

namespace BaserCore\View\Helper;

use BaserCore\Service\PermissionServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\View\View;

/**
 * Class BcAdminPermissionHelper
 * @package BaserCore\View\Helper
 * @property PermissionServiceInterface $PermissionService
 */
class BcAdminPermissionHelper extends \Cake\View\Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * BcAdminPermissionHelper constructor.
     * @param View $view
     * @param array $config
     */
    public function __construct(View $view, array $config = [])
    {
        parent::__construct($view, $config);
        $this->PermissionService = $this->getService(PermissionServiceInterface::class);
    }

    /**
     * アクセス制限におけるメソッドのリストを取得
     * @return array
     */
    public function getMethodList()
    {
        return $this->PermissionService->getMethodList();
    }
}
