<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ブログカテゴリ 一覧
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

<?php if(!empty($dbDatas)): ?>

<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableBlogCategorys">
<tr>
	<th style="width:122px">操作</th>
    <th>NO</th>
	<th>ブログカテゴリ名</th>
    <th>ブログカテゴリタイトル</th>
	<th>登録日</th>
	<th>更新日</th>
</tr>
<?php $count=0; ?>
<?php foreach($dbDatas as $dbData): ?>
	<?php if ($count%2 === 0): ?>
		<?php $class=' class="altrow"'; ?>
	<?php else: ?>
		<?php $class=''; ?>
	<?php endif; ?>
	<tr<?php echo $class; ?>>
		<td class="operation-button">
            <?php $baser->link('確認',$blog->getCategoryUrl($dbData['BlogCategory']['id']),array('target'=>'_blank','class'=>'btn-green-s button-s')) ?>
			<?php $baser->link('編集',array('action'=>'edit', $blogContent['BlogContent']['id'], $dbData['BlogCategory']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php $baser->link('削除', array('action'=>'delete', $blogContent['BlogContent']['id'], $dbData['BlogCategory']['id']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？\n\nこのカテゴリに関連する記事は、どのカテゴリにも関連しない状態として残ります。', $dbData['BlogCategory']['name']),false); ?>
		</td>
        <td><?php echo $dbData['BlogCategory']['no'] ?></td>
        <td><?php $baser->link($dbData['BlogCategory']['name'],array('action'=>'edit', $blogContent['BlogContent']['id'], $dbData['BlogCategory']['id'])) ?></td>
		<td><?php echo $dbData['BlogCategory']['title'] ?></td>
        <td><?php echo $timeEx->format('Y-m-d',$dbData['BlogCategory']['created']); ?></td>
		<td><?php echo $timeEx->format('Y-m-d',$dbData['BlogCategory']['modified']); ?></td>
	</tr>
	<?php $count++; ?>
<?php endforeach; ?>
</table>
<?php else: ?>
<p class="no-data">データが見つかりませんでした。</p>
<?php endif; ?>

<div class="align-center"><?php $baser->link('新規登録',array('action'=>'add', $blogContent['BlogContent']['id']),array('class'=>'btn-red button')) ?></div>