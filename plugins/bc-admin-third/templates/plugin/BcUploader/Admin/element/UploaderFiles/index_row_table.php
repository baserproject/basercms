<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

/**
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcUploader\Model\Entity\UploaderFile $uploaderFile
 * @var array $uploaderCategories
 * @var array $users
 * @var int $listId
 * @checked
 * @noTodo
 * @unitTest
 */
$classies = [];
if (!$this->Uploader->isPublish($uploaderFile)) {
  $classies = ['unpublish', 'disablerow', 'selectable-file'];
  $statusPublish = false;
} else {
  $classies = ['publish', 'selectable-file'];
  $statusPublish = true;
}
?>


<tr class="<?= h(implode(' ', $classies)); ?>" id="selectedFile<?= h($uploaderFile->id) ?>">
  <td class="id bca-table-listup__tbody-td">
    <?= h($uploaderFile->id) ?>
    <div style="display:none">
      <span class="small"><?= h($uploaderFile->small) ?></span>
      <span class="midium"><?= h($uploaderFile->midium) ?></span>
      <span class="large"><?= h($uploaderFile->large) ?></span>
      <span class="url">
        <?= h($this->BcHtml->Url->build($this->Uploader->getFileUrl($uploaderFile->name))) ?>
      </span>
      <span class="user-id"><?= h($uploaderFile->user_id) ?></span>
      <span class="name"><?= h($uploaderFile->name) ?></span>
      <span class="alt"><?= h($uploaderFile->alt) ?></span>
    </div>
  </td>
  <td class="img bca-table-listup__tbody-td">
    <?= $this->Uploader->file($uploaderFile, [
      'size' => 'small',
      'alt' => h($uploaderFile->alt),
      'style' => 'width:80px'
    ]) ?>
  </td>
  <td class="bca-table-listup__tbody-td">
    <span class="uploader-category-id">
      <?= h($this->BcText->arrayValue($uploaderFile->uploader_category_id, $uploaderCategories)) ?>
    </span>
  </td>
  <td class="bca-table-listup__tbody-td">
    <span><?= h($uploaderFile->name) ?></span>
    <?php if ($uploaderFile->alt): ?>
    <br/><span><?= $this->BcText->truncate(h($uploaderFile->alt), 40) ?><span>
    <?php endif ?>
  </td>
  <td class="bc-align-center bca-table-listup__tbody-td">
    <?= $this->BcText->booleanMark($statusPublish); ?>
  </td>
  <td class="user-name bca-table-listup__tbody-td">
    <?= h($this->BcText->arrayValue($uploaderFile->user_id, $users)) ?>
  </td>
  <td class="created bca-table-listup__tbody-td">
    <span class="created"><?php echo $this->BcTime->format($uploaderFile->created, 'Y.m.d') ?></span><br/>
    <span class="modified"><?php echo $this->BcTime->format($uploaderFile->modified, 'Y.m.d') ?></span>
  </td>
  <?php if (!$listId): ?>
    <td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions" style="width:15%">
      <?php $this->BcBaser->link('', ['action' => 'edit', $uploaderFile->id], [
        'title' => __d('baser_core', '編集'),
        'class' => 'bca-btn-icon',
        'data-bca-btn-type' => 'edit',
        'data-bca-btn-size' => 'lg'
      ]) ?>
      <?php echo $this->BcAdminForm->postLink('', ['action' => 'delete', $uploaderFile->id], [
        'confirm' => __d('baser_core', '{0} を本当に削除してもいいですか？', $uploaderFile->name),
        'title' => __d('baser_core', '削除'),
        'class' => 'bca-btn-icon',
        'data-bca-btn-type' => 'delete',
        'data-bca-btn-size' => 'lg'
      ]) ?>
    </td>
  <?php endif ?>
</tr>
