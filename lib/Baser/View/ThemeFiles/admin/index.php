<?php
/**
 * [ADMIN] テーマファイル一覧
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$writable = true;
if ((is_dir($fullpath) && !is_writable($fullpath)) || $theme == 'core') {
	$writable = false;
}
$this->BcBaser->js(array(
	'admin/jquery.baser_ajax_data_list',
	'admin/jquery.baser_ajax_batch',
	'admin/baser_ajax_data_list_config',
	'admin/baser_ajax_batch_config'
));
$params = explode('/', $path);
?>


<script type="text/javascript">
$(function(){
	$("#ThemeFileFile").change(function(){
		$("#Waiting").show();
		$("#ThemeFileUpload").submit();
	});
	$.baserAjaxDataList.init();
	$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});
});
</script>


<div id="AjaxBatchUrl" style="display:none"><?php $this->BcBaser->url(array_merge(array('controller' => 'theme_files', 'action' => 'ajax_batch', $theme, $type), $params)) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none"><div id="flashMessage" class="notice-message"></div></div>

<!-- current -->
<div class="em-box align-left">現在の位置：<?php echo $currentPath ?>
	<?php if (!$writable): ?>
		　<span style="color:#FF3300">[書込不可]</span>
	<?php endif ?>
</div>

<div id="DataList"><?php $this->BcBaser->element('theme_files/index_list') ?></div>

<div class="submit">
	<?php if ($writable): ?>
		<?php echo $this->BcForm->create('ThemeFile', array('id' => 'ThemeFileUpload', 'url' => array_merge(array('action' => 'upload', $theme, $plugin, $type), $params), 'enctype' => 'multipart/form-data')) ?>
		<?php echo $this->BcForm->input('ThemeFile.file', array('type' => 'file')) ?>
		<?php echo $this->BcForm->end() ?>
		<?php $this->BcBaser->link('フォルダ新規作成', array_merge(array('action' => 'add_folder', $theme, $type), $params), array('class' => 'btn-orange button')) ?>
	<?php endif ?>
	<?php if (($path || $type != 'etc') && $type != 'img' && $writable): ?>
		<?php $this->BcBaser->link('ファイル新規作成', array_merge(array('action' => 'add', $theme, $type), $params), array('class' => 'btn-red button')) ?>
	<?php endif ?>
</div>
