<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] テーマファイル一覧
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$writable = true;
if((is_dir($fullpath) && !is_writable($fullpath)) || $theme == 'core'){
	$writable = false;
}
$bcBaser->js(array(
	'admin/jquery.baser_ajax_data_list', 
	'admin/jquery.baser_ajax_batch', 
	'admin/baser_ajax_data_list_config',
	'admin/baser_ajax_batch_config'
));
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


<div id="AjaxBatchUrl" style="display:none"><?php $bcBaser->url(array('controller' => 'theme_files', 'action' => 'ajax_batch', $theme, $type, $path)) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>

<!-- current -->
<div class="em-box align-left">現在の位置：<?php echo $currentPath ?>
<?php if(!$writable): ?>
	　<span style="color:#FF3300">[書込不可]</span>
<?php endif ?>
</div>

<div id="DataList"><?php $bcBaser->element('theme_files/index_list') ?></div>

<div class="submit">
<?php if($writable): ?>
	<?php echo $bcForm->create('ThemeFile', array('id' => 'ThemeFileUpload', 'url'=>array('action' => 'upload', $theme, $plugin, $type, $path), 'enctype' => 'multipart/form-data')) ?>
	<?php echo $bcForm->input('ThemeFile.file', array('type' => 'file')) ?>
	<?php echo $bcForm->end() ?>
	<?php $bcBaser->link('フォルダ新規作成', array('action' => 'add_folder', $theme, $type, $path), array('class' => 'btn-orange button')) ?>
<?php endif ?>
<?php if(($path || $type != 'etc') && $type != 'img' && $writable): ?>
	<?php $bcBaser->link('ファイル新規作成', array('action' => 'add', $theme, $type, $path), array('class' => 'btn-red button')) ?>
<?php endif ?>
</div>
