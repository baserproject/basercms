<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ記事 一覧
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<!-- title -->
<h2><?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ブログ記事に対するコメントの管理が行えます。</p>
	<ul>
		<li>コメントが投稿された場合、サイト基本設定で設定された管理者メールアドレスに通知メールが送信されます。</li>
		<li>コメントが投稿された場合、コメント承認機能を利用している場合は、コメントのステータスは「非公開」となっています。
			内容を確認して問題なければ、「公開」ボタンをクリックします。</li>
	</ul>
</div>

<!-- list-num -->
<?php $baser->element('list_num') ?>

<!-- pagination -->
<?php $baser->pagination('default',array(),null,false) ?>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableBlogComments">
	<tr>
		<th>操作</th>
		<th><?php echo $paginator->sort(array('asc' => 'NO ▼', 'desc' => 'NO ▲'),'no'); ?></th>
		<th><?php echo $paginator->sort(array('asc' => '投稿者 ▼', 'desc' => '投稿者 ▲'), 'name'); ?></th>
		<th><?php echo $paginator->sort(array('asc' => 'メール ▼', 'desc' => '投稿者 ▲'), 'mail'); ?></th>
		<th><?php echo $paginator->sort(array('asc' => 'メッセージ ▼', 'desc'=>'メッセージ ▲'), 'comment'); ?></th>
		<th><?php echo $paginator->sort(array('asc' => '投稿日 ▼', 'desc' => '投稿日 ▲'), 'posts_date'); ?></th>
		<th><?php echo $paginator->sort(array('asc' => '更新日 ▼', 'desc' => '更新日 ▲'), 'modified'); ?></th>
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
			<?php $baser->link('非公開', array('action'=>'unpublish', $blogContent['BlogContent']['id'], $dbData['BlogComment']['blog_post_id'], $dbData['BlogComment']['id']), array('class'=>'btn-orange-s button-s'), sprintf('NO %s を非公開にします。よろしいですか？', $dbData['BlogComment']['no']), false); ?>
			<?php else: ?>
			<?php $baser->link('公開', array('action'=>'publish', $blogContent['BlogContent']['id'], $dbData['BlogComment']['blog_post_id'], $dbData['BlogComment']['id']), array('class'=>'btn-green-s button-s'), sprintf('NO %s を公開します。よろしいですか？', $dbData['BlogComment']['no']), false); ?>
			<?php endif ?>
			<?php $baser->link('削除', array('action'=>'delete', $blogContent['BlogContent']['id'], $dbData['BlogComment']['blog_post_id'], $dbData['BlogComment']['id']), array('class'=>'btn-gray-s button-s'), sprintf('NO %s を本当に削除してもいいですか？', $dbData['BlogComment']['no']), false); ?>
		<?php else: ?>
			<?php if($dbData['BlogComment']['status']): ?>
			<?php $baser->link('非公開', 	array('action'=>'unpublish', $blogContent['BlogContent']['id'], 0, $dbData['BlogComment']['id']), array('class'=>'btn-orange-s button-s'), sprintf('NO %s を非公開にします。よろしいですか？', $dbData['BlogComment']['no']), false); ?>
			<?php else: ?>
			<?php $baser->link('公開', array('action'=>'publish', $blogContent['BlogContent']['id'], 0, $dbData['BlogComment']['id']), array('class'=>'btn-green-s button-s'), sprintf('NO %s を公開します。よろしいですか？', $dbData['BlogComment']['no']),	 false); ?>
			<?php endif ?>
			<?php $baser->link('削除', array('action'=>'delete', $blogContent['BlogContent']['id'], 0, $dbData['BlogComment']['id']), array('class'=>'btn-gray-s button-s'), sprintf('NO %s を本当に削除してもいいですか？', $dbData['BlogComment']['no']), false); ?>
		<?php endif ?></td>
		<td><?php echo $dbData['BlogComment']['no'] ?></td>
		<td>
		<?php if(!empty($dbData['BlogComment']['url'])): ?>
			<?php $baser->link($dbData['BlogComment']['name'], $dbData['BlogComment']['url'], array('target' => '_blank')) ?>
		<?php else: ?>
			<?php echo $dbData['BlogComment']['name'] ?>
		<?php endif ?>
		</td>
		<td>
		<?php if(!empty($dbData['BlogComment']['email'])): ?>
			<?php $baser->link($dbData['BlogComment']['email'], 'mailto:'.$dbData['BlogComment']['email']) ?>
		<?php endif; ?>
		</td>
		<td>
			<strong>
			<?php $baser->link($dbData['BlogPost']['name'], array('controller' => 'blog_posts', 'action' => 'edit', $blogContent['BlogContent']['id'], $dbData['BlogPost']['id'])) ?>
			</strong><br />
			<?php echo $dbData['BlogComment']['message'] ?>
		</td>
		<td><?php echo $timeEx->format('y-m-d',$dbData['BlogComment']['created']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$dbData['BlogComment']['modified']); ?></td>
	</tr>
		<?php $count++; ?>
	<?php endforeach; ?>
<?php else: ?>
	<tr><td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td></tr>
<?php endif; ?>
</table>

<!-- pagination -->
<?php $baser->pagination('default',array(),null,false) ?>