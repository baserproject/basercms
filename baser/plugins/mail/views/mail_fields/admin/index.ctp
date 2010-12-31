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
<?php $baser->js('sorttable',false) ?>

<?php echo $formEx->create('Sort',array('action'=>'update_sort','url'=>am(array('controller'=>'mail_fields'),$this->passedArgs))) ?>
	<?php echo $formEx->hidden('Sort.id') ?>
	<?php echo $formEx->hidden('Sort.offset') ?>
<?php echo $formEx->end() ?>

<div id="pageMessage" class="message" style="display:none"></div>

<h2>
	<?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?>
	<?php $baser->img('ajax-loader-s.gif',array('id'=>'ListAjaxLoader')) ?>
</h2>

<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>メールフォームの各フィールド（項目）の管理が行えます。</p>
	<ul>
		<li>新しいフィールドを登録するには、画面下の「新規登録」ボタンをクリックします。</li>
		<li>メールフォームの表示を確認するには、サイドメニューの「公開ページ確認」をクリックします。</li>
		<li>画面一番下の「並び替えモード」をクリックすると、表示される<?php $baser->img('sort.png',array('alt'=>'並び替え')) ?>マークをドラッグアンドドロップして行の並び替えができます。</li>
		<li>フィールドの設定をそのままコピーするにはコピーしたいフィールド左の「コピー」ボタンをクリックします。</li>
		<li>メールフォームより受信した内容は、画面下の「受信メールCSV」よりダウンロードする事ができ、Microsoft Excel 等の表計算ソフトで確認する事ができます。</li>
	</ul>
</div>

<p><strong>このメールフォームのURL：<?php $baser->link($baser->getUri('/'.$mailContent['MailContent']['name'].'/index'),'/'.$mailContent['MailContent']['name'].'/index',array('target'=>'_blank')) ?></strong></p>

<table cellpadding="0" cellspacing="0" class="admin-col-table-01 sort-table" id="TableMailFields">
	<tr>
		<th>操作</th>
		<th>NO</th>
		<th>フィールド名<br />項目名</th>
		<th>タイプ</th>
		<th>グループ名</th>
		<th>必須</th>
		<th>登録日<br />更新日</th>
	</tr>
	<?php if(!empty($listDatas)): ?>
		<?php $count=0; ?>
		<?php foreach($listDatas as $listData): ?>
			<?php if (!$listData['MailField']['use_field']): ?>
				<?php $class=' class="disablerow sortable"'; ?>
			<?php elseif ($count%2 === 0): ?>
				<?php $class=' class="altrow sortable"'; ?>
			<?php else: ?>
				<?php $class=' class="sortable"'; ?>
			<?php endif; ?>
	<tr id="Row<?php echo $count+1 ?>" <?php echo $class; ?>>
		<td style="width:25%" class="operation-button">
			<?php if($sortmode): ?>
			<span class="sort-handle"><?php $baser->img('sort.png',array('alt'=>'並び替え')) ?></span>
			<?php echo $formEx->hidden('Sort.id'.$listData['MailField']['id'],array('class'=>'id','value'=>$listData['MailField']['id'])) ?>
			<?php endif ?>
			<?php $baser->link('コピー',array('action'=>'copy', $mailContent['MailContent']['id'],$listData['MailField']['id']),array('class'=>'btn-red-s button-s'),null,false) ?>
			<?php $baser->link('編集',array('action'=>'edit', $mailContent['MailContent']['id'],$listData['MailField']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php $baser->link('削除', array('action'=>'delete', $mailContent['MailContent']['id'],$listData['MailField']['id']), array('class'=>'btn-gray-s button-s'), sprintf('本当に「%s」を削除してもいいですか？', $listData['MailField']['name']),false); ?>
		</td>
		<td style="width:5%"><?php echo $listData['MailField']['no'] ?></td>
		<td style="width:30%">
			<?php $baser->link($listData['MailField']['field_name'],array('action'=>'edit', $mailContent['MailContent']['id'],$listData['MailField']['id'])); ?><br />
			<?php echo $listData['MailField']['name']; ?>
		</td>
		<td style="width:19%"><?php echo $textEx->listValue('MailField.type',$listData['MailField']['type']); ?></td>
		<td style="width:10%"><?php echo $listData['MailField']['group_field'] ?></td>
		<td style="width:8%"><?php echo $textEx->booleanMark($listData['MailField']['not_empty']) ?></td>
		<td style="width:8%">
				<?php echo $timeEx->format('y-m-d',$listData['MailField']['created']); ?><br />
				<?php echo $timeEx->format('y-m-d',$listData['MailField']['modified']); ?>
		</td>
	</tr>
			<?php $count++; ?>
		<?php endforeach; ?>
	<?php else: ?>
	<tr>
		<td colspan="9"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
	<?php endif; ?>
</table>
<p class="align-center">
	<?php $baser->link('新規登録',array('action'=>'add',$mailContent['MailContent']['id']),array('class'=>'btn-red button')) ?>
	<?php if(!$sortmode): ?>
	<?php $baser->link('並び替えモード',array($mailContent['MailContent']['id'], 'sortmode'=>1),array('class'=>'btn-orange button')) ?>
	<?php else: ?>
	<?php $baser->link('ノーマルモード',array($mailContent['MailContent']['id'], 'sortmode'=>0),array('class'=>'btn-orange button')) ?>
	<?php endif ?>
	<?php $baser->link('受信メールCSV',array('action'=>'download_csv', $mailContent['MailContent']['id']),array('class'=>'btn-gray button'),null,false) ?>
</p>
