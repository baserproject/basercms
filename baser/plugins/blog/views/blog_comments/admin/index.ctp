<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ブログ記事 一覧
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
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<!-- pagination -->
<?php echo $this->renderElement('paginations'.DS.'default'); ?>


<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableBlogComments">
<tr>
	<th>操作</th>
	<th><?php echo $paginator->sort(array('asc'=>'NO ▼','desc'=>'NO ▲'),'no'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'投稿者 ▼','desc'=>'投稿者 ▲'),'name'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'メール ▼','desc'=>'投稿者 ▲'),'mail'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'メッセージ ▼','desc'=>'メッセージ ▲'),'comment'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'投稿日 ▼','desc'=>'投稿日 ▲'),'posts_date'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'更新日 ▼','desc'=>'更新日 ▲'),'modified'); ?></th>
</tr>
<?php if(!empty($dbDatas)): ?>
<?php $count=0; ?>
<?php foreach($dbDatas as $dbData): ?>
	<?php if (!$dbData['BlogComment']['status']): ?>
		<?php $class=' class="disablerow"'; ?>
	<?php elseif ($count%2 === 0): ?>
		<?php $class=' class="altrow"'; ?>
	<?php else: ?>
		<?php $class=''; ?>
	<?php endif; ?>
	<tr<?php echo $class; ?>>
       <td class="operation-button">
            <?php if(!empty($this->params['pass'][1])): ?>
                <?php if($dbData['BlogComment']['status']): ?>
                    <?php echo $html->link('非公開', array('action'=>'unpublish', $blogContent['BlogContent']['id'],$dbData['BlogComment']['blog_post_id'],$dbData['BlogComment']['id']), array('class'=>'btn-orange-s button-s'), sprintf('NO %s を非公開にします。よろしいですか？', $dbData['BlogComment']['no']),false); ?>
                <?php else: ?>
                    <?php echo $html->link('公開', array('action'=>'publish', $blogContent['BlogContent']['id'],$dbData['BlogComment']['blog_post_id'],$dbData['BlogComment']['id']), array('class'=>'btn-green-s button-s'), sprintf('NO %s を公開します。よろしいですか？', $dbData['BlogComment']['no']),false); ?>
                <?php endif ?>
                <?php echo $html->link('削除', array('action'=>'delete', $blogContent['BlogContent']['id'],$dbData['BlogComment']['blog_post_id'],$dbData['BlogComment']['id']), array('class'=>'btn-gray-s button-s'), sprintf('NO %s を本当に削除してもいいですか？', $dbData['BlogComment']['no']),false); ?>
            <?php else: ?>
                <?php if($dbData['BlogComment']['status']): ?>
                    <?php echo $html->link('非公開', array('action'=>'unpublish', $blogContent['BlogContent']['id'],0,$dbData['BlogComment']['id']), array('class'=>'btn-orange-s button-s'), sprintf('NO %s を非公開にします。よろしいですか？', $dbData['BlogComment']['no']),false); ?>
                <?php else: ?>
                    <?php echo $html->link('公開', array('action'=>'publish', $blogContent['BlogContent']['id'],0,$dbData['BlogComment']['id']), array('class'=>'btn-green-s button-s'), sprintf('NO %s を公開します。よろしいですか？', $dbData['BlogComment']['no']),false); ?>
                <?php endif ?>
                <?php echo $html->link('削除', array('action'=>'delete', $blogContent['BlogContent']['id'],0,$dbData['BlogComment']['id']), array('class'=>'btn-gray-s button-s'), sprintf('NO %s を本当に削除してもいいですか？', $dbData['BlogComment']['no']),false); ?>
            <?php endif ?>
		</td>
		<td><?php echo $dbData['BlogComment']['no'] ?></td>
		<td><?php if(!empty($dbData['BlogComment']['url'])): ?>
                <?php echo $html->link($dbData['BlogComment']['name'],$dbData['BlogComment']['url'],array('target'=>'_blank')) ?>
            <?php else: ?>
                <?php echo $dbData['BlogComment']['name'] ?>
            <?php endif ?>
        </td>
        <td><?php if(!empty($dbData['BlogComment']['email'])): ?>
                <?php echo $html->link($dbData['BlogComment']['email'],'mailto:'.$dbData['BlogComment']['email']) ?>
            <?php endif; ?>
		</td>
        <td><strong><?php echo $html->link($dbData['BlogPost']['name'],'/admin/blog/blog_posts/edit/'.$blogContent['BlogContent']['id'].'/'.$dbData['BlogPost']['id']) ?></strong><br />
            <?php echo $dbData['BlogComment']['message'] ?></td>
		<td><?php echo $timeEx->format('y-m-d',$dbData['BlogComment']['created']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$dbData['BlogComment']['modified']); ?></td>
	</tr>
	<?php $count++; ?>
<?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td></tr>
<?php endif; ?>
</table>

<?php echo $this->renderElement('paginations'.DS.'default'); ?>
