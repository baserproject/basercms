<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.View
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */
$classies = [];
if (!$this->Uploader->isPublish($file)) {
	$classies = ['unpublish', 'disablerow', 'selectable-file'];
	$statusPublish = false;
} else {
	$classies = ['publish', 'selectable-file'];
	$statusPublish = true;
}
$class = ' class="' . implode(' ', $classies) . '"';
?>


<tr<?php echo $class; ?> id="selectedFile<?php echo $file['UploaderFile']['id'] ?>">
	<?php if (!$listId): ?>
		<td class="row-tools" style="width:15%">
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', ['alt' => __d('baser', '編集'), 'class' => 'btn']), ['action' => 'edit', $file['UploaderFile']['id']], ['title' => __d('baser', '編集')]) ?>
			<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_delete.png', ['alt' => __d('baser', '削除'), 'class' => 'btn']), ['action' => 'delete', $file['UploaderFile']['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete']) ?>
		</td>
	<?php endif ?>
	<td class="id">
		<?php echo $file['UploaderFile']['id'] ?>
		<div style="display:none">
			<span class="small"><?php echo $file['UploaderFile']['small'] ?></span>
			<span class="midium"><?php echo $file['UploaderFile']['midium'] ?></span>
			<span class="large"><?php echo $file['UploaderFile']['large'] ?></span>
			<span
				class="url"><?php echo $this->BcHtml->url($this->Uploader->getFileUrl($file['UploaderFile']['name'])) ?></span>
			<span class="user-id"><?php echo $file['UploaderFile']['user_id'] ?></span>
			<span class="name"><?php echo $file['UploaderFile']['name'] ?></span>
			<span class="alt"><?php echo h($file['UploaderFile']['alt']) ?></span>
		</div>
	</td>
	<td class="img"><?php echo $this->Uploader->file($file, ['size' => 'small', 'alt' => h($file['UploaderFile']['alt']), 'style' => 'width:80px']) ?></td>
	<td><span
			class="uploader-category-id"><?php echo $this->BcText->arrayValue($file['UploaderFile']['uploader_category_id'], $uploaderCategories) ?>
	</td>
	<td width="30%">
		<span><?php echo $file['UploaderFile']['name'] ?></span>
		<?php if ($file['UploaderFile']['alt']): ?>
		<br/><span><?php echo $this->BcText->truncate(h($file['UploaderFile']['alt']), 40) ?><span><?php endif ?>
	</td>
	<td class="align-center"><?php echo $this->BcText->booleanMark($statusPublish); ?></td>
	<td class="user-name"><?php echo h($this->BcText->arrayValue($file['UploaderFile']['user_id'], $users)) ?></td>
	<td class="created">
		<span class="created"><?php echo $this->BcTime->format('Y.m.d', $file['UploaderFile']['created']) ?></span><br/>
		<span class="modified"><?php echo $this->BcTime->format('Y.m.d', $file['UploaderFile']['modified']) ?></span>
	</td>
</tr>
