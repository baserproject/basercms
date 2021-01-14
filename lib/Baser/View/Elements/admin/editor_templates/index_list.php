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
 * [ADMIN] エディタテンプレート一覧　テーブル
 *
 * @var \BcAppView $this
 */
$this->BcListTable->setColumnNumber(5);
?>


<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
	<thead>
	<tr>
		<th style="width:140px" class="list-tool">
			<div>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', ['alt' => __d('baser', '新規追加')]) . __d('baser', '新規追加'), ['action' => 'add']) ?>
				　
			</div>
		</th>
		<th>NO</th>
		<th><?php echo __d('baser', 'テンプレート名') ?></th>
		<th><?php echo __d('baser', '説明文') ?></th>
		<?php echo $this->BcListTable->dispatchShowHead() ?>
		<th><?php echo __d('baser', '登録日') ?><br><?php echo __d('baser', '更新日') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php if (!empty($datas)): ?>
		<?php foreach($datas as $data): ?>
			<?php $this->BcBaser->element('editor_templates/index_row', ['data' => $data]) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>"><p
					class="no-data"><?php echo __d('baser', 'データが見つかりませんでした。') ?></p></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
