<?php
/**
 * [ADMIN] テーマファイル一覧　テーブル
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


<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
<thead>
	<tr>
		<th style="width:160px" class="list-tool">
			<?php if ($this->BcBaser->isAdminUser() && $theme != 'core'): ?>
			<div>
				<?php echo $this->BcForm->checkbox('ListTool.checkall', array('title' => '一括選択')) ?>
				<?php echo $this->BcForm->input('ListTool.batch', array('type' => 'select', 'options' => array('del' => '削除'), 'empty' => '一括処理')) ?>
				<?php echo $this->BcForm->button('適用', array('id' => 'BtnApplyBatch', 'disabled' => 'disabled')) ?>
				<?php if ($path): ?>
					<?php $this->BcBaser->link($this->BcBaser->getImg('admin/up.gif', array('alt' => '上へ移動')), array('action' => 'index', $theme, $plugin, $type, dirname($path)), array('title' => '上へ移動')) ?>
				<?php endif ?>
			</div>
			<?php endif ?>
		</th>
		<th>フォルダ名／テーマファイル名</th>
	</tr>
</thead>
<tbody>
	<?php if (!empty($themeFiles)): ?>
		<?php foreach ($themeFiles as $data): ?>
			<?php $this->BcBaser->element('theme_files/index_row', array('data' => $data)) ?>
		<?php endforeach; ?>
	<?php else: ?>
	<tr>
		<td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
	<?php endif; ?>
</tbody>
</table>