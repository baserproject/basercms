<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

use BaserCore\Model\Entity\UserGroup;
use BaserCore\View\BcAdminAppView;

/**
 * [ADMIN] アクセス制限設定一覧
 *
 * @var BcAdminAppView $this
 * @var UserGroup $currentUserGroup
 */
$this->BcAdmin->setTitle(sprintf(__d('baser', '%s｜アクセス制限設定一覧'), $currentUserGroup->title));
$this->BcAdmin->setHelp('permissions_index');
$this->BcBaser->js('admin/permissions/index.bundle', false, [
  'id' => 'AdminPermissionsIndexScript',
  'data-userGroupId' => $currentUserGroup->id
]);
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add', $currentUserGroup->id],
  'title' => __d('baser', '新規追加'),
]);
?>


<section id="DataList">
    <?php $this->BcBaser->element('Permissions/index_list') ?>
</section>
<?= $this->fetch('postLink') ?>
