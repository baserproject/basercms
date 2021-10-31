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
use BaserCore\Service\ContentService;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Service\PermissionService;
use BaserCore\Service\UserServiceInterface;
use BaserCore\Service\ContentServiceInterface;
use BaserCore\Service\PermissionServiceInterface;

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
        $createdItems = BcUtil::getContentsItem();
        $types = [];
        foreach($createdItems as $key => $value) {
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
     * @noTodo
     * @unitTest
     */
    public function isContentDeletable(): bool
    {
        $userGroups = BcUtil::loginUser()->user_groups;
        if ($userGroups) {
            foreach ($userGroups as $userGroup) {
                if ($this->PermissionService->check('/' . BcUtil::getPrefix() . '/contents/delete', $userGroup->id)) return true;
            }
        }
        return false;
    }

    /**
     * コンテンツフォルダーのリストを取得
     *
     * @param null $siteId
     * @param array $options
     * @return array|bool
     */
    public function getContentFolderList($siteId = null, $options = [])
    {
        return $this->ContentService->getContentFolderList($siteId, $options);
    }

    /**
     * コンテンツ管理上のURLを元に正式なURLを取得する
     *
     * ドメインからのフルパスでない場合、デフォルトでは、
     * サブフォルダ設置時等の baseUrl（サブフォルダまでのパス）は含まない
     *
     * @param string $url コンテンツ管理上のURL
     * @param bool $full http からのフルのURLかどうか
     * @param bool $useSubDomain サブドメインを利用しているかどうか
     * @param bool $base $full が false の場合、ベースとなるURLを含めるかどうか
     * @return string URL
     */
    public function getUrl($url, $full = false, $useSubDomain = false, $base = false)
    {
        return $this->ContentService->getUrl($url, $full, $useSubDomain, $base);
    }

    /**
     * コンテンツIDよりフルURLを取得する
     *
     * @param int $id コンテンツID
     * @return mixed
     */
    public function getUrlById($id, $full = false)
    {
        return $this->ContentService->getUrlById($id, $full);
    }
}
