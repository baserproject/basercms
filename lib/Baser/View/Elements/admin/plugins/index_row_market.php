<?php
/**
 * [ADMIN] プラグイン一覧　行
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<td class="row-tools">
		<div><?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_down_on.png', array('title' => 'ダウンロード', 'alt' => 'ダウンロード')), $data['link'], array('target' => '_blank')) ?></div>
	</td>
	<td>
		<?php echo $data['title'] ?>
	</td>
	<td><?php echo $data['version'] ?></td>
	<td><?php echo $data['description'] ?></td>
	<td><?php $this->BcBaser->link($data['author'], $data['authorLink'], array('target' => '_blank')) ?></td>
	<td style="width:10%;white-space: nowrap">
		<?php echo $this->BcTime->format('Y-m-d', $data['created']) ?><br />
		<?php echo $this->BcTime->format('Y-m-d', $data['modified']) ?>
	</td>
</tr>