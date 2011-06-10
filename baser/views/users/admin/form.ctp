<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ユーザー フォーム
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
<script type="text/javascript">
$(function(){
	$("#btnEdit").click(function(){
		if($("#SelfUpdate").html()) {
			if(confirm('更新内容をログイン情報に反映する為、一旦ログアウトします。よろしいですか？')) {
				return true;
			}
		} else {
			return true;
		}
		return false;
	});
});
</script>

<div id="SelfUpdate" class="display-none"><?php echo $selfUpdate ?></div>

<h2><?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>

<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ログイン用のユーザーアカウントを登録する事ができます。<br />
		パスワード欄は変更する場合のみ入力します。</p>
</div>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $formEx->create('User') ?>
<?php echo $formEx->hidden('User.id') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('User.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $formEx->value('User.id') ?>
			<?php echo $formEx->input('User.id', array('type' => 'hidden')) ?>
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('User.name', 'アカウント名') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('User.name', array('type' => 'text', 'size' => 20, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpName', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('User.name') ?>
			<div id="helptextName" class="helptext">半角英数字とハイフン、アンダースコアのみで入力してください。</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('User.real_name_1', '名前') ?></th>
		<td class="col-input">
			<small>[姓]</small> <?php echo $formEx->input('User.real_name_1', array('type' => 'text', 'size' => 12, 'maxlength' => 255)) ?>
			<small>[名]</small> <?php echo $formEx->input('User.real_name_2', array('type' => 'text', 'size' => 12, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpRealName1', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('User.real_name_1', '姓を入力してください') ?>
			<?php echo $formEx->error('User.real_name_2', '名を入力してください') ?>
			<div id="helptextRealName1" class="helptext"> 「名」は省略する事ができます。 </div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('User.user_group_id', 'グループ') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('User.user_group_id', array('type' => 'select', 'options' => $formEx->getControlSource('user_group_id'))) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpUserGroupId', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('User.user_group_id', 'グループを選択してください') ?>
			<div id="helptextUserGroupId" class="helptext"> ユーザーグループごとにコンテンツへのアクセス制限をかける場合などには
				<?php $baser->link('ユーザーグループ管理',array('controller'=>'user_groups','action'=>'index')) ?>
				より新しいグループを追加しアクセス制限の設定をおこないます。</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('User.email', 'Eメール') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('User.email', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id' => 'helpEmail', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('User.email') ?>
			<div id="helptextEmail" class="helptext">
				連絡用メールアドレスを入力します。<br /><small>※ パスワードを忘れた場合の新パスワードの通知先等</small>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head">
			<?php if($this->action == 'admin_add'): ?>
			<span class="required">*</span>&nbsp;
			<?php endif; ?>
			<?php echo $formEx->label('User.password_1', 'パスワード') ?>
		</th>
		<td class="col-input">
			<small>[パスワードは変更する場合のみ入力してください]</small><br />
			<?php echo $formEx->input('User.password_1', array('type' => 'password', 'size' => 20, 'maxlength' => 255)) ?>
			<?php echo $formEx->input('User.password_2', array('type' => 'password', 'size' => 20, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpPassword', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('User.password') ?>
			<div id="helptextPassword" class="helptext">
				<ul>
					<li>
						<?php if($this->action == "admin_edit"): ?>
						パスワードの変更をする場合は、
						<?php endif; ?>
						確認の為２回入力してください。</li>
					<li>半角英数字とハイフン、アンダースコアのみで入力してください</li>
					<li>最低６文字以上で入力してください</li>
				</ul>
			</div>
		</td>
	</tr>
</table>

<div class="align-center">
	<?php if ($this->action == 'admin_edit'): ?>
		<?php if(isset($baser->siteConfig['demo_on']) && $baser->siteConfig['demo_on']): ?>
	<p class="message">デモサイトで管理ユーザーの編集、削除はできません</p>
		<?php else: ?>
	<?php echo $formEx->submit('更　新', array('div' => false, 'class' => 'btn-orange button', 'id' => 'btnEdit')) ?>
	<?php $baser->link('削　除', 
			array('action' => 'delete', $formEx->value('User.id')),
			array('class' => 'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('User.name')), false); ?>
		<?php endif ?>
	<?php else: ?>
	<?php echo $formEx->submit('登　録', array('div' => false, 'class' => 'btn-red button')) ?>
	<?php endif ?>
</div>

<?php echo $formEx->end() ?>