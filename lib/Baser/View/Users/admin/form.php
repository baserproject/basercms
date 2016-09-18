<?php
/**
 * [ADMIN] ユーザー フォーム
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
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

		$.bcToken.check(function(){
			data = $.extend(data, {
				_Token: {
					key: $.bcToken.key
				}
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
});
</script>


<div id="SelfUpdate" style="display: none"><?php echo $selfUpdate ?></div>
<div id="AlertMessage" style="display: none"></div>
<div id="UserGroupSetDefaultFavoritesUrl" style="display:none"><?php $this->BcBaser->url(array('plugin' => null, 'controller' => 'user_groups', 'action' => 'set_default_favorites', @$this->request->data['UserGroup']['id'])) ?></div>


<?php echo $this->BcForm->create('User') ?>
<?php echo $this->BcForm->hidden('User.id') ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->request->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('User.id', 'NO') ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('User.id') ?>
					<?php echo $this->BcForm->input('User.id', array('type' => 'hidden')) ?>
				</td>
			</tr>
		<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('User.name', 'アカウント名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('User.name', array('type' => 'text', 'size' => 20, 'maxlength' => 255)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('User.name') ?>
				<div id="helptextName" class="helptext">半角英数字とハイフン、アンダースコアのみで入力してください。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('User.real_name_1', '名前') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<small>[姓]</small> <?php echo $this->BcForm->input('User.real_name_1', array('type' => 'text', 'size' => 12, 'maxlength' => 255)) ?>
				<small>[名]</small> <?php echo $this->BcForm->input('User.real_name_2', array('type' => 'text', 'size' => 12, 'maxlength' => 255)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpRealName1', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('User.real_name_1', '姓を入力してください') ?>
				<?php echo $this->BcForm->error('User.real_name_2', '名を入力してください') ?>
				<div id="helptextRealName1" class="helptext"> 「名」は省略する事ができます。 </div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('User.nickname', 'ニックネーム') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('User.nickname', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('User.nickname') ?>
				<div id="helptextNickname" class="helptext">ニックネームを設定している場合は全ての表示にニックネームが利用されます。</div>
			</td> 
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('User.user_group_id', 'グループ') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php if ($editable): ?>
					<?php echo $this->BcForm->input('User.user_group_id', array('type' => 'select', 'options' => $userGroups)) ?>
					<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpUserGroupId', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
					<?php echo $this->BcForm->error('User.user_group_id', 'グループを選択してください') ?>
					<div id="helptextUserGroupId" class="helptext"> ユーザーグループごとにコンテンツへのアクセス制限をかける場合などには
						<?php $this->BcBaser->link('ユーザーグループ管理', array('controller' => 'user_groups', 'action' => 'index')) ?>
						より新しいグループを追加しアクセス制限の設定をおこないます。</div>
				<?php else: ?>
					<?php echo $this->BcText->arrayValue($this->request->data['User']['user_group_id'], $userGroups) ?>
					<?php echo $this->BcForm->input('User.user_group_id', array('type' => 'hidden')) ?>
				<?php endif ?>	
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('User.email', 'Eメール') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('User.email', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpEmail', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('User.email') ?>
				<div id="helptextEmail" class="helptext">
					連絡用メールアドレスを入力します。<br /><small>※ パスワードを忘れた場合の新パスワードの通知先等</small>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head">
				<?php if ($this->request->action == 'admin_add'): ?>
					<span class="required">*</span>&nbsp;
				<?php endif; ?>
				<?php echo $this->BcForm->label('User.password_1', 'パスワード') ?>
			</th>
			<td class="col-input">
				<?php if ($this->request->action == "admin_edit"): ?><small>[パスワードは変更する場合のみ入力してください]</small><br /><?php endif ?>
				<?php echo $this->BcForm->input('User.password_1', array('type' => 'password', 'size' => 20, 'maxlength' => 255)) ?>
				<?php echo $this->BcForm->input('User.password_2', array('type' => 'password', 'size' => 20, 'maxlength' => 255)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpPassword', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('User.password') ?>
				<div id="helptextPassword" class="helptext">
					<ul>
						<li>
							<?php if ($this->request->action == "admin_edit"): ?>
								パスワードの変更をする場合は、
							<?php endif; ?>
							確認の為２回入力してください。</li>
						<li>半角英数字とハイフン、アンダースコアのみで入力してください</li>
						<li>最低６文字以上で入力してください</li>
					</ul>
				</div>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<div class="submit section">
	<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php if ($editable): ?>
	<?php if ($this->request->action == 'admin_edit' && $deletable): ?>
			<?php $this->BcBaser->link('削除', array('action' => 'delete', $this->BcForm->value('User.id')), array('class' => 'submit-token button'), sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('User.name')), false); ?>
	<?php endif; ?>
<?php endif; ?>
</div>

<?php echo $this->BcForm->end() ?>

<?php if ($this->request->action == 'admin_edit'): ?>
	<div class="panel-box corner10">
		<h2>登録されている「よく使う項目」</h2>
		<?php if ($this->request->data['Favorite']): ?>
			<ul class="clearfix" id="DefaultFavorites">
				<?php foreach ($this->request->data['Favorite'] as $key => $favorite): ?>
					<li style="float:left">
						<?php $this->BcBaser->link($favorite['name'], $favorite['url']) ?>
						<?php echo $this->BcForm->input('Favorite.name.' . $key, array('type' => 'hidden', 'value' => $favorite['name'], 'class' => 'favorite-name')) ?>
						<?php echo $this->BcForm->input('Favorite.url.' . $key, array('type' => 'hidden', 'value' => $favorite['url'], 'class' => 'favorite-url')) ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif ?>
		<?php if ($this->Session->check('AuthAgent') || $this->BcBaser->isAdminUser()): ?>
			<div class="submit"><?php echo $this->BcForm->button($this->request->data['UserGroup']['title'] . 'グループの初期値に設定', array('label' => 'グループ初期データに設定', 'id' => 'btnSetUserGroupDefault', 'class' => 'button')) ?></div>
		<?php endif ?>
	</div>
<?php endif ?>
