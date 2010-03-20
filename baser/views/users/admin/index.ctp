<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ユーザー一覧
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
<!-- pagination -->
<?php $baser->pagination('default',array(),null,false) ?>


<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableUsers">
<tr>
	<th>操作</th>
    <th><?php echo $paginator->sort(array('asc'=>'NO ▼','desc'=>'NO ▲'),'id'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'アカウント名 ▼','desc'=>'アカウント名 ▲'),'name'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'姓 ▼','desc'=>'姓 ▲'),'real_name_1'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'名 ▼','desc'=>'名 ▲'),'real_name_2'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'グループ ▼','desc'=>'グループ ▲'),'user_group_id'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'登録日 ▼','desc'=>'登録日 ▲'),'created'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'更新日 ▼','desc'=>'更新日 ▲'),'modified'); ?></th>
</tr>
<?php if(!empty($users)): ?>
<?php $count=0; ?>
<?php foreach($users as $user): ?>
	<?php if ($count%2 === 0): ?>
		<?php $class=' class="altrow"'; ?>
	<?php else: ?>
		<?php $class=''; ?>
	<?php endif; ?>
	<tr<?php echo $class; ?>>
		<td class="operation-button">
			<?php echo $html->link('編集',array('action'=>'edit', $user['User']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php echo $html->link('削除', array('action'=>'delete', $user['User']['id']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？', $user['User']['name']),false); ?>
		</td>
        <td><?php echo $user['User']['id']; ?></td>
		<td><?php echo $html->link($user['User']['name'],array('action'=>'edit', $user['User']['id'])); ?></td>
		<td><?php echo $user['User']['real_name_1']; ?></td>
		<td><?php echo $user['User']['real_name_2']; ?></td>
		<td><?php echo $textEx->listValue('User.user_group_id',$user['User']['user_group_id']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$user['User']['created']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$user['User']['modified']); ?></td>
	</tr>
	<?php $count++; ?>
<?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td></tr>
<?php endif; ?>
</table>


<?php $baser->pagination('default',array(),null,false) ?>


<div class="align-center"><?php echo $html->link('新規登録',array('action'=>'add'),array('class'=>'btn-red button')) ?></div>