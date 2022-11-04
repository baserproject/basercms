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
 * [ADMIN] ユーザー一覧　行
 */
?>
<tr class="publish bca-table-listup__tbody-tr">
	<td
		class="bca-table-listup__tbody-td"
		style="width:140px;"
	>
		<?= Hash::get($row, 'Dblog.id') ?>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?= h(Hash::get($row, 'Dblog.name')) ?>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?php if ($row['Dblog']['user_id']): ?>
			<?php if ($this->BcBaser->getUserName($row['User'])): ?>
				<?php echo h($this->BcBaser->getUserName($row['User'])) ?>
			<?php else: ?>
				<?= Hash::get($row, 'User.name', '削除ユーザー') ?>
			<?php endif; ?>
		<?php else: ?>
			*system
		<?php endif; ?>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?= $this->BcTime->format('Y-m-d H:i:s', Hash::get($row, 'Dblog.created')) ?>
	</td>
</tr>
