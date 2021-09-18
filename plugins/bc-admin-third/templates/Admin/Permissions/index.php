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


$this->BcAdmin->setTitle(sprintf(__d('baser', '%s｜アクセス制限設定一覧'), $currentUserGroup->title));
$this->BcAdmin->setHelp('permissions_index');

$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add', $currentUserGroup->id],
  'title' => __d('baser', '新規追加'),
]);
?>

<script>
$(function () {
  /**
   * 並び替え機能実装
   */
  $.bcSortable.init({
      updateSortUrl: "<?php echo $this->BcBaser->getUrl(['controller' => 'permissions', 'action' => 'update_sort', $currentUserGroup->id]) ?>"
  });
  /**
   * 一括処理実装
   */
  $.bcBatch.init({
      batchUrl: "<?php echo $this->BcBaser->getUrl(['controller' => 'permissions', 'action' => 'batch']) ?>"
  });
});

</script>


<section id="DataList">
    <?php $this->BcBaser->element('Permissions/index_list') ?>
</section>
<?= $this->fetch('postLink') ?>
