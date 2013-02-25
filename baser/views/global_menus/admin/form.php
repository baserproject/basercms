<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] メニューフォーム
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
	$("#GlobalMenuName").focus();
});
</script>


<?php echo $bcForm->create('GlobalMenu') ?>
<?php echo $bcForm->input('GlobalMenu.id', array('type' => 'hidden')) ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('GlobalMenu.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $bcForm->value('GlobalMenu.no') ?>
				<?php echo $bcForm->input('GlobalMenu.no', array('type' => 'hidden')) ?>
			</td>
		</tr>
<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('GlobalMenu.name', 'メニュー名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('GlobalMenu.name', array('type' => 'text', 'size' => 40, 'maxlength' => 20)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>日本語が利用できます。</li>
						<li>識別しやすくわかりやすい名前を入力します。</li>
					</ul>
				</div>
				<?php echo $bcForm->error('GlobalMenu.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('GlobalMenu.link', 'リンクURL') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('GlobalMenu.link', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpLink', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('GlobalMenu.link') ?>
				<div id="helptextLink" class="helptext"> 先頭にスラッシュつけたルートパスで入力してください。<br />
					(例) /admin/global/index </div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('GlobalMenu.status', '利用状態') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('GlobalMenu.status', array(
						'type'		=> 'radio',
						'options'	=> $bcText->booleanDoList("利用"),
						'legend'	=> false,
						'separator'	=> '&nbsp;&nbsp;')) ?>
				<?php echo $bcForm->error('GlobalMenu.status') ?>
			</td>
		</tr>
	</table>
</div>
<div class="submit">
	<?php echo $bcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php if($this->action == 'admin_edit'): ?>
	<?php $bcBaser->link('削除', 
			array('action' => 'delete', $bcForm->value('GlobalMenu.id')),
			array('class' => 'button'),
			sprintf('%s を本当に削除してもいいですか？', $bcForm->value('GlobalMenu.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $bcForm->end() ?>