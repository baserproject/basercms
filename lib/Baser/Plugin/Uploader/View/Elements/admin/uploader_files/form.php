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
if (!isset($uploaderCategories)) {
	$uploaderCategories = $this->BcForm->getControlSource("UploaderFile.uploader_category_id");
}
if (!isset($listId)) {
	$listId = '';
}
if (empty($popup)) {
	$users = $this->BcForm->getControlSource("UploaderFile.user_id");
}
?>


<?php if (empty($popup)): ?>

	<div id="AdminPrefix" style="display:none;"><?php echo Configure::read('Routing.prefixes.0'); ?></div>
	<script type="text/javascript">
		$(function () {
			var name = $("#UploaderFileName").val();
			var imgUrl = $.baseUrl + '/' + $("#AdminPrefix").html() + '/uploader/uploader_files/ajax_image/' + name + '/midium';
			$.get(imgUrl, function (res) {
				$("#UploaderFileImage").html(res);
			});
		});
	</script>


	<div class="em-box align-left">
		<?php $url = $this->Uploader->getFileUrl($this->request->data['UploaderFile']['name']) ?>
		<strong><?php echo __d('baser', 'このファイルのURL') ?>
			：<?php $this->BcBaser->link($this->BcBaser->getUri($url), $url) ?></strong>
	</div>
<?php endif ?>


<?php if (!empty($popup)): ?>
	<?php echo $this->BcForm->create('UploaderFile', ['url' => ['action' => 'edit'], 'id' => 'UploaderFileEditForm' . $listId]) ?>
<?php else: ?>
	<?php echo $this->BcForm->create('UploaderFile', ['url' => ['action' => 'edit', $this->request->data['UploaderFile']['id'], $listId], 'id' => 'UploaderFileEditForm' . $listId, 'type' => 'file']) ?>
<?php endif ?>


<table cellpadding="0" cellspacing="0" class="form-table">
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('UploaderFile.id', 'NO') ?></th>
		<td class="col-input">
			<?php if (empty($popup)): ?>
				<?php echo $this->BcForm->value('UploaderFile.id') ?>
				<?php echo $this->BcForm->input('UploaderFile.id', ['type' => 'hidden']) ?>
			<?php else: ?>
				<?php echo $this->BcForm->text('UploaderFile.id', ['size' => 30, 'maxlength' => 255, 'readonly' => 'readonly', 'id' => 'UploaderFileId' . $listId, 'class' => 'uploader-file-id']) ?>&nbsp;
			<?php endif ?>
		</td>
	</tr>
	<?php if (empty($popup)): ?>
		<tr>
			<th><?php echo __d('baser', 'アップロードファイル') ?></th>
			<td><?php echo $this->BcForm->input('UploaderFile.name', ['type' => 'file', 'delCheck' => false, 'imgsize' => 'midium', 'force' => 'true']) ?></td>
		</tr>
	<?php else: ?>
		<tr>
			<th class="col-head">
				<!--<span class="required">*</span>&nbsp;--><?php echo $this->BcForm->label('UploaderFile.name', __d('baser', 'ファイル名')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->text('UploaderFile.name', ['size' => 30, 'maxlength' => 255, 'readonly' => 'readonly', 'id' => 'UploaderFileName' . $listId, 'class' => 'uploader-file-name']) ?>
				<?php echo $this->BcForm->error('UploaderFile.name', __d('baser', 'ファイル名を入力して下さい')) ?>&nbsp;
			</td>
		</tr>
	<?php endif ?>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('UploaderFile.alt', __d('baser', '説明文')) ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->text('UploaderFile.alt', ['size' => 51, 'maxlength' => 255, 'id' => 'UploaderFileAlt' . $listId, 'class' => 'uploader-file-alt']) ?>
			&nbsp;
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('UploaderFile.publish_begin_date', __d('baser', '公開期間')) ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('UploaderFile.publish_begin', [
				'type' => 'dateTimePicker',
				'size' => 12,
				'maxlength' => 10,
				'dateLabel' => ['text' => '開始日付'],
				'timeLabel' => ['text' => '開始時間']
			]) ?>
			&nbsp;〜&nbsp;
			<?php echo $this->BcForm->input('UploaderFile.publish_end', [
				'type' => 'dateTimePicker',
				'size' => 12,
				'maxlength' => 10,
				'dateLabel' => ['text' => '終了日付'],
				'timeLabel' => ['text' => '終了時間']
			]) ?>
			<?php echo $this->BcForm->error('UploaderFile.publish_begin') ?>
			<?php echo $this->BcForm->error('UploaderFile.publish_end') ?>
		</td>
	</tr>
	<?php if ($uploaderCategories): ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('UploaderFile.uploader_category_id', __d('baser', 'カテゴリ')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('UploaderFile.uploader_category_id', ['type' => 'select', 'options' => $uploaderCategories, 'empty' => '指定なし', 'id' => '_UploaderFileUploaderCategoryId' . $listId]) ?>
			</td>
		</tr>
	<?php endif ?>
	<tr>
		<th class="col-head"><?php echo __d('baser', '保存者') ?></th>
		<td class="col-input">
			<span id="UploaderFileUserName<?php echo $listId ?>">
			<?php if (empty($popup)): ?>
				<?php echo $this->BcText->arrayValue($this->request->data['UploaderFile']['user_id'], $users) ?>
			<?Php endif ?>
			</span>
			<?php echo $this->BcForm->input('UploaderFile.user_id', ['type' => 'hidden', 'id' => 'UploaderFileUserId' . $listId]) ?>
		</td>
	</tr>
	<?php if (!empty($popup)): ?>
		<tr>
			<td colspan="2" id="UploaderFileImage<?php echo $listId ?>"
				class="uploader-file-image"><?php echo $this->Html->image('admin/ajax-loader.gif') ?></td>
		</tr>
	<?php endif ?>
</table>


<?php if (empty($popup)): ?>
	<div class="submit">
		<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
		<?php $this->BcBaser->link(__d('baser', '削除'),
			['action' => 'delete', $this->BcForm->value('UploaderFile.id')],
			['class' => 'submit-token button'],
			sprintf(__d('baser', '%s を本当に削除してもいいですか？'), $this->BcForm->value('UploaderFile.name')),
			false); ?>
	</div>
<?php endif; ?>

<?php echo $this->BcForm->end() ?>
