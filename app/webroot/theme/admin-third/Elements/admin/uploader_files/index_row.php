<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Uploader.View
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
	<td class="id bca-table-listup__tbody-td">
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
	<td class="img bca-table-listup__tbody-td"><?php echo $this->Uploader->file($file, ['size' => 'small', 'alt' => h($file['UploaderFile']['alt']), 'style' => 'width:80px']) ?></td>
	<td class="bca-table-listup__tbody-td"><span
			class="uploader-category-id"><?php echo $this->BcText->arrayValue($file['UploaderFile']['uploader_category_id'], $uploaderCategories) ?>
	</td>
	<td class="bca-table-listup__tbody-td">
		<span><?php echo h($file['UploaderFile']['name']) ?></span>
		<?php if ($file['UploaderFile']['alt']): ?>
		<br/><span><?php echo $this->BcText->truncate(h($file['UploaderFile']['alt']), 40) ?><span><?php endif ?>
	</td>
	<td class="bc-align-center bca-table-listup__tbody-td"><?php echo $this->BcText->booleanMark($statusPublish); ?></td>
	<td class="user-name bca-table-listup__tbody-td"><?php echo h($this->BcText->arrayValue($file['UploaderFile']['user_id'], $users)) ?></td>
	<td class="created bca-table-listup__tbody-td">
		<span class="created"><?php echo $this->BcTime->format('Y.m.d', $file['UploaderFile']['created']) ?></span><br/>
		<span class="modified"><?php echo $this->BcTime->format('Y.m.d', $file['UploaderFile']['modified']) ?></span>
	</td>
	<?php if (!$listId): ?>
		<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions" style="width:15%">
			<?php $this->BcBaser->link('', ['action' => 'edit', $file['UploaderFile']['id']], ['title' => __d('baser', '編集'), 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
			<?php $this->BcBaser->link('', ['action' => 'delete', $file['UploaderFile']['id']], ['title' => __d('baser', '削除'), 'class' => 'bca-btn-icon btn-delete', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
		</td>
	<?php endif ?>
</tr>
