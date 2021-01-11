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
// IE文字化け対策
header('Content-type: text/html; charset=utf-8');
$users = $this->BcForm->getControlSource("UploaderFile.user_id");
$uploaderCategories = $this->BcForm->getControlSource("UploaderFile.uploader_category_id");
$this->passedArgs['action'] = 'ajax_list';
//==============================================================================
// Ajaxで呼び出される事が前提の為インラインで呼びだし
//==============================================================================
$this->BcBaser->js('admin/vendors/jquery.upload-1.0.0.min');
?>


<div id="DivPanelList">

	<?php $this->BcBaser->element('pagination') ?>

	<!-- form -->
	<?php if (!$installMessage): ?>
		<div id="UploaderForm" class="clearfix">
			<div>
				<?php echo $this->BcForm->label('UploaderFile.uploader_category_id', __d('baser', 'アップロード')) ?>&nbsp;
				<?php if ($uploaderCategories): ?>
					<?php echo $this->BcForm->input('UploaderFile.uploader_category_id', ['type' => 'select', 'options' => $uploaderCategories, 'empty' => __d('baser', 'カテゴリ指定なし'), 'id' => 'UploaderFileUploaderCategoryId' . $listId]) ?>&nbsp;
				<?php endif ?>
				<span id="SpanUploadFile<?php echo $listId ?>">
			<?php echo $this->BcForm->input('UploaderFile.file', ['type' => 'file', 'id' => 'UploaderFileFile' . $listId, 'class' => 'uploader-file-file', 'div' => false]) ?>
		</span>
			</div>
		</div>
	<?php else: ?>
		<p style="color:#C00;font-weight:bold"><?php echo $installMessage ?></p>
	<?php endif ?>

	<div class="file-list-body clearfix corner5">
		<?php if ($files): ?>
			<?php foreach($files as $file): ?>
				<?php $this->BcBaser->element('uploader_files/index_box', ['file' => $file, 'users' => $users]) ?>
			<?php endforeach ?>
		<?php else: ?>
			<p class="no-data"><?php echo __d('baser', 'ファイルが存在しません') ?></p>
		<?php endif ?>
	</div>

</div>
