<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] メールコンテンツ 一覧
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.mail.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableMailContents">
<tr>
	<th>操作</th>
    <th>NO</th>
	<th>メールフォームアカウント</th>
	<th>メールフォームタイトル</th>
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
            <?php echo $html->link('確認',array('admin'=>false,'plugin'=>'','controller'=>$listData['MailContent']['name'],'action'=>'index'),array('target'=>'_blank','class'=>'btn-green-s button-s')) ?>
            <?php echo $html->link('管理',array('controller'=>'mail_fields','action'=>'index', $listData['MailContent']['id']),array('class'=>'btn-red-s button-s'),null,false) ?>
			<?php echo $html->link('編集',array('action'=>'edit', $listData['MailContent']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php echo $html->link('削除', array('action'=>'delete', $listData['MailContent']['id']), array('class'=>'btn-gray-s button-s'), sprintf('本当に「%s」を削除してもいいですか？', $listData['MailContent']['title']),false); ?>
		</td>
        <td><?php echo $listData['MailContent']['id']; ?></td>
		<td><?php echo $html->link($listData['MailContent']['name'],array('action'=>'edit',$listData['MailContent']['id'])); ?></td>
		<td><?php echo $listData['MailContent']['title'] ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['MailContent']['created']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['MailContent']['modified']); ?></td>
	</tr>
	<?php $count++; ?>
<?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="6"><p class="no-data">データが見つかりませんでした。</p></td></tr>
<?php endif; ?>
</table>


<div class="align-center"><?php echo $html->link('新規登録',array('action'=>'add'),array('class'=>'btn-red button')) ?></div>