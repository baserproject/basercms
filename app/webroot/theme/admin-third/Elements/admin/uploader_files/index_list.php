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
/**
 * @var BcAppView $this
 */
// IE文字化け対策
header('Content-type: text/html; charset=utf-8');
$users = $this->BcForm->getControlSource("UploaderFile.user_id");
$uploaderCategories = $this->BcForm->getControlSource("UploaderFile.uploader_category_id");
$this->passedArgs['action'] = 'ajax_list';
//==============================================================================
// Ajaxで呼び出される事が前提の為インラインで呼びだし
//==============================================================================
$this->BcBaser->js([
	'admin/vendors/jquery.upload-1.0.0.min',
	'Uploader.admin/uploader_files/uploader_list_table'
]);
?>


<div id="DivTableList">

	<?php if ($listId): ?>
		<div id="UploaderForm">
			<?php if (!$installMessage): ?>
				<div>
					<?php if ($uploaderCategories): ?>
						<?php echo $this->BcForm->input('UploaderFile.uploader_category_id', ['type' => 'select', 'options' => $uploaderCategories, 'empty' => __d('baser', 'カテゴリ指定なし'), 'id' => 'UploaderFileUploaderCategoryId' . $listId, 'style' => 'width:100px']) ?>&nbsp;
					<?php endif ?>
					<span id="SpanUploadFile<?php echo $listId ?>">
			<?php echo $this->BcForm->input('UploaderFile.file', ['type' => 'file', 'id' => 'UploaderFileFile' . $listId, 'class' => 'uploader-file-file', 'div' => false]) ?>
		</span>
				</div>
			<?php endif ?>
		</div>
	<?php endif ?>


	<?php if ($installMessage): ?>
		<p style="color:#C00;font-weight:bold"><?php echo $installMessage ?></p>
	<?php endif ?>

	<div class="bca-data-list__top bca-list-head">
		<?php if (!$listId): ?>
			<div id="UploaderForm">
				<?php if (!$installMessage): ?>
					<div>
						<label for="UploaderFileUploaderCategoryId"><?php echo __d('baser', 'アップロード') ?></label>
						<?php if ($uploaderCategories): ?>
							<?php echo $this->BcForm->input('UploaderFile.uploader_category_id', ['type' => 'select', 'options' => $uploaderCategories, 'empty' => __d('baser', 'カテゴリ指定なし'), 'id' => 'UploaderFileUploaderCategoryId' . $listId]) ?>
						<?php endif ?>
						<span id="SpanUploadFile<?php echo $listId ?>">
					<?php echo $this->BcForm->input('UploaderFile.file', ['type' => 'file', 'id' => 'UploaderFileFile' . $listId, 'class' => 'uploader-file-file', 'div' => false]) ?>
				</span>
					</div>
				<?php endif ?>
			</div>
		<?php endif ?>
		<div class="bca-data-list__sub">
			<!-- pagination -->
			<?php $this->BcBaser->element('pagination') ?>
		</div>
	</div>

	<div class="file-list-body clearfix corner5">

		<table class="bca-table-listup">

			<thead class="bca-table-listup__thead">
			<tr>
				<th class="bca-table-listup__thead-th">No</th>
				<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'イメージ') ?></th>
				<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'カテゴリ') ?></th>
				<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'ファイル名') ?>
					<br><?php echo __d('baser', '説明文') ?></th>
				<th class="bca-table-listup__thead-th"><?php echo __d('baser', '公開状態') ?></th>
				<th class="bca-table-listup__thead-th"><?php echo __d('baser', '投稿者') ?></th>
				<th class="bca-table-listup__thead-th"><?php echo __d('baser', '投稿日') ?>
					<br><?php echo __d('baser', '編集日') ?></th>
				<?php if (!$listId): ?>
					<th class="bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
				<?php endif ?>
			</tr>
			</thead>
			<?php if ($files): ?>
				<tbody>
				<?php foreach($files as $file): ?>
					<?php $this->BcBaser->element('uploader_files/index_row', ['file' => $file, 'users' => $users, 'uploaderCategories' => $uploaderCategories]) ?>
				<?php endforeach ?>
				</tbody>
			<?php else: ?>
				<tbody>
				<tr>
					<td colspan="8" class="no-data"><?php echo __d('baser', 'ファイルが存在しません') ?></td>
				</tr>
				</tbody>
			<?php endif ?>
		</table>

	</div>

	<div class="bca-data-list__bottom">
		<div class="bca-data-list__sub">
			<!-- pagination -->
			<?php $this->BcBaser->element('pagination') ?>
			<!-- list-num -->
			<?php $this->BcBaser->element('list_num') ?>
		</div>
	</div>

</div>
