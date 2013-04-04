<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ記事コメント 一覧　行
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
if (!$data['BlogComment']['status']) {
	$class=' class="disablerow unpublish"';
} else {
	$class=' class="publish"';
}
?>


<tr<?php echo $class; ?>>
	<td class="row-tools">
<?php if($bcBaser->isAdminUser()): ?>
		<?php echo $bcForm->checkbox('ListTool.batch_targets.'.$data['BlogComment']['id'], array('type' => 'checkbox', 'class' => 'batch-targets', 'value' => $data['BlogComment']['id'])) ?>
<?php endif ?>
<?php if(!empty($this->params['pass'][1])): ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_unpublish.png', array('width' => 24, 'height' => 24, 'alt' => '非公開', 'class' => 'btn')), array('action' => 'ajax_unpublish', $blogContent['BlogContent']['id'], $data['BlogComment']['blog_post_id'], $data['BlogComment']['id']), array('title' => '非公開', 'class' => 'btn-unpublish')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_publish.png', array('width' => 24, 'height' => 24, 'alt' => '公開', 'class' => 'btn')), array('action' => 'ajax_publish', $blogContent['BlogContent']['id'], $data['BlogComment']['blog_post_id'], $data['BlogComment']['id']), array('title' => '公開', 'class' => 'btn-publish')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $blogContent['BlogContent']['id'], $data['BlogComment']['blog_post_id'], $data['BlogComment']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
<?php else: ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_unpublish.png', array('width' => 24, 'height' => 24, 'alt' => '非公開', 'class' => 'btn')), array('action' => 'ajax_unpublish', $blogContent['BlogContent']['id'], 0, $data['BlogComment']['id']), array('title' => '非公開', 'class' => 'btn-unpublish')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_publish.png', array('width' => 24, 'height' => 24, 'alt' => '公開', 'class' => 'btn')), array('action' => 'ajax_publish', $blogContent['BlogContent']['id'], 0, $data['BlogComment']['id']), array('title' => '公開', 'class' => 'btn-publish')) ?>
		<?php $bcBaser->link($bcBaser->getImg('admin/icn_tool_delete.png', array('width' => 24, 'height' => 24, 'alt' => '削除', 'class' => 'btn')), array('action' => 'ajax_delete', $blogContent['BlogContent']['id'], 0, $data['BlogComment']['id']), array('title' => '削除', 'class' => 'btn-delete')) ?>
<?php endif ?>
	</td>
	<td><?php echo $data['BlogComment']['no'] ?></td>
	<td>
<?php if(!empty($data['BlogComment']['url'])): ?>
		<?php $bcBaser->link($data['BlogComment']['name'], $data['BlogComment']['url'], array('target' => '_blank')) ?>
<?php else: ?>
		<?php echo $data['BlogComment']['name'] ?>
<?php endif ?>
	</td>
	<td>
<?php if(!empty($data['BlogComment']['email'])): ?>
		<?php $bcBaser->link($data['BlogComment']['email'], 'mailto:'.$data['BlogComment']['email']) ?>
<?php endif; ?>
		<br />
		<?php echo $bcText->autoLinkUrls($data['BlogComment']['url']) ?>
	</td>
	<td>
		<strong>
		<?php $bcBaser->link($data['BlogPost']['name'], array('controller' => 'blog_posts', 'action' => 'edit', $blogContent['BlogContent']['id'], $data['BlogPost']['id'])) ?>
		</strong><br />
		<?php echo nl2br($bcText->autoLinkUrls($data['BlogComment']['message'])) ?>
	</td>
	<td style="white-space: nowrap">
		<?php echo $bcTime->format('Y-m-d',$data['BlogComment']['created']); ?><br />
		<?php echo $bcTime->format('Y-m-d',$data['BlogComment']['modified']); ?>
	</td>
</tr>
