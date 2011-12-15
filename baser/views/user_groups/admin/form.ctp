<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ユーザーグループ登録/編集フォーム
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$authPrefixes = array();
foreach(Configure::read('AuthPrefix') as $key => $authPrefix) {
	$authPrefixes[$key] = $key;
}
?>

<!-- title -->
<h2><?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpAdmin', 'class' => 'slide-trigger', 'alt' => 'ヘルプ')) ?></h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ログインするユーザーのグループを登録します。コンテンツへの権限がわかりやすい名称で登録します。</p>
</div>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<!-- form -->
<?php echo $formEx->create('UserGroup') ?>

<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('UserGroup.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $formEx->value('UserGroup.id') ?>
			<?php echo $formEx->input('UserGroup.id', array('type' => 'hidden')) ?>
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('UserGroup.name', 'ユーザーグループ名') ?></th>
		<td class="col-input">
<?php if($formEx->value('UserGroup.name')=='admins' && $this->action == 'admin_edit'): ?>
			<?php echo $formEx->value('UserGroup.name') ?>
			<?php echo $formEx->input('UserGroup.name', array('type' => 'hidden')) ?>
<?php else: ?>
			<?php echo $formEx->text('UserGroup.name', array('size' => 20, 'maxlength' => 255)) ?>
<?php endif ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id' => 'helpName', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('UserGroup.name') ?>
			<div id="helptextName" class="helptext">
				<ul>
					<li>重複しない識別名称を半角のみで入力してください。</li>
					<li>admins の場合は変更できません。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('UserGroup.title', '表示名') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('UserGroup.title', array('type' => 'text', 'size' => 20, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpTitle', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<div id="helptextTitle" class="helptext">日本語が入力できますのでわかりやすい名称を入力します。</div>
			<?php echo $formEx->error('UserGroup.title') ?>
		</td>
	</tr>
<?php if(count($authPrefixes) > 1): ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('UserGroup.auth_prefix', '認証プレフィックス') ?></th>
		<td class="col-input">
			<?php if($formEx->value('UserGroup.name') == 'admins'): ?>
				<?php echo $formEx->value('UserGroup.auth_prefix') ?>
				<?php echo $formEx->hidden('UserGroup.auth_prefix') ?>
			<?php else: ?>
				<?php echo $formEx->input('UserGroup.auth_prefix', array('type' => 'select', 'options' => $authPrefixes)) ?>
			<?php endif ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id' => 'helpAuthPrefix', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('UserGroup.auth_prefix') ?>
			<div id="helptextAuthPrefix" class="helptext">所属するプレフィックスコンテンツを指定します。<br />ユーザーグループ名が admins の場合は編集できません。</div>
		</td>
	</tr>
<?php endif ?>
</table>

<div class="align-center">
<?php if ($this->action == 'admin_edit'): ?>
	<?php echo $formEx->submit('更　新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php if ($formEx->value('UserGroup.name') != 'admins'): ?>
		<?php $baser->link('削　除', 
				array('action'=>'delete', $formEx->value('UserGroup.id')),
				array('class'=>'btn-gray button'),
				sprintf('%s を本当に削除してもいいですか？\n\n削除する場合、関連するユーザーは削除されませんが、関連するアクセス制限設定は全て削除されます。\n※ 関連するユーザーは管理者グループに所属する事になります。',
				$formEx->value('UserGroup.name')), false); ?>
	<?php endif ?>
<?php else: ?>
	<?php echo $formEx->submit('登　録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>