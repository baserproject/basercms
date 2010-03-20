<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] パーミッション一覧
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="PermissionsTable">
<tr>
	<th>操作</th>
    <th>NO</th>
	<th>設定名</th>
	<th>設定</th>
	<th>登録日</th>
	<th>更新日</th>
</tr>
<?php if(!empty($listDatas)): ?>
<?php $count=0; ?>
<?php foreach($listDatas as $listData): ?>
	<?php if ($count%2 === 0): ?>
		<?php $class=' class="altrow"'; ?>
	<?php else: ?>
		<?php $class=''; ?>
	<?php endif; ?>
	<tr<?php echo $class; ?>>
		<td class="operation-button">
			<?php echo $html->link('編集',array('action'=>'edit', $listData['Permission']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php if($listData['Permission']['name']!='admins'): ?>
				<?php echo $html->link('削除', array('action'=>'delete', $listData['Permission']['id']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？', $listData['Permission']['name']),false); ?>
			<?php endif ?>
		</td>
        <td><?php echo $listData['Permission']['id']; ?></td>
		<td><?php echo $html->link($listData['Permission']['name'],array('action'=>'edit', $listData['Permission']['id'])); ?></td>
		<td><?php echo $listData['Permission']['setting']; ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['Permission']['created']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['Permission']['modified']); ?></td>
	</tr>
	<?php $count++; ?>
<?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td></tr>
<?php endif; ?>
</table>

<div class="align-center"><?php echo $html->link('新規登録',array('action'=>'add'),array('class'=>'btn-red button')) ?></div>