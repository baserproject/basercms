<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ユーザーグループ登録/編集フォーム
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
<h2><?php $baser->contentsTitle() ?></h2>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php echo $formEx->create('Permission') ?>
<?php echo $formEx->hidden('Permission.id') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Permission.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $formEx->text('Permission.no', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
<tr>
	<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Permission.name', 'ルール名') ?></th>
	<td class="col-input">
		<?php echo $formEx->text('Permission.name', array('size'=>20,'maxlength'=>255)) ?>
		<?php echo $html->image('help.png',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?>
		<?php echo $form->error('Permission.name') ?>
		<div id="helptextName" class="helptext">
			設定を特定できるわかりやすい名称を入力してください。
		</div>
	</td>
</tr>
<tr>
	<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Permission.user_group_id', 'ユーザーグループ') ?></th>
	<td class="col-input">
		<?php echo $formEx->select('Permission.user_group_id', $formEx->getControlSource('user_group_id'),null,array(),false) ?>
		<?php echo $formEx->error('Permission.user_group_id') ?>
	</td>
</tr>
<tr>
	<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Permission.url', 'URL設定') ?></th>
	<td class="col-input">
		<?php echo $formEx->text('Permission.url', array('size'=>40,'maxlength'=>255)) ?>
		<?php echo $html->image('help.png',array('id'=>'helpUrl','class'=>'help','alt'=>'ヘルプ')) ?>
		<?php echo $form->error('Permission.url') ?>
		<div id="helptextUrl" class="helptext">
			<ul>
				<li>BaserCMSの設置URLを除いたスラッシュから始まるURLを入力してください。<br />
					（例）/admin/users/index</li>
				<li>管理画面など認証がかかっているURLしか登録できません。</li>
				<li>特定のフォルダ配下に対しアクセスできないようにする場合などにはワイルドカード（*）を利用します。<br />
					（例）ユーザー管理内のURL全てアクセスさせない場合： /admin/users*
				</li>
			</ul>
		</div>
	</td>
</tr>
<tr>
	<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Permission.auth', 'アクセス') ?></th>
	<td class="col-input">
		<?php echo $formEx->radio('Permission.auth', $formEx->getControlSource('auth'),array('legend'=>false,'separator'=>'　')) ?>
		<?php echo $formEx->error('Permission.auth') ?>
	</td>
</tr>
</table>

<div class="align-center">
<?php if ($this->action == 'admin_edit'): ?>
	<?php echo $formEx->submit('更　新',array('div'=>false,'class'=>'btn-orange button')) ?>
	<?php $baser->link('削　除', array('action'=>'delete', $formEx->value('Permission.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $formEx->value('Permission.name')),false); ?>
	</form>
<?php else: ?>
	<?php echo $formEx->end(array('label'=>'登　録', 'div'=>false,'class'=>'btn-red button')) ?>
<?php endif ?>
</div>
