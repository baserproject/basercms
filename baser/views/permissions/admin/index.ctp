<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] アクセス拒否設定一覧
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
<script type="text/javascript">
$(document).ready(function(){
	<?php if($form->value('Permission.open')): ?>
	$("#PermissionsSearchBody").show();
	<?php endif ?>
});
</script>

<h2>
	<?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>サイト運営者には必要最低限のメニューしか表示しないなど、ユーザーグループごとのアクセス制限をかける事でシンプルなインターフェイスを実現する事ができます。<br />
		画面下の「新規登録」ボタンより新しいルールを追加します。</p>
	<ul>
		<li>ルールを何も追加しない状態では、全てのユーザーが全てのコンテンツにアクセスできるようになっています。</li>
		<li>複数のルールを追加した場合は、上から順に設定が上書きされ、下にいくほど優先されます。</li>
		<li>URL設定ではワイルドカード（*）を利用して一定のURL階層内のコンテンツに対し一度に設定を行う事ができます。</li>
		<li>管理者グループ「admins」には、アクセス制限の設定はできません。</li>
	</ul>
	<div class="example-box">
		<div class="head">（例）ページ管理全体は許可しないが、特定のページ「NO: ２」のみ許可を与える場合</div>
		<ol>
			<li>1つ目のルールとして、　/admin/pages*　を「不可」として追加します。</li>
			<li>2つ目のルールとして、　/admin/pages/edit/2　を「可」として追加します。</li>
		</ol>
	</div>
</div>
<h3><a href="javascript:void(0);" class="slide-trigger" id="PermissionsSearch">検索</a></h3>
<div class="function-box corner10" id="PermissionsSearchBody"> <?php echo $formEx->create('Permission',array('url'=>array('action'=>'index'),'type'=>'get')) ?>
	<p> <small>ユーザーグループ</small> <?php echo $formEx->select('Permission.user_group_id',  $formEx->getControlSource('user_group_id'),null,array(),false) ?>　 </p>
	<?php echo $formEx->hidden('Permission.open',array('value'=>true)) ?>
	<div class="align-center"> <?php echo $formEx->submit('検　索',array('div'=>false,'class'=>'btn-orange button')) ?> </div>
</div>
<!-- list -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="PermissionsTable">
	<tr>
		<th>操作</th>
		<th>NO</th>
		<th>ルール名</th>
		<th>URL設定</th>
		<th>アクセス</th>
		<th>登録日</th>
		<th>更新日</th>
	</tr>
	<?php if(!empty($listDatas)): ?>
		<?php $count=0; ?>
		<?php foreach($listDatas as $listData): ?>
			<?php if (!$listData['Permission']['status']): ?>
				<?php $class=' class="disablerow"'; ?>
			<?php elseif ($count%2 === 0): ?>
				<?php $class=' class="altrow"'; ?>
			<?php else: ?>
				<?php $class=''; ?>
			<?php endif; ?>
	<tr<?php echo $class; ?>>
		<td class="operation-button"><?php $baser->link('編集',array('action'=>'edit', $listData['Permission']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php if($listData['Permission']['name']!='admins'): ?>
			<?php $baser->link('削除', array('action'=>'delete', $listData['Permission']['id']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？', $listData['Permission']['name']),false); ?>
			<?php endif ?>
			<?php $baser->link('▲',array('action'=>'index','sortup'=>$listData['Permission']['id'])) ?>
			<?php $baser->link('▼',array('action'=>'index','sortdown'=>$listData['Permission']['id'])) ?></td>
		<td><?php echo $listData['Permission']['no']; ?></td>
		<td><?php $baser->link($listData['Permission']['name'],array('action'=>'edit', $listData['Permission']['id'])); ?></td>
		<td><?php echo $listData['Permission']['url']; ?></td>
		<td class="align-center"><?php echo $textEx->arrayValue($listData['Permission']['auth'],array(0=>'×',1=>'〇')) ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['Permission']['created']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['Permission']['modified']); ?></td>
	</tr>
			<?php $count++; ?>
		<?php endforeach; ?>
	<?php else: ?>
	<tr>
		<td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
	<?php endif; ?>
</table>
<div class="align-center">
	<?php $baser->link('新規登録',array('action'=>'add'),array('class'=>'btn-red button')) ?>
</div>
