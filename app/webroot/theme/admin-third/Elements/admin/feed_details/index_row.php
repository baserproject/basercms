<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Feed.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] フィード設定　行
 */
?>


<tr>
	<td class="bca-table-listup__tbody-td" class="row-tools bca-table-listup__tbody-td">
		<?php if ($this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->input('ListTool.batch_targets.' . $data['id'], ['type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">チェックする</span>', 'class' => 'batch-targets bca-checkbox__input', 'value' => $data['id']]) ?>
		<?php endif ?>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?php if ($data['url']): ?>
			<?php $this->BcBaser->link($data['name'], ['controller' => 'feed_details', 'action' => 'edit', $this->BcForm->value('FeedConfig.id'), $data['id']], ['escape' => true]) ?>
		<?php else: ?>
			<?php echo h($data['name']) ?>
		<?php endif; ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo $data['category_filter'] ?></td>
	<td class="bca-table-listup__tbody-td"><?php echo $this->BcText->listValue('FeedDetail.cache_time', $data['cache_time']) ?></td>
	<?php echo $this->BcListTable->dispatchShowRow($data) ?>
	<td class="bca-table-listup__tbody-td"><?php echo $this->BcTime->format('Y-m-d', $data['created']) ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['modified']) ?></td>
	<td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
		<?php $this->BcBaser->link('', $data['url'], ['title' => __d('baser', '確認'), 'target' => '_blank', 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'preview', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['controller' => 'feed_details', 'action' => 'edit', $this->BcForm->value('FeedConfig.id'), $data['id']], ['title' => __d('baser', '編集'), 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']) ?>
		<?php $this->BcBaser->link('', ['controller' => 'feed_details', 'action' => 'ajax_delete', $this->BcForm->value('FeedConfig.id'), $data['id']], ['title' => __d('baser', '削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
	</td>
</tr>
