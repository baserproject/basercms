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
 * [ADMIN] テーマファイル一覧
 */
$writable = true;
if ((is_dir($fullpath) && !is_writable($fullpath)) || $theme == 'core') {
	$writable = false;
}
$this->BcBaser->js([
	'admin/libs/jquery.baser_ajax_data_list',
	'admin/libs/jquery.baser_ajax_batch',
	'admin/libs/baser_ajax_data_list_config',
	'admin/libs/baser_ajax_batch_config'
]);
$params = explode('/', $path);
?>


<script type="text/javascript">
	$(function () {
		$("#ThemeFileFile").change(function () {
			$("#Waiting").show();
			$("#ThemeFileUpload").submit();
		});
		$.baserAjaxDataList.init();
		$.baserAjaxBatch.init({url: $("#AjaxBatchUrl").html()});
	});
</script>

<?php $this->BcBaser->element('submenus/theme_files'); ?>

<div id="AjaxBatchUrl"
	 style="display:none"><?php $this->BcBaser->url(array_merge(['controller' => 'theme_files', 'action' => 'ajax_batch', $theme, $type], $params)) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
	<div id="flashMessage" class="notice-message"></div>
</div>

<!-- current -->
<div class="em-box bca-current-box"><?php echo __d('baser', '現在の位置') ?>：<?php echo h($currentPath) ?>
	<?php if (!$writable): ?>
		　<span style="color:#FF3300">[<?php echo __d('baser', '書込不可') ?>]</span>
	<?php endif ?>
</div>

<div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('theme_files/index_list') ?></div>

<div class="bca-actions" data-bca-type="type2">
	<?php if ($writable): ?>
		<div class="bca-actions__form">
			<?php echo $this->BcForm->create('ThemeFile', ['id' => 'ThemeFileUpload', 'url' => array_merge(['action' => 'upload', $theme, $plugin, $type], $params), 'enctype' => 'multipart/form-data']) ?>
			<?php echo $this->BcForm->input('ThemeFile.file', ['type' => 'file']) ?>
			<?php echo $this->BcForm->end() ?>
		</div>
	<?php endif ?>
	<div class="bca-actions__adds">
		<?php if ($writable): ?>
			<?php $this->BcBaser->link('<i class="bca-icon--folder"></i> ' . __d('baser', 'フォルダ新規作成'), array_merge(['action' => 'add_folder', $theme, $type], $params),
				[
					'class' => 'bca-btn',
					'data-bca-btn-type' => 'add'
				]
			) ?>
		<?php endif ?>
		<?php if (($path || $type != 'etc') && $type != 'img' && $writable): ?>
			<?php $this->BcBaser->link('<i class="bca-icon--file"></i> ' . __d('baser', 'ファイル新規作成'), array_merge(['action' => 'add', $theme, $type], $params),
				[
					'class' => 'bca-btn',
					'data-bca-btn-type' => 'add'
				]
			) ?>
		<?php endif ?>
	</div>
</div>
