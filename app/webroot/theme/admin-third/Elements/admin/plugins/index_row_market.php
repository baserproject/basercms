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
 * [ADMIN] プラグイン一覧　行
 */
?>


<tr>
	<td class="row-tools bca-table-listup__tbody-td">
		<div>
			<?php $this->BcBaser->link('', $data['link'], [
				'target' => '_blank',
				'aria-label' => __d('baser', 'ダウンロードサイトへ移動する'),
				'title' => __d('baser', 'ダウンロードサイトへ移動する'),
				'class' => 'btn-download bca-btn-icon',
				'data-bca-btn-type' => 'download',
				'data-bca-btn-size' => 'lg'
			]) ?>
		</div>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?php echo h($data['title']) ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo h($data['version']) ?></td>
	<td class="bca-table-listup__tbody-td"><?php echo nl2br(h($data['description'])) ?></td>
	<td class="bca-table-listup__tbody-td"><?php $this->BcBaser->link($data['author'], $data['authorLink'], ['target' => '_blank', 'escape' => true]) ?></td>
	<td class="bca-table-listup__tbody-td" style="width:10%;white-space: nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['created']) ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['modified']) ?>
	</td>
</tr>
