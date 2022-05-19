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
	<td class="row-tools">
		<div><?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_down.png', ['title' => __d('baser', 'ダウンロード'), 'alt' => __d('baser', 'ダウンロード')]), $data['link'], ['target' => '_blank']) ?></div>
	</td>
	<td>
		<?php echo h($data['title']) ?>
	</td>
	<td><?php echo h($data['version']) ?></td>
	<td><?php echo h($data['description']) ?></td>
	<td><?php $this->BcBaser->link($data['author'], $data['authorLink'], ['target' => '_blank', 'escape' => true]) ?></td>
	<td style="width:10%;white-space: nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['created']) ?><br/>
		<?php echo $this->BcTime->format('Y-m-d', $data['modified']) ?>
	</td>
</tr>
