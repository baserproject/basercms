<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ユーザーグループ登録/編集フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<?php echo $formEx->create('Permission', array('url' => array($this->params['pass'][0]))) ?>
<?php echo $formEx->input('Permission.id', array('type' => 'hidden')) ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<tr>
			<th class="col-head"><?php echo $formEx->label('Permission.user_group_id', 'ユーザーグループ') ?></th>
			<td class="col-input">
				<?php $userGroups = $formEx->getControlSource('user_group_id') ?>
				<?php echo $userGroups[$formEx->value('Permission.user_group_id')] ?>
				<?php echo $formEx->input('Permission.user_group_id', array('type' => 'hidden')) ?>
			</td>
		</tr>
<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('Permission.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $formEx->value('Permission.no') ?>
			</td>
		</tr>
<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('Permission.name', 'ルール名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('Permission.name', array('type' => 'text', 'size' => 20, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $form->error('Permission.name') ?>
				<div id="helptextName" class="helptext"> ルール名には日本語が利用できます。特定しやすいわかりやすい名称を入力してください。 </div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('Permission.url', 'URL設定') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<strong>/<?php echo $authPrefix ?>/</strong>
				<?php echo $formEx->input('Permission.url', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
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
			<th class="col-head"><?php echo $formEx->label('Permission.auth', 'アクセス') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('Permission.auth', array(
					'type'		=> 'radio',
					'options'	=> $formEx->getControlSource('auth'),
					'legend'	=> false,
					'separator'	=> '　')) ?>
				<?php echo $formEx->error('Permission.auth') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('Permission.status', '利用状態') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('Permission.status', array(
					'type'		=> 'radio',
					'options'	=> $textEx->booleanStatusList(),
					'legend'	=> false,
					'separator'	=> '　')) ?>
				<?php echo $formEx->error('Permission.status') ?>
			</td>
		</tr>
	</table>
</div>
<div class="submit">
<?php if ($this->action == 'admin_edit'): ?>
	<?php echo $formEx->submit('更新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削除', 
			array('action'=>'delete', $this->params['pass'][0], $formEx->value('Permission.id')),
			array('class'=>'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('Permission.name')),
			false); ?>
<?php else: ?>
	<?php echo $formEx->submit('登　録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>