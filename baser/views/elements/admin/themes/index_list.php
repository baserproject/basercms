<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] テーマ一覧　テーブル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<script type="text/javascript">
$(function(){
	$(".theme-popup").colorbox({inline:true, width:"60%"});
});
</script>


<div id="CurrentTheme" class="clearfix">
	<h2>現在のテーマ</h2>
	<div class="current-theme-left">
		<div class="theme-screenshot">
			<?php if($currentTheme['screenshot']): ?>
			<?php $bcBaser->img('/themed/'.$currentTheme['name'].'/screenshot.png',array('alt'=>$currentTheme['title'])) ?>
			<?php else: ?>
			<?php $bcBaser->img('admin/no-screenshot.png',array('alt'=>$currentTheme['title'])) ?>
			<?php endif ?>
		</div>
		<p class="row-tools">
			<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_manage.png', array('width' => 24, 'height' => 24, 'alt' => '管理', 'class' => 'btn')), array('controller'=>'theme_files','action' => 'index', $currentTheme['name']), array('title' => '管理')) ?>
			<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $currentTheme['name']), array('title' => '編集')) ?>
			<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $currentTheme['name']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
	<?php if(!$currentTheme['is_writable_pages']): ?>
			<br /><div class="error-message lastChild clearfix" style="clear:both">「pages」フォルダに書き込み権限を与えてください。</div>
	<?php endif ?>
		</p>
	</div>
		
	<div class="theme-info">
		<p class="theme-name"><strong><?php echo $currentTheme['title'] ?></strong>&nbsp;(&nbsp;<?php echo $currentTheme['name'] ?>&nbsp;)</p>
		<p class="theme-version">バージョン：<?php echo $currentTheme['version'] ?></p>
		<p class="theme-author">制作者：<?php if(!empty($currentTheme['url']) && !empty($currentTheme['author'])): ?>
			<?php $bcBaser->link($currentTheme['author'],$currentTheme['url'],array('target'=>'_blank')) ?>
			<?php else: ?>
			<?php echo $currentTheme['author'] ?>
			<?php endif ?>
		</p>
	</div>
	<br /><br />
<?php if($defaultDataPatterns && $bcBaser->isAdminUser()): ?>
	<?php echo $bcForm->create('Theme', array('action' => 'load_default_data_pattern')) ?>
	<?php echo $bcForm->input('Theme.default_data_pattern', array('type' => 'select', 'options' => $defaultDataPatterns)) ?>
	<?php echo $bcForm->submit('初期データ読込', array('class' => 'button-small', 'div' => false, 'id' => 'BtnLoadDefaultDataPattern')) ?>
	<?php echo $bcForm->end() ?>
<?php endif ?>
	<br /><br /><br /><br />
	<div class="theme-description"><?php echo nl2br($currentTheme['description']) ?></div>
</div>

<ul class="list-panel clearfix">
<?php if(!empty($datas)): ?>
	<?php foreach($datas as $data): ?>
		<?php $bcBaser->element('themes/index_row', array('data' => $data)) ?>
	<?php endforeach; ?>
<?php else: ?>
	<li class="no-data">変更できるテーマがありません。<br /><a href="http://basercms.net/themes/index" target="_blank">baserCMSの公式サイト</a>では無償のテーマが公開されています。</li>
<?php endif; ?>
</ul>