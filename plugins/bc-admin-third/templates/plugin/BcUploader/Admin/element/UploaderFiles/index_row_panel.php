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
 * @var array $users
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


<span class="bca-file-list__item <?= h(implode(' ', $classies)); ?>" id="selectedFile<?= h($uploaderFile->id) ?>" class="uploader-file-row-panel">
	<?php echo $this->Uploader->file($uploaderFile, [
    'width' => 120,
    'height' => 120,
    'size' => 'small',
    'alt' => h($uploaderFile->alt),
    'style' => 'width:120px;height:120px'
  ]) ?>
	<div class="uploader-file-row-panel__alt">
		<span class="id">
		  <?= h($uploaderFile->id) ?></span>.<span><?= h($this->BcText->truncate(h($uploaderFile->alt), 13)) ?>
    </span>
	</div>
	<span class="name"><?= h($uploaderFile->name) ?></span>
	<div class="uploader-file-row-panel__created">
		<span class="created" style="white-space: nowrap">
			[<?php echo __d('baser_core', '公開状態') ?>：<?php echo $this->BcText->booleanMark($statusPublish); ?>]&nbsp;<?php echo $this->BcTime->format($uploaderFile->created, 'Y.m.d') ?>
		</span>
	</div>
	<div class="user-name uploader-file-row-panel__user-name">
	  <span><?php echo h($this->BcText->arrayValue($uploaderFile->user_id, $users)) ?></span>
  </div>
	<div style="display:none">
		<span class="modified"><?php echo $this->BcTime->format($uploaderFile->modified, 'Y.m.d') ?></span>
		<span class="small"><?php echo $uploaderFile->small ?></span>
		<span class="midium"><?php echo $uploaderFile->midium ?></span>
		<span class="large"><?php echo $uploaderFile->large ?></span>
		<span class="url">
		  <?= h($this->BcHtml->Url->build($this->Uploader->getFileUrl($uploaderFile->name))) ?>
    </span>
		<span class="user-id"><?= h($uploaderFile->user_id) ?></span>
		<span class="publish-begin">
		  <?php echo $this->BcTime->format($uploaderFile->publish_begin, 'yyyy/MM/dd') ?>
    </span>
		<span class="publish-begin-time">
		  <?php echo $this->BcTime->format($uploaderFile->publish_begin, 'HH:mm:ss') ?>
    </span>
		<span class="publish-end">
		  <?php echo $this->BcTime->format($uploaderFile->publish_end, 'yyyy/MM/dd') ?>
    </span>
		<span class="publish-end-time">
		  <?php echo $this->BcTime->format($uploaderFile->publish_end, 'HH:mm:ss') ?>
    </span>
		<span class="uploader-category-id"><?= h($uploaderFile->uploader_category_id) ?></span>
		<span class="alt"><?php echo h($uploaderFile->alt) ?></span>
	</div>
</span>
