<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ユーザー フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<script type="text/javascript">
$(window).load(function() {
	$("#UserName").focus();
});
$(function(){
	$("#BtnSave").click(function(){
		if($("#SelfUpdate").html()) {
			if(confirm('更新内容をログイン情報に反映する為、一旦ログアウトします。よろしいですか？')) {
				return true;
			}
		} else {
			return true;
		}
		return false;
	});
	$("#btnSetUserGroupDefault").click(function() {
		if(!confirm('登録されている「よく使う項目」を、このユーザーが所属するユーザーグループの初期設定として登録します。よろしいですか？')) {
			return true;
		}
		var data = {};
		$("#DefaultFavorites li").each(function(i){
			data[i] ={
				'name' : $(this).find('.favorite-name').val(), 
				'url' :$(this).find('.favorite-url').val()
			};
		});
		$.ajax({
			url: $("#UserGroupSetDefaultFavoritesUrl").html(),
			type: 'POST',
			data: data,
			dataType: 'html',
			beforeSend: function() {
				$("#Waiting").show();
				alertBox();
			},
			success: function(result){
				$("#ToTop a").click();
				if(result) {
					alertBox('登録されている「よく使う項目」を所属するユーザーグループの初期値として設定しました。');
				} else {
					alertBox('処理に失敗しました。');
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				var errorMessage = '';
				if(XMLHttpRequest.status == 404) {
					errorMessage = '<br />'+'送信先のプログラムが見つかりません。';
				} else {
					if(XMLHttpRequest.responseText) {
						errorMessage = '<br />'+XMLHttpRequest.responseText;
					} else {
						errorMessage = '<br />'+errorThrown;
					}
				}
				alertBox('処理に失敗しました。('+XMLHttpRequest.status+')'+errorMessage);
			},
			complete: function() {
				$("#Waiting").hide();
			}
		});
	});
});
</script>


<div id="SelfUpdate" style="display: none"><?php echo $selfUpdate ?></div>
<div id="AlertMessage" style="display: none"></div>
<div id="UserGroupSetDefaultFavoritesUrl" style="display:none"><?php $bcBaser->url(array('plugin' => null, 'controller' => 'user_groups', 'action' => 'set_default_favorites', $this->data['UserGroup']['id'])) ?></div>


<?php echo $bcForm->create('User') ?>
<?php echo $bcForm->hidden('User.id') ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('User.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $bcForm->value('User.id') ?>
				<?php echo $bcForm->input('User.id', array('type' => 'hidden')) ?>
			</td>
		</tr>
<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('User.name', 'アカウント名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('User.name', array('type' => 'text', 'size' => 20, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('User.name') ?>
				<div id="helptextName" class="helptext">半角英数字とハイフン、アンダースコアのみで入力してください。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('User.real_name_1', '名前') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<small>[姓]</small> <?php echo $bcForm->input('User.real_name_1', array('type' => 'text', 'size' => 12, 'maxlength' => 255)) ?>
				<small>[名]</small> <?php echo $bcForm->input('User.real_name_2', array('type' => 'text', 'size' => 12, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpRealName1', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('User.real_name_1', '姓を入力してください') ?>
				<?php echo $bcForm->error('User.real_name_2', '名を入力してください') ?>
				<div id="helptextRealName1" class="helptext"> 「名」は省略する事ができます。 </div>
			</td>
		</tr
		<tr>
			<th class="col-head"><?php echo $bcForm->label('User.nickname', 'ニックネーム') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('User.nickname', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png',array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('User.nickname') ?>
				<div class="helptext">
					ニックネームを設定している場合は全ての表示にニックネームが利用されます。
				</div>
			</td>
		</tr>

		<tr>
			<th class="col-head"><?php echo $bcForm->label('User.user_group_id', 'グループ') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
<?php if($editable): ?>
				<?php echo $bcForm->input('User.user_group_id', array('type' => 'select', 'options' => $userGroups)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpUserGroupId', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('User.user_group_id', 'グループを選択してください') ?>
				<div id="helptextUserGroupId" class="helptext"> ユーザーグループごとにコンテンツへのアクセス制限をかける場合などには
					<?php $bcBaser->link('ユーザーグループ管理',array('controller'=>'user_groups','action'=>'index')) ?>
					より新しいグループを追加しアクセス制限の設定をおこないます。</div>
<?php else: ?>
				<?php echo $bcText->arrayValue($this->data['User']['user_group_id'], $userGroups) ?>
				<?php echo $bcForm->input('User.user_group_id', array('type' => 'hidden')) ?>
<?php endif ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('User.email', 'Eメール') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('User.email', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png',array('id' => 'helpEmail', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('User.email') ?>
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
				<?php echo $bcForm->label('User.password_1', 'パスワード') ?>
			</th>
			<td class="col-input">
				<?php if($this->action == "admin_edit"): ?><small>[パスワードは変更する場合のみ入力してください]</small><br /><?php endif ?>
				<?php echo $bcForm->input('User.password_1', array('type' => 'password', 'size' => 20, 'maxlength' => 255)) ?>
				<?php echo $bcForm->input('User.password_2', array('type' => 'password', 'size' => 20, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpPassword', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('User.password') ?>
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
</div>

<div class="submit section">
	<?php echo $bcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
	<?php if ($this->action == 'admin_edit'): ?>
		<?php if(isset($bcBaser->siteConfig['demo_on']) && $bcBaser->siteConfig['demo_on']): ?>
	<p class="message">デモサイトで管理ユーザーの編集、削除はできません</p>
		<?php else: ?>
			<?php if($editable): ?>
	<?php $bcBaser->link('削除', 
			array('action' => 'delete', $bcForm->value('User.id')),
			array('class' => 'button'),
			sprintf('%s を本当に削除してもいいですか？', $bcForm->value('User.name')), false); ?>
			<?php endif ?>
		<?php endif ?>
	<?php else: ?>
	
	<?php endif ?>
</div>

<?php echo $bcForm->end() ?>

<?php if($this->action == 'admin_edit'): ?>
<div class="panel-box corner10">
	<h2>登録されている「よく使う項目」</h2>
	<?php if($this->data['Favorite']): ?>
	<ul class="clearfix" id="DefaultFavorites">
		<?php foreach($this->data['Favorite'] as $key => $favorite): ?>
		<li style="float:left">
			<?php $bcBaser->link($favorite['name'], $favorite['url']) ?>
			<?php echo $bcForm->input('Favorite.name.'.$key, array('type' => 'hidden', 'value' => $favorite['name'], 'class' => 'favorite-name')) ?>
			<?php echo $bcForm->input('Favorite.url.'.$key, array('type' => 'hidden', 'value' => $favorite['url'], 'class' => 'favorite-url')) ?>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif ?>
	<?php if($session->check('AuthAgent') || $bcBaser->isAdminUser()): ?>
	<div class="submit"><?php echo $bcForm->button($this->data['UserGroup']['title'].'グループの初期値に設定', array('label' => 'グループ初期データに設定', 'id' => 'btnSetUserGroupDefault', 'class' => 'button')) ?></div>
	<?php endif ?>
</div>
<?php endif ?>

