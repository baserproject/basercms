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
?>


<script type="text/javascript">
$(window).load(function() {
	$("#PermissionName").focus();
});
</script>


<?php echo $bcForm->create('Permission', array('url' => array($this->params['pass'][0]))) ?>
<?php echo $bcForm->input('Permission.id', array('type' => 'hidden')) ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<tr>
			<th class="col-head"><?php echo $bcForm->label('Permission.user_group_id', 'ユーザーグループ') ?></th>
			<td class="col-input">
				<?php $userGroups = $bcForm->getControlSource('user_group_id') ?>
				<?php echo $userGroups[$bcForm->value('Permission.user_group_id')] ?>
				<?php echo $bcForm->input('Permission.user_group_id', array('type' => 'hidden')) ?>
			</td>
		</tr>
<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('Permission.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $bcForm->value('Permission.no') ?>
			</td>
		</tr>
<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('Permission.name', 'ルール名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('Permission.name', array('type' => 'text', 'size' => 20, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $form->error('Permission.name') ?>
				<div id="helptextName" class="helptext"> ルール名には日本語が利用できます。特定しやすいわかりやすい名称を入力してください。 </div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('Permission.url', 'URL設定') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<strong>/<?php echo $permissionAuthPrefix ?>/</strong>
				<?php echo $bcForm->input('Permission.url', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpUrl', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $form->error('Permission.url') ?>
				<div id="helptextUrl" class="helptext">
					<ul>
						<li>baserCMSの設置URLを除いたスラッシュから始まるURLを入力してください。<br />
							（例）/admin/users/index</li>
						<li>管理画面など認証がかかっているURLしか登録できません。</li>
						<li>特定のフォルダ配下に対しアクセスできないようにする場合などにはワイルドカード（*）を利用します。<br />
							（例）ユーザー管理内のURL全てアクセスさせない場合： /admin/users* </li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('Permission.auth', 'アクセス') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('Permission.auth', array(
					'type'		=> 'radio',
					'options'	=> $bcForm->getControlSource('auth'),
					'legend'	=> false,
					'separator'	=> '　')) ?>
				<?php echo $bcForm->error('Permission.auth') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('Permission.status', '利用状態') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('Permission.status', array(
					'type'		=> 'radio',
					'options'	=> $bcText->booleanStatusList(),
					'legend'	=> false,
					'separator'	=> '　')) ?>
				<?php echo $bcForm->error('Permission.status') ?>
			</td>
		</tr>
	</table>
</div>
<div class="submit">
	<?php echo $bcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php if ($this->action == 'admin_edit'): ?>
	<?php $bcBaser->link('削除', 
			array('action' => 'delete', $this->params['pass'][0], $bcForm->value('Permission.id')),
			array('class' => 'button'),
			sprintf('%s を本当に削除してもいいですか？', $bcForm->value('Permission.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $bcForm->end() ?>