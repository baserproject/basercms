<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ユーザー フォーム
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
<!--<h3>基本項目</h3>-->
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php echo $form->create('User') ?>
<?php echo $form->hidden('User.id') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_view' || $this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $form->label('User.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $form->text('User.id', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
<tr>
	<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('User.name', 'アカウント名') ?></th>
	<td class="col-input">
		<?php echo $form->text('User.name', array('size'=>20,'maxlength'=>255)) ?>
		<?php echo $form->error('User.name') ?>&nbsp;
	</td>
</tr>
<tr>
	<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('User.real_name_1', '名前') ?></th>
	<td class="col-input">
		<small>[姓]</small> <?php echo $form->text('User.real_name_1', array('size'=>12,'maxlength'=>255)) ?> 
        <small>[名]</small> <?php echo $form->text('User.real_name_2', array('size'=>12,'maxlength'=>255)) ?>
		<?php echo $form->error('User.real_name_1', '>> 姓を入力して下さい') ?>
		<?php echo $form->error('User.real_name_2', '>> 名を入力して下さい') ?>&nbsp;
        &nbsp;
	</td>
</tr>
<tr>
	<th class="col-head">
		<?php if($this->action == 'admin_add'): ?>
		<span class="required">*</span>&nbsp;
		<?php endif; ?>
		<?php echo $form->label('User.password_1', 'パスワード') ?>
	</th>
	<td class="col-input">
		<small>[パスワードは変更する場合のみ入力してください]</small><br />
		<?php echo $form->password('User.password_1', array('size'=>20,'maxlength'=>255)) ?>
		<?php echo $form->password('User.password_2', array('size'=>20,'maxlength'=>255)) ?>&nbsp;
        <?php echo $html->image('help.png',array('id'=>'helpPassword','class'=>'help','alt'=>'ヘルプ')) ?>
        <div id="helptextPassword" class="helptext">
            <ul>
                <li><?php if($this->action == "admin_edit"): ?>パスワードの変更をする場合は、<?php endif; ?>確認の為２回入力して下さい。</li>
                <li>半角英数字で入力して下さい</li>
                <li>最低６文字以上で入力して下さい</li>
            </ul>
        </div>
		<?php echo $form->error('User.password') ?>&nbsp;
	</td>
</tr>
</table>

<div style="display:none">
<h3><a href="javascript:void(0)" id="formOption" class="slide-trigger">オプション</a></h3>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01 slide-body" id="formOptionBody">
<tr>
	<th class="col-head"><?php echo $form->label('User.authority_group', 'グループ') ?></th>
	<td class="col-input"><?php echo $form->select('User.authority_group', $formEx->getControlSource('authority_group'),$form->value('User.authority_group')) ?>
        <?php echo $form->error('User.authority_group', '>> グループを選択して下さい') ?>&nbsp;</td>
</tr>
<tr>
	<th class="col-head"><?php echo $form->label('User.email', 'Eメール') ?></th>
	<td class="col-input"><?php echo $form->text('User.email', array('size'=>40,'maxlength'=>255)) ?>
        <?php echo $form->error('User.email', '>> Eメールの形式が不正です') ?>&nbsp;</td>
</tr>
</table>
</div>

<div class="align-center">
<?php if ($this->action == 'admin_edit'): ?>
    <?php if(isset($baser->siteConfig['demo_on']) && $baser->siteConfig['demo_on']): ?>
    <p class="message">デモサイトで管理ユーザーの編集、削除はできません</p>
    <?php else: ?>
        <?php echo $form->submit('更　新',array('div'=>false,'class'=>'btn-orange button')) ?>
        <?php echo $html->link('削除する', array('action'=>'delete', $form->value('User.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $form->value('User.name')),false); ?>
    <?php endif ?>
	</form>
<?php else: ?>
	<?php echo $form->end(array('label'=>'登　録', 'div'=>false,'class'=>'btn-red button')) ?>
<?php endif ?>
</div>
