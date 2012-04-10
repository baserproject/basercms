<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ユーザー一覧　行
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserdatas/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<td class="row-tools">
		<?php $baser->link($baser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['User']['id']), array('title' => '編集')) ?>
		<?php $baser->link($baser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['User']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?></td>
	<td><?php echo $data['User']['id'] ?></td>
	<td><?php $baser->link($data['User']['name'],array('action'=>'edit', $data['User']['id'])) ?></td>
	<td><?php echo $textEx->listValue('User.user_group_id',$data['User']['user_group_id']); ?><br />
		<?php echo $data['User']['real_name_1']; ?>&nbsp;<?php echo $data['User']['real_name_2'] ?></td>
	<td><?php echo $timeEx->format('Y-m-d',$data['User']['created']) ?><br />
		<?php echo $timeEx->format('Y-m-d',$data['User']['modified']) ?></td>
</tr>