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
 * [ADMIN] エディタテンプレート一覧　行
 *
 * @var BcAppView $this
 */
?>


<tr<?php $this->BcListTable->rowClass(true, $data) ?>>
	<td class="bca-table-listup__tbody-td"><?php echo $data['EditorTemplate']['id'] ?></td>
	<td class="bca-table-listup__tbody-td">
		<?php if ($data['EditorTemplate']['image']): ?>
			<?php $this->BcBaser->img('/files/editor/' . $data['EditorTemplate']['image'], [
				'url' => ['action' => 'edit', $data['EditorTemplate']['id']],
				'alt' => $data['EditorTemplate']['name'],
				'title' => $data['EditorTemplate']['name'],
				'style' => 'float:left;margin-right:10px;height:36px'
			]) ?>
		<?php endif ?>
		<?php $this->BcBaser->link($data['EditorTemplate']['name'], ['action' => 'edit', $data['EditorTemplate']['id']], ['escape' => true]) ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo h($data['EditorTemplate']['description']) ?></td>

	<?php echo $this->BcListTable->dispatchShowRow($data) ?>

	<td class="bca-table-listup__tbody-td"
		style="white-space:nowrap"><?php echo $this->BcTime->format('Y-m-d', $data['EditorTemplate']['created']) ?><br>
		<?php echo $this->BcTime->format('Y-m-d', $data['EditorTemplate']['modified']) ?></td>
	<td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
		<?php $this->BcBaser->link('',
			['action' => 'edit', $data['EditorTemplate']['id']],
			['title' => __d('baser', '編集'), 'class' => ' bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']
		) ?>
		<?php $this->BcBaser->link('',
			['action' => 'ajax_delete', $data['EditorTemplate']['id']],
			['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']
		) ?>
	</td>
</tr>
