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
use BaserCore\Service\UserManageService;
use Cake\View\Helper;

/**
 * Class BcUserManageHelper
 * @package BaserCore\View\Helper
 */
class BcUserManageHelper extends Helper
{

    /**
     * User Manage Service
     * @var UserManageService
     */
    public $UserManage;

    /**
     * initialize
     * @param array $config
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->UserManage = new UserManageService();
    }

    /**
     * ログインユーザー自身の更新かどうか
     * @return false
     */
    public function isSelfUpdate()
    {
        return $this->UserManage->isSelfUpdate($this->_View->getRequest());
    }

    /**
     * 更新ができるかどうか
     * @return false
     */
    public function isEditable()
    {
        return $this->UserManage->isEditable($this->_View->getRequest());
    }

    /**
     * 削除ができるかどうか
     * @param $request
     * @return false
     */
    public function isDeletable()
    {
        return $this->UserManage->isDeletable($this->_View->getRequest());
    }

    /**
     * ユーザーグループ選択用のリスト
     * @return array
     */
    public function getUserGroupList()
    {
        return $this->UserManage->getUserGroupList();
    }

}
