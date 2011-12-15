<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログカテゴリ 一覧
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
$allowOwners = array();
if(isset($user['user_group_id'])) {
	$allowOwners = array('', $user['user_group_id']);
}
?>

<!-- title -->
<h2><?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpAdmin', 'class' => 'slide-trigger', 'alt' => 'ヘルプ')) ?></h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ブログカテゴリは、ブログ記事を分類分けする際に利用します。<br />
		また、各カテゴリは子カテゴリを持つ事ができるようになっています。</p>
</div>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableBlogCategorys">
	<tr>
		<th style="width:122px">操作</th>
		<th>NO</th>
		<th>ブログカテゴリ名
			<?php if($baser->siteConfig['category_permission']): ?>
			<br />管理グループ
			<?php endif ?>
		</th>
		<th>ブログカテゴリタイトル</th>
		<th>登録日<br />更新日</th>
	</tr>
<?php if(!empty($dbDatas)): ?>
	<?php $count=0; ?>
	<?php foreach($dbDatas as $dbData): ?>
		<?php if ($count%2 === 0): ?>
			<?php $class=' class="altrow"'; ?>
		<?php else: ?>
			<?php $class=''; ?>
		<?php endif; ?>
	<tr<?php echo $class; ?>>
		<td class="operation-button">
			<?php $baser->link('確認', $blog->getCategoryUrl($dbData['BlogCategory']['id']), array('target' => '_blank', 'class' => 'btn-green-s button-s')) ?>
		<?php if(in_array($dbData['BlogCategory']['owner_id'], $allowOwners)||(isset($user['user_group_id']) && $user['user_group_id']==1)): ?>
			<?php $baser->link('編集', array('action' => 'edit', $blogContent['BlogContent']['id'], $dbData['BlogCategory']['id']), array('class' => 'btn-orange-s button-s'), null, false) ?>
			<?php $baser->link('削除',
					array('action'=>'delete', $blogContent['BlogContent']['id'], $dbData['BlogCategory']['id']),
					array('class'=>'btn-gray-s button-s'),
					sprintf('%s を本当に削除してもいいですか？\n\nこのカテゴリに関連する記事は、どのカテゴリにも関連しない状態として残ります。', $dbData['BlogCategory']['name']),
					false); ?>
		<?php endif ?>
		</td>
		<td><?php echo $dbData['BlogCategory']['no'] ?></td>
		<td><?php $baser->link($dbData['BlogCategory']['name'], array('action' => 'edit', $blogContent['BlogContent']['id'], $dbData['BlogCategory']['id'])) ?>
	<?php if($baser->siteConfig['category_permission']): ?>
			<br />
			<?php echo $textEx->arrayValue($dbData['BlogCategory']['owner_id'], $owners) ?>
	<?php endif ?>
		</td>
		<td><?php echo $dbData['BlogCategory']['title'] ?></td>
		<td><?php echo $timeEx->format('Y-m-d',$dbData['BlogCategory']['created']); ?><br />
			<?php echo $timeEx->format('Y-m-d',$dbData['BlogCategory']['modified']); ?></td>
	</tr>
		<?php $count++; ?>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="6"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
<?php endif; ?>
</table>

<!-- button -->
<?php if(isset($newCatAddable) && $newCatAddable): ?>
<div class="align-center">
	<?php $baser->link('新規登録', array('action' => 'add', $blogContent['BlogContent']['id']), array('class' => 'btn-red button')) ?>
</div>
<?php endif ?>
