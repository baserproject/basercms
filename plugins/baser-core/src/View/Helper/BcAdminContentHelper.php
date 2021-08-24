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
namespace BaserCore\View\Helper;

use BaserCore\Service\Admin\ContentManageService;
use BaserCore\Service\Admin\ContentManageServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcAdminContentHelper
 * @property ContentManageService $ContentManage
 */
class BcAdminContentHelper extends Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * initialize
     * @param array $config
     * @checked
     * @noTodo
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->ContentManage = $this->getService(ContentManageServiceInterface::class);
    }

    /**
     * 登録されているタイプの一覧を取得する
     * @return array
     */
    public function getTypes()
    {
        return $this->ContentManage->getTypes();
    }

    public function getAuthors()
    {
        return $this->ContentManage->getAuthors();
    }
}
