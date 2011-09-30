<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] プラグイン　フォーム
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
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
	<p>プラグイン設定の登録・変更を行います。<br />
		新しいプラグインを入手した場合は、そのままの内容で登録しても大丈夫です。
		プラグイン一覧の「管理」ボタンをクリックした際に表示するページを変更する場合は「管理URL」を変更します。</p>
</div>

<?php echo $formEx->create('Plugin',array('url' => array($this->data['Plugin']['name']))) ?>
<?php echo $formEx->input('Plugin.name', array('type' => 'hidden')) ?>
<?php echo $formEx->input('Plugin.title', array('type' => 'hidden')) ?>
<?php echo $formEx->input('Plugin.status', array('type' => 'hidden')) ?>
<?php echo $formEx->input('Plugin.version', array('type' => 'hidden')) ?>

<!-- form -->
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><?php echo $formEx->label('Plugin.name', 'プラグイン名') ?></th>
		<td class="col-input">
			<?php echo $formEx->value('Plugin.name').' '.$formEx->value('Plugin.version') ?>
			<?php if($formEx->value('Plugin.title')): ?>
				（<?php echo $formEx->value('Plugin.title') ?>）
			<?php endif ?>
		</td>
	</tr>
</table>

<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->submit('登　録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php else: ?>
	<?php echo $formEx->submit('更　新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削　除', 
			array('action' => 'delete', $formEx->value('Plugin.id')),
			array('class' => 'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('Plugin.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>