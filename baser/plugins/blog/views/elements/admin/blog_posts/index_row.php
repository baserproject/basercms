<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ記事 一覧　行
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
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
$classies = array();
if (!$blog->allowPublish($data)) {
	$classies = array('unpublish', 'disablerow');
} else {
	$classies = array('publish');
}
$class=' class="'.implode(' ', $classies).'"';
?>


<tr<?php echo $class; ?>>
	<td class="row-tools">
<?php if($bcBaser->isAdminUser()): ?>
		<?php echo $bcForm->checkbox('ListTool.batch_targets.'.$data['BlogPost']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['BlogPost']['id'])) ?>
<?php endif ?>		
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_unpublish.png', array('width' => 24, 'height' => 24, 'alt' => '非公開', 'class' => 'btn')), array('action' => 'ajax_unpublish', $data['BlogContent']['id'], $data['BlogPost']['id']), array('title' => '非公開', 'class' => 'btn-unpublish')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_publish.png', array('width' => 24, 'height' => 24, 'alt' => '公開', 'class' => 'btn')), array('action' => 'ajax_publish', $data['BlogContent']['id'], $data['BlogPost']['id']), array('title' => '公開', 'class' => 'btn-publish')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_check.png', array('width' => 24, 'height' => 24, 'alt' => '確認', 'class' => 'btn')), '/'.$data['BlogContent']['name'].'/archives/'.$data['BlogPost']['no'], array('title' => '確認', 'target' => '_blank')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_edit.png', array('width' => 24, 'height' => 24, 'alt' => '編集', 'class' => 'btn')), array('action' => 'edit', $data['BlogContent']['id'], $data['BlogPost']['id']), array('title' => '編集')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_copy.png', array('width' => 24, 'height' => 24, 'alt' => 'コピー', 'class' => 'btn')), array('action' => 'ajax_copy', $data['BlogContent']['id'], $data['BlogPost']['id']), array('title' => 'コピー', 'class' => 'btn-copy')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $data['BlogContent']['id'], $data['BlogPost']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
	</td>
	<td><?php echo $data['BlogPost']['no']; ?></td>
	<td>
<?php if(!empty($data['BlogCategory']['title'])): ?>
		<?php echo $data['BlogCategory']['title']; ?>
<?php endif; ?>
<?php if($data['BlogContent']['tag_use'] && !empty($data['BlogTag'])): ?>
			<?php $tags = Set::extract('/name', $data['BlogTag']) ?>
			<span class="tag"><?php echo implode('</span><span class="tag">',$tags) ?></span>
<?php endif ?>
		<br />
		<?php $bcBaser->link($data['BlogPost']['name'],array('action'=>'edit', $data['BlogContent']['id'], $data['BlogPost']['id'])) ?>
	</td>
	<td>
		<?php echo $bcBaser->getUserName($data['User']) ?>
	</td>
	<td style="text-align:center"><?php echo $bcText->booleanMark($data['BlogPost']['status']); ?></td>
<?php if($data['BlogContent']['comment_use']): ?>
	<td>
		<?php $comment = count($data['BlogComment']) ?>
	<?php if($comment): ?>
		<?php $bcBaser->link($comment,array('controller'=>'blog_comments','action'=>'index',$data['BlogContent']['id'],$data['BlogPost']['id'])) ?>
	<?php else: ?>
		<?php echo $comment ?>
	<?php endif ?>
	</td>
<?php endif ?>
	<td style="white-space:nowrap">
		<?php echo $bcTime->format('Y-m-d',$data['BlogPost']['posts_date']); ?><br />
		<?php echo $bcTime->format('Y-m-d',$data['BlogPost']['modified']); ?>
	</td>
</tr>
