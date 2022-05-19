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
 * [ADMIN] ユーザーグループ登録/編集フォーム
 *
 * @var BcAppView $this
 */
$this->BcBaser->js('admin/user_groups/form', false);
$authPrefixes = [];
foreach(Configure::read('BcAuthPrefix') as $key => $authPrefix) {
	$authPrefixes[$key] = $authPrefix['name'];
}
?>


<script type="text/javascript">
	$(window).load(function () {
		<?php if ($this->BcForm->value('UserGroup.name') == 'admins'): ?>
		$("#UserGroupAuthPrefixAdmin").prop('disabled', true);
		<?php endif ?>
		$("#UserGroupAdminEditForm").submit(function () {
			$("#UserGroupAuthPrefixAdmin").removeAttr('disabled');
		});
	});
</script>

<!-- form -->
<?php echo $this->BcForm->create('UserGroup') ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->request->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('UserGroup.id', 'NO') ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('UserGroup.id') ?>
					<?php echo $this->BcForm->input('UserGroup.id', ['type' => 'hidden']) ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('UserGroup.name', __d('baser', 'ユーザーグループ名')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php if ($this->BcForm->value('UserGroup.name') == 'admins' && $this->request->action == 'admin_edit'): ?>
					<?php echo $this->BcForm->value('UserGroup.name') ?>
					<?php echo $this->BcForm->input('UserGroup.name', ['type' => 'hidden']) ?>
				<?php else: ?>
					<?php echo $this->BcForm->text('UserGroup.name', ['size' => 20, 'maxlength' => 255, 'autofocus' => true]) ?>
				<?php endif ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpName', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('UserGroup.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li><?php echo __d('baser', '重複しない識別名称を半角のみで入力してください。') ?></li>
						<li><?php echo __d('baser', 'admins の場合は変更できません。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('UserGroup.title', __d('baser', '表示名')) ?>&nbsp;<span
					class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('UserGroup.title', ['type' => 'text', 'size' => 20, 'maxlength' => 255]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpTitle', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div id="helptextTitle" class="helptext"><?php echo __d('baser', '日本語が入力できますのでわかりやすい名称を入力します。') ?></div>
				<?php echo $this->BcForm->error('UserGroup.title') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('UserGroup.use_admin_globalmenu', __d('baser', 'その他')) ?></th>
			<td class="col-input">
				<?php if ($this->BcForm->value('UserGroup.name') == 'admins' && $this->request->action == 'admin_edit'): ?>
					<?php if ($this->BcForm->value('UserGroup.use_admin_globalmenu')): ?>
						<?php echo __d('baser', '管理システムのグローバルメニューを利用する') ?>
					<?php else: ?>
						<?php echo __d('baser', '管理システムのグローバルメニューを利用しない') ?>
					<?php endif ?>
				<?php else: ?>
					<span
						style="white-space: nowrap"><?php echo $this->BcForm->input('UserGroup.use_admin_globalmenu', ['type' => 'checkbox', 'label' => __d('baser', '管理システムのグローバルメニューを利用する')]) ?>　</span>
					<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpName', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<div id="helptextName" class="helptext">
						<ul>
							<li><?php echo __d('baser', '管理システムでグローバルメニューを利用するかどうか設定します。') ?></li>
							<li><?php echo __d('baser', '管理グループの場合は変更できません。') ?></li>
						</ul>
					</div>
					<?php echo $this->BcForm->error('UserGroup.use_admin_globalmenu') ?>
				<?php endif ?>
				<br>
				<span
					style="white-space: nowrap"><?php echo $this->BcForm->input('UserGroup.use_move_contents', ['type' => 'checkbox', 'label' => __d('baser', 'コンテンツのドラッグ＆ドロップ移動機能を利用する')]) ?></span>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpName', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<div id="helptextName" class="helptext">
					<span>
                        <?php echo __d('baser', 'コンテンツ一覧のツリー構造において、ドラッグ＆ドロップでコンテンツの移動を許可するかどうかを設定します。') ?>
					</span>
				</div>
				<?php echo $this->BcForm->error('UserGroup.use_move_contents') ?>
			</td>
		</tr>
		<?php if (count($authPrefixes) > 1): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('UserGroup.auth_prefix', __d('baser', '認証プレフィックス設定')) ?>
					&nbsp;<span class="required">*</span></th>
				<td class="col-input">
					<?php echo $this->BcForm->input('UserGroup.auth_prefix', ['type' => 'select', 'multiple' => 'checkbox', 'options' => $authPrefixes, 'value' => explode(',', $this->BcForm->value('UserGroup.auth_prefix'))]) ?>
					<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpAuthPrefix', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<?php echo $this->BcForm->error('UserGroup.auth_prefix') ?>
					<div id="helptextAuthPrefix" class="helptext">
						<?php echo __d('baser', '認証プレフィックスの設定を指定します。<br />ユーザーグループ名が admins の場合は編集できません。') ?>
					</div>
				</td>
			</tr>
		<?php endif ?>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit align-center section">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
	<?php if ($this->request->action == 'admin_edit'): ?>
		<?php if ($this->BcForm->value('UserGroup.name') != 'admins'): ?>
			<?php $this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $this->BcForm->value('UserGroup.id')], ['class' => 'submit-token button'], sprintf(__d('baser', "%s を本当に削除してもいいですか？\n\n削除する場合、関連するユーザーは削除されませんが、関連するアクセス制限設定は全て削除されます。\n※ 関連するユーザーは管理者グループに所属する事になります。"), $this->BcForm->value('UserGroup.name'))) ?>
		<?php endif; ?>
	<?php endif; ?>
</div>

<?php echo $this->BcForm->end() ?>

<?php if ($this->request->action == 'admin_edit'): ?>
	<div class="section">
		<div class="panel-box corner10">
			<h2><?php echo __d('baser', '「よく使う項目」の初期データ') ?></h2>
			<p>
				<small><?php echo __d('baser', 'このグループに新しいユーザーを登録した際、次の「よく使う項目」が登録されます。	') ?></small>
			</p>
			<?php $favorites = BcUtil::unserialize($this->request->data['UserGroup']['default_favorites']) ?>
			<?php if ($favorites): ?>
				<ul class="clearfix">
					<?php foreach($favorites as $favorite): ?>
						<li style="float:left"><?php $this->BcBaser->link($favorite['name'], $favorite['url'], ['escape' => true]) ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif ?>
		</div>
	</div>
<?php endif; ?>
