<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ブログコンテンツ 一覧
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
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<h2><?php $baser->contentsTitle() ?></h2>

<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableBlogContents">
<tr>
	<th>操作</th>
    <th>NO</th>
	<th>ブログアカウント</th>
	<th>ブログタイトル</th>
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
            <?php $baser->link('確認','/'.$listData['BlogContent']['name'],array('target'=>'_blank','class'=>'btn-green-s button-s')) ?>
            <?php $baser->link('管理',array('controller'=>'blog_posts','action'=>'index', $listData['BlogContent']['id']),array('class'=>'btn-red-s button-s'),null,false) ?>
			<?php $baser->link('編集',array('action'=>'edit', $listData['BlogContent']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php $baser->link('削除', array('action'=>'delete', $listData['BlogContent']['id']), array('class'=>'btn-gray-s button-s'), sprintf('削除を行うと関連する記事やカテゴリは全て削除されてしまい元に戻す事はできません。\n本当に「%s」を削除してもいいですか？', $listData['BlogContent']['title']),false); ?>
		</td>
        <td><?php echo $listData['BlogContent']['id']; ?></td>
		<td><?php $baser->link($listData['BlogContent']['name'],array('action'=>'edit',$listData['BlogContent']['id'])); ?></td>
		<td><?php echo $listData['BlogContent']['title'] ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['BlogContent']['created']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['BlogContent']['modified']); ?></td>
	</tr>
	<?php $count++; ?>
<?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="6"><p class="no-data">データが見つかりませんでした。</p></td></tr>
<?php endif; ?>
</table>

<div class="align-center"><?php $baser->link('新規登録',array('action'=>'add'),array('class'=>'btn-red button')) ?></div>