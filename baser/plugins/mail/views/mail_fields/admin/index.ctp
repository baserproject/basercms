<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] メールフィールド 一覧
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
 * @package			baser.plugins.mail.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<h2><?php $baser->contentsTitle() ?>&nbsp;<?php echo $html->image('help.png',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>メールフォームの各フィールド（項目）の管理が行えます。</p>
	<ul>
		<li>新しいフィールドを登録するには、画面下の「新規登録」ボタンをクリックします。</li>
		<li>メールフォームの表示を確認するには、サイドメニューの「公開ページ確認」をクリックします。</li>
		<li>各フィールド左の▲▼をクリックする事で並び順を変更する事ができます。</li>
		<li>フィールドの設定をそのままコピーするにはコピーしたいフィールド左の「コピー」ボタンをクリックします。</li>
		<li>メールフォームより受信した内容は、画面下の「受信メールCSV」よりダウンロードする事ができ、Microsoft Excel 等の表計算ソフトで確認する事ができます。</li>
	</ul>
</div>


<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableMailFields">
<tr>
	<th>操作</th>
    <th>NO</th>
	<th>フィールド名</th>
	<th>項目名</th>
	<th>グループ名</th>
	<th>タイプ</th>
	<th>必須</th>
	<th>登録日</th>
	<th>更新日</th>
</tr>
<?php if(!empty($listDatas)): ?>
<?php $count=0; ?>
<?php foreach($listDatas as $listData): ?>
	<?php if (!$listData['MailField']['use_field']): ?>
		<?php $class=' class="disablerow"'; ?>
	<?php elseif ($count%2 === 0): ?>
		<?php $class=' class="altrow"'; ?>
	<?php else: ?>
		<?php $class=''; ?>
	<?php endif; ?>
	<tr<?php echo $class; ?>>
        <td class="operation-button">
			<?php $baser->link('コピー',array('action'=>'copy', $mailContent['MailContent']['id'],$listData['MailField']['id']),array('class'=>'btn-red-s button-s'),null,false) ?>
			<?php $baser->link('編集',array('action'=>'edit', $mailContent['MailContent']['id'],$listData['MailField']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php $baser->link('削除', array('action'=>'delete', $mailContent['MailContent']['id'],$listData['MailField']['id']), array('class'=>'btn-gray-s button-s'), sprintf('本当に「%s」を削除してもいいですか？', $listData['MailField']['name']),false); ?>
			<?php $baser->link('▲',array('action'=>'index',$mailContent['MailContent']['id'],'sortup'=>$listData['MailField']['id'])) ?>
			<?php $baser->link('▼',array('action'=>'index',$mailContent['MailContent']['id'],'sortdown'=>$listData['MailField']['id'])) ?>
		</td>
        <td><?php echo $listData['MailField']['no'] ?></td>
		<td><?php $baser->link($listData['MailField']['field_name'],array('action'=>'edit', $mailContent['MailContent']['id'],$listData['MailField']['id'])); ?></td>
		<td><?php echo $listData['MailField']['name']; ?></td>
		<td><?php echo $listData['MailField']['group_field'] ?></td>
		<td><?php echo $textEx->listValue('MailField.type',$listData['MailField']['type']); ?></td>
		<td><?php echo $textEx->booleanMark($listData['MailField']['not_empty']) ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['MailField']['created']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['MailField']['modified']); ?></td>
	</tr>
	<?php $count++; ?>
<?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="9"><p class="no-data">データが見つかりませんでした。</p></td></tr>
<?php endif; ?>
</table>


<p class="align-center">
<?php $baser->link('新規登録',array('action'=>'add',$mailContent['MailContent']['id']),array('class'=>'btn-red button')) ?>
<?php $baser->link('受信メールCSV',array('action'=>'download_csv', $mailContent['MailContent']['id']),array('class'=>'btn-orange button'),null,false) ?>
</p>