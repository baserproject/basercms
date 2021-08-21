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

/**
 * [ADMIN] アクセス制限設定一覧
 *
 * @var BcAppView $this
 */

use BaserCore\View\{AppView as AppViewAlias};

// $this->BcBaser->i18nScript([
//   'sorttableAlertMessage1' => __d('baser', '並び替えの保存に失敗しました。')
// ]);
// $this->BcBaser->js('admin/libs/sorttable', false);
// $this->BcBaser->js([
//   'admin/libs/jquery.baser_ajax_data_list',
//   'admin/libs/jquery.baser_ajax_batch',
//   'admin/libs/jquery.baser_ajax_sort_table',
//   'admin/libs/baser_ajax_data_list_config',
//   'admin/libs/baser_ajax_batch_config'
// ]);

$this->BcAdmin->setTitle(sprintf(__d('baser', '%s｜アクセス制限設定一覧'), $currentUserGroup->title));
$this->BcAdmin->setHelp('permissions_index');

$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add', $currentUserGroup->id],
  'title' => __d('baser', '新規追加'),
]);
?>

<script type="text/javascript">
  // $(function () {
  //   $("#PermissionsSearchBody").show();
  //   $.baserAjaxDataList.init();
  //   $.baserAjaxSortTable.init({url: $("#AjaxSorttableUrl").html()});
  //   $.baserAjaxBatch.init({url: $("#AjaxBatchUrl").html()});
  // });
</script>

<?php /*
<div id="AjaxBatchUrl"
    style="display:none"><?php $this->BcBaser->url(['controller' => 'permissions', 'action' => 'ajax_batch']) ?></div>
<div id="AjaxSorttableUrl"
    style="display:none"><?php $this->BcBaser->url(['controller' => 'permissions', 'action' => 'ajax_update_sort', $userGroupId]) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
  <div id="flashMessage" class="notice-message"></div>
</div>
<div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('Permissions/index_list') ?></div>
*/
?>
<section id="DataList">
    <?php $this->BcBaser->element('Permissions/index_list') ?>
    <?= $this->fetch('postLink') ?>
</section>
