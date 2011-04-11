<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] 受信メール一覧
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
 * @package			baser.plugins.mail.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<!-- title -->
<h2><?php $baser->contentsTitle() ?>
	<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpAdmin', 'class' => 'slide-trigger', 'alt' => 'ヘルプ')) ?></h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>受信メールの詳細確認、削除が行えます。操作対象データの操作欄のボタンをクリックします。</p>
</div>

<!-- list-num -->
<?php $baser->element('list_num') ?>

<!-- pagination -->
<?php $baser->pagination('default',array(),null,false) ?>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01 sort-table" id="TableMailMessages">
	<tr>
		<th style="white-space: nowrap">操作</th>
		<th style="white-space: nowrap"><?php echo $paginator->sort(array('asc' => 'NO ▼', 'desc' => 'NO ▲'), 'id'); ?></th>
		<th style="white-space: nowrap" colspan="2"><?php echo $paginator->sort(array('asc' => '受信日時 ▼', 'desc' => '受信日時 ▲'), 'created'); ?></th>
		<th style="white-space: nowrap">受信内容</th>
	</tr>
<?php if($messages): ?>
	<?php $count=0; ?>
	<?php foreach ($messages as $message): ?>
		<?php if ($count%2 === 0): ?>
			<?php $class=' class="altrow"' ?>
		<?php else: ?>
			<?php $class='' ?>
		<?php endif; ?>
	<tr id="Row<?php echo $count+1 ?>" <?php echo $class; ?>>
		<td style="text-align:center;white-space: nowrap">
			<?php $baser->link('詳細',
					array('action'=>'view', $mailContent['MailContent']['id'], $message['Message']['id']),
					array('class'=>'btn-orange-s button-s')) ?>
			<?php $baser->link('削除',
					array('action'=>'delete', $mailContent['MailContent']['id'], $message['Message']['id']),
					array('class'=>'btn-gray-s button-s'),
					sprintf('受信メール NO「%s」を削除してもいいですか？', $message['Message']['id']), false) ?>
		</td>
		<td><?php echo $message['Message']['id'] ?></td>
		<td><?php echo date('Y/m/d', strtotime($message['Message']['created'])) ?></td>
		<td><?php echo date('H:i', strtotime($message['Message']['created'])) ?></td>
		<td>
			<?php $inData = array() ?>
			<?php foreach($mailFields as $mailField): ?>
				<?php if(!$mailField['MailField']['no_send'] && $mailField['MailField']['use_field']): ?>
					<?php $inData[] = $maildata->control(
							$mailField['MailField']['type'],
							$message['Message'][$mailField['MailField']['field_name']],
							$mailfield->getOptions($mailField['MailField'])
					) ?>
				<?php endif ?>
			<?php endforeach ?>
			<?php echo $textEx->mbTruncate(implode(',',$inData),170) ?>
		</td>
	</tr>
		<?php $count++; ?>
	<?php endforeach; ?>
<?php else: ?>
	<tr><td colspan="5"><p class="no-data">データが見つかりませんでした。</p></td></tr>
<?php endif ?>
</table>

<p class="align-center">
	<?php $baser->link('CSVダウンロード',
			array('controller' => 'mail_fields', 'action' => 'download_csv', $mailContent['MailContent']['id']),
			array('class'=>'btn-gray button'), null, false) ?>
</p>
