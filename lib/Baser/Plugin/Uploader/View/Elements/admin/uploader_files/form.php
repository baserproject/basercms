<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader.View
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */
if(!isset($uploaderCategories)) {
	$uploaderCategories = $this->BcForm->getControlSource("UploaderFile.uploader_category_id");
}
if(!isset($listId)) {
	$listId = '';
}
if(empty($popup)) {
	$users = $this->BcForm->getControlSource("UploaderFile.user_id");
}
?>


<?php if(empty($popup)): ?>

<div id="AdminPrefix" style="display:none;"><?php echo Configure::read('Routing.prefixes.0'); ?></div>
<script type="text/javascript">
$(function(){
	var name = $("#UploaderFileName").val();
	var imgUrl = $.baseUrl + '/' + $("#AdminPrefix").html() + '/uploader/uploader_files/ajax_image/'+name+'/midium';
	$.get(imgUrl,function(res){
		$("#UploaderFileImage").html(res);
	});	
});
</script>


<div class="em-box align-left">
	<?php $url = $this->Uploader->getFileUrl($this->request->data['UploaderFile']['name']) ?>
	<strong>このファイルのURL：<?php $this->BcBaser->link($this->BcBaser->getUri($url), $url) ?></strong>
</div>
<?php endif ?>


<?php if(!empty($popup)): ?>
	<?php echo $this->BcForm->create('UploaderFile',array('action' => 'edit', 'id' => 'UploaderFileEditForm'.$listId)) ?>
<?php else: ?>
	<?php echo $this->BcForm->create('UploaderFile',array('action' => 'edit', 'url' => array($this->request->data['UploaderFile']['id'], $listId), 'id' => 'UploaderFileEditForm'.$listId, 'type' => 'file')) ?>
<?php endif ?>


<table cellpadding="0" cellspacing="0" class="form-table">
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('UploaderFile.id', 'NO') ?></th>
		<td class="col-input">
		<?php if(empty($popup)): ?>
			<?php echo $this->BcForm->value('UploaderFile.id') ?>
			<?php echo $this->BcForm->input('UploaderFile.id', array('type' => 'hidden')) ?>
		<?php else: ?>
			<?php echo $this->BcForm->text('UploaderFile.id', array('size'=>30,'maxlength'=>255,'readonly'=>'readonly','id'=>'UploaderFileId'.$listId, 'class' => 'uploader-file-id')) ?>&nbsp;
		<?php endif ?>
		</td>
	</tr>
<?php if(empty($popup)): ?>
	<tr><th>アップロードファイル</th><td><?php echo $this->BcForm->file('UploaderFile.name', array('delCheck' => false, 'imgsize' => 'midium', 'force' => 'true')) ?></td></tr>
<?php else: ?>
	<tr>
		<th class="col-head"><!--<span class="required">*</span>&nbsp;--><?php echo $this->BcForm->label('UploaderFile.name', 'ファイル名') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->text('UploaderFile.name', array('size'=>30,'maxlength'=>255,'readonly'=>'readonly','id'=>'UploaderFileName'.$listId, 'class' => 'uploader-file-name')) ?>
			<?php echo $this->BcForm->error('UploaderFile.name', 'ファイル名を入力して下さい') ?>&nbsp;
		</td>
	</tr>
<?php endif ?>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('UploaderFile.alt', '説明文') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->text('UploaderFile.alt', array('size'=>51,'maxlength'=>255,'id'=>'UploaderFileAlt'.$listId, 'class' => 'uploader-file-alt')) ?>&nbsp;
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('UploaderFile.publish_begin_date', '公開期間') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->dateTimePicker('UploaderFile.publish_begin', array('size' => 12, 'maxlength' => 10)) ?>
			&nbsp;〜&nbsp;
			<?php echo $this->BcForm->dateTimePicker('UploaderFile.publish_end', array('size' => 12, 'maxlength' => 10)) ?>
			<?php echo $this->BcForm->error('UploaderFile.publish_begin') ?>
			<?php echo $this->BcForm->error('UploaderFile.publish_end') ?>
		</td>
	</tr>
<?php if($uploaderCategories): ?>
	<tr>
		<th class="col-head"><?php echo $this->BcForm->label('UploaderFile.uploader_category_id', 'カテゴリ') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('UploaderFile.uploader_category_id', array('type' => 'select', 'options' => $uploaderCategories, 'empty' => '指定なし', 'id' => '_UploaderFileUploaderCategoryId'.$listId)) ?>
		</td>
	</tr>
<?php endif ?>
	<tr>
		<th class="col-head">保存者</th>
		<td class="col-input">
			<span id="UploaderFileUserName<?php echo $listId ?>">
			<?php if(empty($popup)): ?>
				<?php echo $this->BcText->arrayValue($this->request->data['UploaderFile']['user_id'], $users) ?>
			<?Php endif ?>
			</span>
			<?php echo $this->BcForm->input('UploaderFile.user_id', array('type' => 'hidden', 'id' => 'UploaderFileUserId'.$listId)) ?>
		</td>
	</tr>
<?php if(!empty($popup)): ?>
	<tr><td colspan="2" id="UploaderFileImage<?php echo $listId ?>" class="uploader-file-image"><?php echo $this->Html->image('admin/ajax-loader.gif') ?></td></tr>
<?php endif ?>
</table>


<?php if(empty($popup)): ?>
<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
	<?php $this->BcBaser->link('削除',
			array('action' => 'delete', $this->BcForm->value('UploaderFile.id')),
			array('class' => 'submit-token button'),
			sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('UploaderFile.name')),
			false); ?>
</div>
<?php endif; ?>

<?php echo $this->BcForm->end() ?>