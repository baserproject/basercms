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

<h2>
	<?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ユーザーグループは、グループごとにコンテンツへのアクセス制限をかける際に利用します。<br />
		サイト運営者にはニュースリリースの発信のみ行わせたい場合などにログインユーザーのグループ分けを行うと便利です。</p>
	<ul>
		<li>アクセス制限をかけるには
			<?php $baser->link('アクセス制限設定管理',array('controller'=>'permissions','action'=>'index')) ?>
			より行います。</li>
		<li>管理者グループのアクセス制限設定、削除、識別名の変更はできません。</li>
	</ul>
</div>
<!-- pagination -->
<?php $baser->pagination('default',array(),null,false) ?>
<!-- list -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="UserGroupsTable">
	<tr>
		<th>操作</th>
		<th><?php echo $paginator->sort(array('asc'=>'NO ▼','desc'=>'NO ▲'),'id'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'ユーザーグループ名 ▼','desc'=>'識別名 ▲'),'name'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'表示名 ▼','desc'=>'グループ名 ▲'),'title'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'登録日 ▼','desc'=>'登録日 ▲'),'created'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'更新日 ▼','desc'=>'更新日 ▲'),'modified'); ?></th>
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
		<td class="operation-button"><?php if($listData['UserGroup']['name']!='admins'): ?>
			<?php $baser->link('権限', array('controller'=>'permissions','action'=>'index', $listData['UserGroup']['id']), array('class'=>'btn-red-s button-s'),null,false); ?>
			<?php endif ?>
			<?php $baser->link('編集',array('action'=>'edit', $listData['UserGroup']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php if($listData['UserGroup']['name']!='admins'): ?>
			<?php $baser->link('削除', array('action'=>'delete', $listData['UserGroup']['id']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？\n\n削除する場合、関連するユーザーは削除されませんが、関連するアクセス制限設定は全て削除されます。\n※ 関連するユーザーは管理者グループに所属する事になります。', $listData['UserGroup']['title']),false); ?>
			<?php endif ?></td>
		<td><?php echo $listData['UserGroup']['id']; ?></td>
		<td><?php $baser->link($listData['UserGroup']['name'],array('action'=>'edit', $listData['UserGroup']['id'])); ?></td>
		<td><?php echo $listData['UserGroup']['title']; ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['UserGroup']['created']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['UserGroup']['modified']); ?></td>
	</tr>
			<?php $count++; ?>
		<?php endforeach; ?>
	<?php else: ?>
	<tr>
		<td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
	<?php endif; ?>
</table>
<!-- pagination -->
<?php $baser->pagination('default',array(),null,false) ?>
<div class="align-center">
	<?php $baser->link('新規登録',array('action'=>'add'),array('class'=>'btn-red button')) ?>
</div>
