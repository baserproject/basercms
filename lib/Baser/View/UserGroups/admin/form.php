<?php
/**
 * [ADMIN] ユーザーグループ登録/編集フォーム
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
$authPrefixes = array();
foreach (Configure::read('BcAuthPrefix') as $key => $authPrefix) {
	$authPrefixes[$key] = $authPrefix['name'];
}
?>


<script type="text/javascript">
$(window).load(function() {
	$("#UserGroupName").focus();
});
</script>

<!-- form -->
<?php echo $this->BcForm->create('UserGroup') ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->request->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('UserGroup.id', 'NO') ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('UserGroup.id') ?>
					<?php echo $this->BcForm->input('UserGroup.id', array('type' => 'hidden')) ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('UserGroup.name', 'ユーザーグループ名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php if ($this->BcForm->value('UserGroup.name') == 'admins' && $this->request->action == 'admin_edit'): ?>
					<?php echo $this->BcForm->value('UserGroup.name') ?>
					<?php echo $this->BcForm->input('UserGroup.name', array('type' => 'hidden')) ?>
				<?php else: ?>
					<?php echo $this->BcForm->text('UserGroup.name', array('size' => 20, 'maxlength' => 255)) ?>
				<?php endif ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('UserGroup.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>重複しない識別名称を半角のみで入力してください。</li>
						<li>admins の場合は変更できません。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('UserGroup.title', '表示名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('UserGroup.title', array('type' => 'text', 'size' => 20, 'maxlength' => 255)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpTitle', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextTitle" class="helptext">日本語が入力できますのでわかりやすい名称を入力します。</div>
				<?php echo $this->BcForm->error('UserGroup.title') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('UserGroup.use_admin_globalmenu', '管理システムの<br />グローバルメニューを利用する') ?></th>
			<td class="col-input">
				<?php if ($this->BcForm->value('UserGroup.name') == 'admins' && $this->request->action == 'admin_edit'): ?>
					<?php if ($this->BcForm->value('UserGroup.use_admin_globalmenu')): ?>
						利用する
					<?php else: ?>
						利用しない
					<?php endif ?>
				<?php else: ?>
					<?php echo $this->BcForm->input('UserGroup.use_admin_globalmenu', array('type' => 'checkbox', 'label' => '利用する')) ?>
				<?php endif ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('UserGroup.use_admin_globalmenu') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>管理システムでグローバルメニューを利用するかどうか設定します。</li>
						<li>admins の場合は変更できません。</li>
					</ul>
				</div>
			</td>
		</tr>
		<?php if (count($authPrefixes) > 1): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('UserGroup.auth_prefix', '認証プレフィックス設定') ?>&nbsp;<span class="required">*</span></th>
				<td class="col-input">
					<?php if ($this->BcForm->value('UserGroup.name') == 'admins'): ?>
						<?php echo $this->BcForm->value('UserGroup.auth_prefix') ?>
						<?php echo $this->BcForm->hidden('UserGroup.auth_prefix') ?>
					<?php else: ?>
						<?php echo $this->BcForm->input('UserGroup.auth_prefix', array('type' => 'select', 'options' => $authPrefixes)) ?>
					<?php endif ?>
					<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpAuthPrefix', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
					<?php echo $this->BcForm->error('UserGroup.auth_prefix') ?>
					<div id="helptextAuthPrefix" class="helptext">
						認証プレフィックスの設定を指定します。<br />
						ユーザーグループ名が admins の場合は編集できません。
					</div>
				</td>
			</tr>
		<?php endif ?>
	</table>
</div>
<div class="submit align-center section">
	<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
	<?php if ($this->request->action == 'admin_edit'): ?>
		<?php if ($this->BcForm->value('UserGroup.name') != 'admins'): ?>
			<?php $this->BcBaser->link('削除', array('action' => 'delete', $this->BcForm->value('UserGroup.id')), array('class' => 'submit-token button'), sprintf("%s を本当に削除してもいいですか？\n\n削除する場合、関連するユーザーは削除されませんが、関連するアクセス制限設定は全て削除されます。\n※ 関連するユーザーは管理者グループに所属する事になります。", $this->BcForm->value('UserGroup.name')), false); ?>
		<?php endif; ?>
	<?php endif; ?>
</div>

<?php echo $this->BcForm->end() ?>

<?php if ($this->request->action == 'admin_edit'): ?>
	<div class="section">
		<div class="panel-box corner10">
			<h2>「よく使う項目」の初期データ</h2>
			<p>
				<small>このグループに新しいユーザーを登録した際、次の「よく使う項目」が登録されます。	</small>
			</p>
			<?php $favorites = BcUtil::unserialize($this->request->data['UserGroup']['default_favorites']) ?>
			<?php if ($favorites): ?>
			<ul class="clearfix">
				<?php foreach ($favorites as $favorite): ?>
					<li style="float:left"><?php $this->BcBaser->link($favorite['name'], $favorite['url']) ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif ?>
		</div>
	</div>
<?php endif; ?>
