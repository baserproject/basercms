<?php
/**
 * [ADMIN] メニューフォーム
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
?>


<script type="text/javascript">
$(window).load(function() {
	$("#MenuName").focus();
});
</script>


<?php echo $this->BcForm->create('Menu') ?>
<?php echo $this->BcForm->input('Menu.id', array('type' => 'hidden')) ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->request->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('Menu.id', 'NO') ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('Menu.no') ?>
					<?php echo $this->BcForm->input('Menu.no', array('type' => 'hidden')) ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Menu.name', 'メニュー名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Menu.name', array('type' => 'text', 'size' => 40, 'maxlength' => 20)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>日本語が利用できます。</li>
						<li>識別しやすくわかりやすい名前を入力します。</li>
					</ul>
				</div>
				<?php echo $this->BcForm->error('Menu.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Menu.link', 'リンクURL') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Menu.link', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpLink', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('Menu.link') ?>
				<div id="helptextLink" class="helptext"> 先頭にスラッシュつけたルートパスで入力してください。<br />
					(例) /admin/global/index </div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Menu.status', '利用状態') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Menu.status', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('利用'))) ?>
				<?php echo $this->BcForm->error('Menu.status') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>
<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
	<?php if ($this->action == 'admin_edit'): ?>
		<?php $this->BcBaser->link('削除', array('action' => 'delete', $this->BcForm->value('Menu.id')), array('class' => 'submit-token button'), sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('Menu.name')), false); ?>
	<?php endif; ?>
</div>

<?php echo $this->BcForm->end() ?>