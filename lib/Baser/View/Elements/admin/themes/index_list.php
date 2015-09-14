<?php
/**
 * [ADMIN] テーマ一覧　テーブル
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
?>


<script type="text/javascript">
$(function(){
	$(".theme-popup").colorbox({inline:true, width:"60%"});
});
</script>


<div id="CurrentTheme" class="clearfix">
	<h2>現在のテーマ</h2>
	<?php if ($currentTheme): ?>
		<div class="current-theme-left">
			<div class="theme-screenshot">
				<?php if ($currentTheme['screenshot']): ?>
					<?php $this->BcBaser->img('/theme/' . $currentTheme['name'] . '/screenshot.png', array('alt' => $currentTheme['title'])) ?>
				<?php else: ?>
					<?php $this->BcBaser->img('admin/no-screenshot.png', array('alt' => $currentTheme['title'])) ?>
				<?php endif ?>
			</div>
			<p class="row-tools">
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_manage.png', array('width' => 24, 'height' => 24, 'alt' => '管理', 'class' => 'btn')), array('controller' => 'theme_files', 'action' => 'index', $currentTheme['name']), array('title' => '管理')) ?>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $currentTheme['name']), array('title' => '編集')) ?>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $currentTheme['name']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
				<?php if (!$currentTheme['is_writable_pages']): ?>
				<br /><div class="error-message lastChild clearfix" style="clear:both">「Pages」フォルダに書き込み権限を与えてください。</div>
			<?php endif ?>
			</p>
		</div>

		<div class="theme-info">
			<p class="theme-name"><strong><?php echo $currentTheme['title'] ?></strong>&nbsp;(&nbsp;<?php echo $currentTheme['name'] ?>&nbsp;)</p>
			<p class="theme-version">バージョン：<?php echo $currentTheme['version'] ?></p>
			<p class="theme-author">制作者：<?php if (!empty($currentTheme['url']) && !empty($currentTheme['author'])): ?>
					<?php $this->BcBaser->link($currentTheme['author'], $currentTheme['url'], array('target' => '_blank')) ?>
				<?php else: ?>
					<?php echo $currentTheme['author'] ?>
				<?php endif ?>
			</p>
		</div>
		<br /><br />
		<?php if ($defaultDataPatterns && $this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->create('Theme', array('action' => 'load_default_data_pattern')) ?>
			<?php echo $this->BcForm->input('Theme.default_data_pattern', array('type' => 'select', 'options' => $defaultDataPatterns)) ?>
			<?php echo $this->BcForm->submit('初期データ読込', array('class' => 'button-small', 'div' => false, 'id' => 'BtnLoadDefaultDataPattern')) ?>
			<?php echo $this->BcForm->end() ?>
		<?php endif ?>
		<br /><br /><br /><br />
		<div class="theme-description"><?php echo nl2br($currentTheme['description']) ?></div>
	<?php else: ?>
		<p>現在、テーマが選択されていません。</p>
	<?php endif ?>
</div>

<ul class="list-panel clearfix">
	<?php if (!empty($datas)): ?>
		<?php foreach ($datas as $data): ?>
			<?php $this->BcBaser->element('themes/index_row', array('data' => $data)) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<?php if(strtotime('2014-03-31 17:00:00') >= time()): ?>
		<li class="no-data">変更できるテーマがありません。<br /><a href="http://basercms.net/themes/index" target="_blank">baserCMSの公式サイト</a>では無償のテーマが公開されています。</li>
		<?php else: ?>
		<li class="no-data">変更できるテーマがありません。<br /><a href="https://market.basercms.net/" target="_blank">baserマーケット</a>でテーマをダウンロードしましょう。</li>
		<?php endif ?>
	<?php endif; ?>
</ul>