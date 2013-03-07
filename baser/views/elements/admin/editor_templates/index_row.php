<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] エディタテンプレート一覧　行
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserdatas/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
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
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['EditorTemplate']['id']), array('title' => '編集')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['EditorTemplate']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td><?php echo $data['EditorTemplate']['id'] ?></td>
	<td>
<?php if($data['EditorTemplate']['image']): ?>
		<?php $bcBaser->img('/files/editor/' . $data['EditorTemplate']['image'], array('url' => array('action'=>'edit', $data['EditorTemplate']['id']), 'alt' => $data['EditorTemplate']['name'], 'title' => $data['EditorTemplate']['name'], 'style' => 'float:left;margin-right:10px;height:36px')) ?>
<?php endif ?>
		<?php $bcBaser->link($data['EditorTemplate']['name'], array('action'=>'edit', $data['EditorTemplate']['id'])) ?>
	</td>
	<td><?php echo $data['EditorTemplate']['description']; ?></td>
	<td style="white-space:nowrap"><?php echo $bcTime->format('Y-m-d',$data['EditorTemplate']['created']) ?><br />
		<?php echo $bcTime->format('Y-m-d',$data['EditorTemplate']['modified']) ?></td>
</tr>