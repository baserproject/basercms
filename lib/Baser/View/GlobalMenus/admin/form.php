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


<?php echo $this->BcForm->create('GlobalMenu') ?>
<?php echo $this->BcForm->input('GlobalMenu.id', array('type' => 'hidden')) ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
<?php if($this->request->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('GlobalMenu.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->value('GlobalMenu.no') ?>
				<?php echo $this->BcForm->input('GlobalMenu.no', array('type' => 'hidden')) ?>
			</td>
		</tr>
<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('GlobalMenu.name', 'メニュー名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('GlobalMenu.name', array('type' => 'text', 'size' => 40, 'maxlength' => 20)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>日本語が利用できます。</li>
						<li>識別しやすくわかりやすい名前を入力します。</li>
					</ul>
				</div>
				<?php echo $this->BcForm->error('GlobalMenu.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('GlobalMenu.link', 'リンクURL') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('GlobalMenu.link', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpLink', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('GlobalMenu.link') ?>
				<div id="helptextLink" class="helptext"> 先頭にスラッシュつけたルートパスで入力してください。<br />
					(例) /admin/global/index </div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('GlobalMenu.status', '利用状態') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('GlobalMenu.status', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('利用'))) ?>
				<?php echo $this->BcForm->error('GlobalMenu.status') ?>
			</td>
		</tr>
	</table>
</div>
<div class="submit">
<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php if($this->action == 'admin_edit'): ?>
	<?php $this->BcBaser->link('削除', 
			array('action' => 'delete', $this->BcForm->value('GlobalMenu.id')),
			array('class' => 'button'),
			sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('GlobalMenu.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>