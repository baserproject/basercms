<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ユーザー フォーム
 */
$this->BcBaser->i18nScript([
	'alertMessage1' => __d('baser', '処理に失敗しました。'),
	'alertMessage2' => __d('baser', '送信先のプログラムが見つかりません。'),
	'confirmMessage1' => __d('baser', '更新内容をログイン情報に反映する為、一旦ログアウトします。よろしいですか？'),
	'confirmMessage2' => __d('baser', '登録されている「よく使う項目」を、このユーザーが所属するユーザーグループの初期設定として登録します。よろしいですか？'),
	'infoMessage1' => __d('baser', '登録されている「よく使う項目」を所属するユーザーグループの初期値として設定しました。'),
]);
$this->BcBaser->js('admin/users/edit', false);
?>


<script type="text/javascript">

</script>


<div id="SelfUpdate" style="display: none"><?php echo $selfUpdate ?></div>
<div id="AlertMessage" style="display: none"></div>
<div id="UserGroupSetDefaultFavoritesUrl"
	 style="display:none"><?php $this->BcBaser->url(['plugin' => null, 'controller' => 'user_groups', 'action' => 'set_default_favorites', @$this->request->data['UserGroup']['id']]) ?></div>


<?php echo $this->BcForm->create('User') ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcForm->hidden('User.id') ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table bca-form-table">
		<?php if ($this->request->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('User.id', 'No') ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->value('User.id') ?>
					<?php echo $this->BcForm->input('User.id', ['type' => 'hidden']) ?>
				</td>
			</tr>
		<?php endif ?>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('User.name', __d('baser', 'アカウント名')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('User.name', ['type' => 'text', 'size' => 20, 'maxlength' => 255, 'autofocus' => true]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('User.name') ?>
				<div id="helptextName"
					 class="helptext"><?php echo __d('baser', '半角英数字とハイフン、アンダースコアのみで入力してください。') ?></div>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('User.real_name_1', __d('baser', '名前')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<small>[<?php echo __d('baser', '姓') ?>
					]</small> <?php echo $this->BcForm->input('User.real_name_1', ['type' => 'text', 'size' => 12, 'maxlength' => 255]) ?>
				<small>[<?php echo __d('baser', '名') ?>
					]</small> <?php echo $this->BcForm->input('User.real_name_2', ['type' => 'text', 'size' => 12, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('User.real_name_1', __d('baser', '姓を入力してください')) ?>
				<?php echo $this->BcForm->error('User.real_name_2', __d('baser', '名を入力してください')) ?>
				<div id="helptextRealName1" class="helptext"><?php echo __d('baser', '「名」は省略する事ができます。') ?></div>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('User.nickname', __d('baser', 'ニックネーム')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('User.nickname', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('User.nickname') ?>
				<div id="helptextNickname"
					 class="helptext"><?php echo __d('baser', 'ニックネームを設定している場合は全ての表示にニックネームが利用されます。') ?></div>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('User.user_group_id', __d('baser', 'グループ')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php if ($editable): ?>
					<?php echo $this->BcForm->input('User.user_group_id', ['type' => 'select', 'options' => $userGroups]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcForm->error('User.user_group_id', __d('baser', 'グループを選択してください')) ?>
					<div id="helptextUserGroupId"
						 class="helptext"><?php echo sprintf(__d('baser', 'ユーザーグループごとにコンテンツへのアクセス制限をかける場合などには%sより新しいグループを追加しアクセス制限の設定をおこないます。'), $this->BcBaser->getLink(__d('baser', 'ユーザーグループ管理'), ['controller' => 'user_groups', 'action' => 'index'])) ?></div>
				<?php else: ?>
					<?php echo h($this->BcText->arrayValue($this->request->data['User']['user_group_id'], $userGroups)) ?>
					<?php echo $this->BcForm->input('User.user_group_id', ['type' => 'hidden']) ?>
				<?php endif ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('User.email', __d('baser', 'Eメール')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('User.email', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('User.email') ?>
				<div id="helptextEmail" class="helptext">
					<?php echo __d('baser', '連絡用メールアドレスを入力します。') ?>
					<br><small>※ <?php echo __d('baser', 'パスワードを忘れた場合の新パスワードの通知先等') ?></small>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label">
				<?php echo $this->BcForm->label('User.password_1', __d('baser', 'パスワード')) ?>
				<?php if ($this->request->action == 'admin_add'): ?>
					<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>&nbsp;
				<?php endif; ?>
			</th>
			<td class="col-input bca-form-table__input">
				<?php if ($this->request->action == "admin_edit"): ?><small>
					[<?php echo __d('baser', 'パスワードは変更する場合のみ入力してください') ?>]</small><br/><?php endif ?>
				<!-- ↓↓↓自動入力を防止する為のダミーフィールド↓↓↓ -->
				<input type="password" name="dummypass" style="top:-100px;left:-100px;position:fixed;"/>
				<?php echo $this->BcForm->input('User.password_1', ['type' => 'password', 'size' => 20, 'maxlength' => 255]) ?>
				<?php echo $this->BcForm->input('User.password_2', ['type' => 'password', 'size' => 20, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('User.password') ?>
				<div id="helptextPassword" class="helptext">
					<ul>
						<li>
							<?php if ($this->request->action == "admin_edit"): ?>
								<?php echo __d('baser', 'パスワードの変更をする場合は、') ?>
							<?php endif; ?>
							<?php echo __d('baser', '確認の為２回入力してください。') ?></li>
						<li><?php echo __d('baser', '半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&;{}!$*)のみで入力してください') ?></li>
						<li><?php echo __d('baser', '最低６文字以上で入力してください') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit section bca-actions">
	<div class="bca-actions__main">
		<?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn bca-actions__item', 'data-bca-btn-type' => 'save', 'data-bca-btn-size' => 'lg', 'data-bca-btn-width' => 'lg', 'id' => 'BtnSave']) ?>
	</div>
	<?php if ($editable): ?>
		<div class="bca-actions__sub">
			<?php if ($this->request->action == 'admin_edit' && $deletable): ?>
				<?php $this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $this->BcForm->value('User.id')], ['class' => 'submit-token button bca-btn bca-actions__item', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'sm'], sprintf(__d('baser', '%s を本当に削除してもいいですか？'), $this->BcForm->value('User.name')), false); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>

<?php echo $this->BcForm->end() ?>

<?php if ($this->request->action == 'admin_edit'): ?>
	<div class="panel-box bca-panel-box corner10">
		<h2><?php echo __d('baser', '登録されている「よく使う項目」') ?></h2>
		<?php if ($this->request->data['Favorite']): ?>
			<ul class="bca-list" data-bca-list-layout="horizon" id="DefaultFavorites">
				<?php foreach($this->request->data['Favorite'] as $key => $favorite): ?>
					<li class="bca-list__item">
						<?php $this->BcBaser->link($favorite['name'], $favorite['url'], ['escape' => true]) ?>
						<?php echo $this->BcForm->input('Favorite.name.' . $key, ['type' => 'hidden', 'value' => $favorite['name'], 'class' => 'favorite-name']) ?>
						<?php echo $this->BcForm->input('Favorite.url.' . $key, ['type' => 'hidden', 'value' => $favorite['url'], 'class' => 'favorite-url']) ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif ?>
		<?php if ($this->Session->check('AuthAgent') || $this->BcBaser->isAdminUser()): ?>
			<div
				class="submit"><?php echo $this->BcForm->button(h($this->request->data['UserGroup']['title']) . 'グループの初期値に設定', ['label' => __d('baser', 'グループ初期データに設定'), 'id' => 'btnSetUserGroupDefault', 'class' => 'button bca-btn']) ?></div>
		<?php endif ?>
	</div>
<?php endif ?>
