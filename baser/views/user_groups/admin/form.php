<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ユーザーグループ登録/編集フォーム
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
$authPrefixes = array();
foreach(Configure::read('BcAuthPrefix') as $key => $authPrefix) {
	$authPrefixes[$key] = $authPrefix['name'];
}
?>


<script type="text/javascript">
$(window).load(function() {
	$("#UserGroupName").focus();
});
</script>

<!-- form -->
<?php echo $bcForm->create('UserGroup') ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('UserGroup.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $bcForm->value('UserGroup.id') ?>
				<?php echo $bcForm->input('UserGroup.id', array('type' => 'hidden')) ?>
			</td>
		</tr>
<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('UserGroup.name', 'ユーザーグループ名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
<?php if($bcForm->value('UserGroup.name')=='admins' && $this->action == 'admin_edit'): ?>
				<?php echo $bcForm->value('UserGroup.name') ?>
				<?php echo $bcForm->input('UserGroup.name', array('type' => 'hidden')) ?>
<?php else: ?>
				<?php echo $bcForm->text('UserGroup.name', array('size' => 20, 'maxlength' => 255)) ?>
<?php endif ?>
				<?php echo $html->image('admin/icn_help.png',array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('UserGroup.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>重複しない識別名称を半角のみで入力してください。</li>
						<li>admins の場合は変更できません。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('UserGroup.title', '表示名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('UserGroup.title', array('type' => 'text', 'size' => 20, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpTitle', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextTitle" class="helptext">日本語が入力できますのでわかりやすい名称を入力します。</div>
				<?php echo $bcForm->error('UserGroup.title') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('UserGroup.use_admin_globalmenu', '管理システムの<br />グローバルメニューを利用する') ?></th>
			<td class="col-input">
<?php if($bcForm->value('UserGroup.name') == 'admins' && $this->action == 'admin_edit'): ?>
				<?php if($bcForm->value('UserGroup.use_admin_globalmenu')): ?>
				利用する
				<?php else: ?>
				利用しない
				<?php endif ?>
<?php else: ?>
				<?php echo $bcForm->input('UserGroup.use_admin_globalmenu', array('type' => 'checkbox', 'label' => '利用する')) ?>
<?php endif ?>
				<?php echo $html->image('admin/icn_help.png',array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('UserGroup.use_admin_globalmenu') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>管理システムでグローバルメニューを利用するかどうか設定します。</li>
						<li>admins の場合は変更できません。</li>
					</ul>
				</div>
			</td>
		</tr>
<?php if(count($authPrefixes) > 1): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('UserGroup.auth_prefix', '認証プレフィックス設定') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php if($bcForm->value('UserGroup.name') == 'admins'): ?>
					<?php echo $bcForm->value('UserGroup.auth_prefix') ?>
					<?php echo $bcForm->hidden('UserGroup.auth_prefix') ?>
				<?php else: ?>
					<?php echo $bcForm->input('UserGroup.auth_prefix', array('type' => 'select', 'options' => $authPrefixes)) ?>
				<?php endif ?>
				<?php echo $html->image('admin/icn_help.png',array('id' => 'helpAuthPrefix', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('UserGroup.auth_prefix') ?>
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
	<?php echo $bcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php if ($this->action == 'admin_edit'): ?>
	<?php if ($bcForm->value('UserGroup.name') != 'admins'): ?>
		<?php $bcBaser->link('削除', 
				array('action' => 'delete', $bcForm->value('UserGroup.id')),
				array('class' => 'button'),
				sprintf('%s を本当に削除してもいいですか？\n\n削除する場合、関連するユーザーは削除されませんが、関連するアクセス制限設定は全て削除されます。\n※ 関連するユーザーは管理者グループに所属する事になります。',
				$bcForm->value('UserGroup.name')), false); ?>
	<?php endif ?>
<?php endif ?>
</div>

<?php echo $bcForm->end() ?>

<?php if($this->action == 'admin_edit'): ?>
<div class="section">
	<div class="panel-box corner10">
		<h2>「よく使う項目」の初期データ</h2>
		<p>
			<small>このグループに新しいユーザーを登録した際、次の「よく使う項目」が登録されます。	</small>
		</p>
	<?php $favorites = unserialize($this->data['UserGroup']['default_favorites']) ?>
	<?php if($favorites): ?>
		<ul class="clearfix">
		<?php foreach($favorites as $favorite): ?>
			<li style="float:left"><?php $bcBaser->link($favorite['name'], $favorite['url']) ?></li>
		<?php endforeach; ?>
		</ul>
	<?php endif ?>
	</div>
</div>
<?php endif ?>