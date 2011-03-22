<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ユーザー一覧
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
	&nbsp;<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpAdmin', 'class' => 'slide-trigger', 'alt' => 'ヘルプ')) ?></h2>

<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ユーザー管理ではログインする事ができるユーザーの管理を行う事ができます。<br />
		新しいユーザーを登録するには「新規登録」ボタンをクリックします。</p>
</div>

<!-- 検索 -->
<h3><a href="javascript:void(0);" class="slide-trigger" id="UsersSearch">検索</a></h3>
<div class="function-box corner10" id="UsersSearchBody">
	<?php echo $formEx->create('User', array('url' => array('action' => 'index'))) ?>
	<p>
		<small>ユーザーグループ</small>
		<?php echo $formEx->input('User.user_group_id', array('type' => 'select', 'options' => $formEx->getControlSource('User.user_group_id'), 'empty' => '指定なし')) ?>
		<?php echo $formEx->submit('検　索',array('div'=>false,'class'=>'btn-orange button')) ?>
		<?php echo $form->end() ?>
	</p>
</div>

<!-- list-num -->
<?php $baser->element('list_num') ?>

<!-- pagination -->
<?php $baser->pagination('default',array(),null,false) ?>

<!-- 一覧 -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableUsers">
	<tr>
		<th style="width:100px">操作</th>
		<th><?php echo $paginator->sort(array('asc'=>'NO ▼','desc'=>'NO ▲'),'id'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'アカウント名 ▼','desc'=>'アカウント名 ▲'),'name'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'グループ ▼','desc'=>'グループ ▲'),'user_group_id'); ?><br />
			<?php echo $paginator->sort(array('asc'=>'氏名 ▼','desc'=>'氏名 ▲'),'real_name_1'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'登録日 ▼','desc'=>'登録日 ▲'),'created'); ?><br />
			<?php echo $paginator->sort(array('asc'=>'更新日 ▼','desc'=>'更新日 ▲'),'modified'); ?></th>
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
			<?php $baser->link('編集',array('action'=>'edit', $user['User']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php $baser->link('削除', array('action'=>'delete', $user['User']['id']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？', $user['User']['name']),false); ?></td>
		<td><?php echo $user['User']['id'] ?></td>
		<td><?php $baser->link($user['User']['name'],array('action'=>'edit', $user['User']['id'])) ?></td>
		<td><?php echo $textEx->listValue('User.user_group_id',$user['User']['user_group_id']); ?><br />
			<?php echo $user['User']['real_name_1']; ?>&nbsp;<?php echo $user['User']['real_name_2'] ?></td>
		<td><?php echo $timeEx->format('y-m-d',$user['User']['created']) ?><br />
			<?php echo $timeEx->format('y-m-d',$user['User']['modified']) ?></td>
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