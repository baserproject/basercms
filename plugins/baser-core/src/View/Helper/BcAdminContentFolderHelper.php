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

use Cake\View\Helper;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\ContentFolderService;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Service\ContentFolderServiceInterface;
use BaserCore\Service\PermissionServiceInterface;

/**
 * BcAdminContentFolderHelper
 * @property ContentFolderService $ContentFolderService
 */
class BcAdminContentFolderHelper extends Helper
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
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->ContentFolderService = $this->getService(ContentFolderServiceInterface::class);
    }

    /**
     * フォルダのテンプレートリストを取得する
     *
     * @param $contentId
     * @param $theme
     * @return array
     */
    public function getFolderTemplateList($contentId, $theme)
    {
        return $this->ContentFolderService->getFolderTemplateList($contentId, $theme);
    }
}
