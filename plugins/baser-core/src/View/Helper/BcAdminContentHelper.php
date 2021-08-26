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
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Service\UserServiceInterface;
use BaserCore\Service\ContentService;
use BaserCore\Service\ContentServiceInterface;

/**
 * BcAdminContentHelper
 * @property ContentService $ContentService
 * @property PermissionService $PermissionService
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
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->ContentService = $this->getService(ContentServiceInterface::class);
        $this->PermissionService = $this->getService(PermissionServiceInterface::class);
    }

    /**
     * 登録されているタイプの一覧を取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTypes(): array
    {
        $createdSettings = BcUtil::getContentsItem();
        $types = [];
        foreach($createdSettings as $key => $value) {
            $types[$key] = $value['title'];
        }
        return $types;
    }

    /**
     * 作成者一覧を取得する
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAuthors()
    {
        return $this->getService(UserServiceInterface::class)->getList();
    }

    /**
     * コンテンツが削除可能かどうか
     *
     * @return bool
     * @checked
     */
    public function isContentDeletable(): bool
    {
        // TODO: true falseどっちを返すか仕様を確認する
        if ($userGroups = BcUtil::loginUser()->user_groups) {
            foreach ($userGroups as $userGroup) {
                if ($this->PermissionService->check('/' . BcUtil::getPrefix() . '/contents/delete', $userGroup)) return false;
            }
        }
        return true;
    }

}
