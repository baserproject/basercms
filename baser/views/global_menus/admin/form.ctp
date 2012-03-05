<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] メニューフォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>



<?php echo $formEx->create('GlobalMenu') ?>
<?php echo $formEx->input('GlobalMenu.id', array('type' => 'hidden')) ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('GlobalMenu.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $formEx->value('GlobalMenu.no') ?>
				<?php echo $formEx->input('GlobalMenu.no', array('type' => 'hidden')) ?>
			</td>
		</tr>
<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('GlobalMenu.name', 'メニュー名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('GlobalMenu.name', array('type' => 'text', 'size' => 40, 'maxlength' => 20)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>日本語が利用できます。</li>
						<li>識別しやすくわかりやすい名前を入力します。</li>
					</ul>
				</div>
				<?php echo $formEx->error('GlobalMenu.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('GlobalMenu.link', 'リンクURL') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('GlobalMenu.link', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpLink', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('GlobalMenu.link') ?>
				<div id="helptextLink" class="helptext"> 先頭にスラッシュつけたルートパスで入力してください。<br />
					(例) /admin/global/index </div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('GlobalMenu.status', '利用状態') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('GlobalMenu.status', array(
						'type'		=> 'radio',
						'options'	=> $textEx->booleanDoList("利用"),
						'legend'	=> false,
						'separator'	=> '&nbsp;&nbsp;')) ?>
				<?php echo $formEx->error('GlobalMenu.status') ?>
			</td>
		</tr>
	</table>
</div>
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->submit('登録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php else: ?>
	<?php echo $formEx->submit('更新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削除', 
			array('action' => 'delete', $formEx->value('GlobalMenu.id')),
			array('class' => 'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('GlobalMenu.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>