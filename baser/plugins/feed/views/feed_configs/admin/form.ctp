<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] フィード設定 フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.feed.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$baser->js(array(
	'admin/jquery.baser_ajax_data_list', 
	'admin/jquery.baser_ajax_batch', 
	'admin/baser_ajax_data_list_config',
	'admin/baser_ajax_batch_config'
));
?>

<script type="text/javascript">
$(function(){
	$("#EditTemplate").click(function(){
		if(confirm('フィード設定を保存して、テンプレート '+$("#FeedConfigTemplate").val()+' の編集画面に移動します。よろしいですか？')){
			$("#FeedConfigEditTemplate").val(true);
			$("#FeedConfigEditForm").submit();
		}
	});
	$.baserAjaxDataList.init();
	$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});
});
</script>

<div id="AjaxBatchUrl" style="display:none"><?php $baser->url(array('controller' => 'feed_details', 'action' => 'ajax_batch')) ?></div>

<div class="section">
	<h2>基本項目</h2>

	<?php echo $formEx->create('FeedConfig') ?>

	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
	<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('FeedConfig.id', 'NO') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->value('FeedConfig.id') ?>
				<?php echo $formEx->input('FeedConfig.id', array('type' => 'hidden')) ?>
			</td>
		</tr>
	<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('FeedConfig.name', 'フィード設定名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('FeedConfig.name', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('FeedConfig.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>日本語が利用できます。</li>
						<li>識別でき、わかりやすい設定名を入力します。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('FeedConfig.display_number', '表示件数') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('FeedConfig.display_number', array('type' => 'text', 'size' => 10, 'maxlength' => 3)) ?>件
				<?php echo $formEx->error('FeedConfig.display_number') ?>
			</td>
		</tr>
	</table>
</div>

<div class="section">
	<h2 class="btn-slide-form"><a href="javascript:void(0)" id="FormOption">オプション</a></h2>
	<table cellpadding="0" cellspacing="0" class="form-table slide-body" id="FormOptionBody">
		<tr>
			<th class="col-head"><?php echo $formEx->label('FeedConfig.feed_title_index', 'フィードタイトルリスト') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('FeedConfig.feed_title_index', array('type' => 'textarea', 'cols' => 36, 'rows' => 3)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpFeedTitleIndex', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('FeedConfig.feed_title_index') ?>
				<div id="helptextFeedTitleIndex" class="helptext">
					<ul>
						<li>一つの表示フィードに対し、複数のフィードを読み込む際、フィードタイトルを表示させたい場合は、フィードタイトルを「|」で区切って入力してください。</li>
						<li>テンプレート上で、「feed_title」として参照できるようになります。</li>
						<li>また、先頭から順に「feed_title_no」としてイ