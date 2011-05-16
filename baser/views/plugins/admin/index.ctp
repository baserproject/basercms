<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] プラグイン 一覧
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<h2><?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>BaserCMSのプラグインの管理を行います。<br />
		メールフォーム・フィードリーダー・ブログの３つのプラグインは標準プラグインとして同梱されており、
		インストールも完了しています。各プラグインの「管理」ボタンから各プラグインの管理が行えます。</p>
	<div class="example-box">
		<div class="head">新しいプラグインのインストール方法</div>
		<ol>
			<li>app/plugins/ フォルダに、入手したプラグインのフォルダをアップロードします。</li>
			<li>プラグイン一覧に、新しいプラグインが表示されますので、その行の「登録」ボタンをクリックします。</li>
			<li>登録画面が表示されますので、表示内容に問題がなければ「登録」ボタンをクリックします。</li>
		</ol>
	</div>
</div>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TablePlugins">
	<tr>
		<th>操作</th>
		<th>プラグイン名</th>
		<th>タイトル</th>
		<th style="white-space: nowrap">バージョン</th>
		<th>説明</th>
		<th>開発者</th>
	</tr>
<?php if(!empty($listDatas)): ?>
	<?php $count=0; ?>
	<?php foreach($listDatas as $listData): ?>
		<?php if(!$listData['Plugin']['status']): ?>
			<?php $class=' class="disablerow"' ?>
		<?php elseif ($count%2 === 0): ?>
			<?php $class=' class="altrow"'; ?>
		<?php else: ?>
			<?php $class=''; ?>
		<?php endif; ?>
	<tr<?php echo $class; ?>>
		<td class="operation-button">
		<?php if($listData['Plugin']['update']): ?>
			<?php $baser->link('UP', '/admin/updaters/plugin/'.$listData['Plugin']['name'], array('class' => 'btn-red-s button-s'), null, false) ?>
		<?php endif ?>
		<?php if($listData['Plugin']['admin_link'] && $listData['Plugin']['status'] && !$listData['Plugin']['update'] && !$listData['Plugin']['old_version']): ?>
			<?php $baser->link('管理', $listData['Plugin']['admin_link'], array('class'=>'btn-red-s button-s'), null, false) ?>
		<?php endif; ?>
		<?php if($listData['Plugin']['status']): ?>
			<?php $baser->link('無効', 
					array('action' => 'delete', $listData['Plugin']['id']),
					array('class' => 'btn-gray-s button-s'),
					sprintf('%s を本当に無効にしてもいいですか？\nプラグインフォルダ内のファイル、データベースに保存した情報は削除されずそのまま残ります。', $listData['Plugin']['name']),
					false); ?>
		<?php elseif(!$listData['Plugin']['status'] && !$listData['Plugin']['update'] && !$listData['Plugin']['old_version']): ?>
			<?php $baser->link('登録', array('action' => 'add', $listData['Plugin']['name']), array('class' => 'btn-green-s button-s'), null, false) ?>
		<?php endif ?>
		<?php if(!$listData['Plugin']['status']): ?>
			<?php $baser->link('削除', 
					array('action' => 'delete_file', $listData['Plugin']['name']),
					array('class' => 'btn-gray-s button-s'),
					sprintf('%s を本当に削除してもいいですか？\nプラグインフォルダ内のファイル、データベースのデータも全て削除されます。', $listData['Plugin']['name']),
					false); ?>
		<?php endif; ?>
		<?php if($listData['Plugin']['old_version']): ?>
			<br /><small style="color:#CC0000">新しいバージョンに<br />アップデートしてください</small>
		<?php elseif($listData['Plugin']['update']): ?>
			<br /><small style="color:#CC0000">アップデートを<br />完了させてください</small>
		<?php endif ?>
		</td>
		<td><?php echo $listData['Plugin']['name'] ?></td>
		<td style="white-space: nowrap"><?php echo $listData['Plugin']['title'] ?></td>
		<td><?php echo $listData['Plugin']['version'] ?></td>
		<td><?php echo $listData['Plugin']['description'] ?></td>
		<td><?php $baser->link($listData['Plugin']['author'], $listData['Plugin']['url'], array('target' => '_blank')) ?></td>
	</tr>
			<?php $count++; ?>
		<?php endforeach; ?>
	<?php else: ?>
	<tr>
		<td colspan="6"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
	<?php endif; ?>
</table>
