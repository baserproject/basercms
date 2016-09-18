<?php
/**
 * [ADMIN] テーマ フォーム
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
	$("#ThemeName").focus();
});
</script>

<?php if ($folderDisabled): ?>
	<p><span class="required">テーマフォルダに書込権限がありません。</span></p>
<?php endif ?>
<?php if ($configDisabled): ?>
	<p><span class="required">テーマ設定ファイルに書込権限がありません。</span></p>
<?php endif ?>


<?php echo $this->BcForm->create('Theme', array('action' => 'edit', 'url' => array('action' => 'edit', $theme))) ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Theme.name', 'テーマ名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Theme.name', array('type' => 'text', 'size' => 20, 'maxlength' => 255, 'disabled' => $folderDisabled)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('Theme.name') ?>
				<div id="helptextName" class="helptext"> 半角英数字のみで入力してください。 </div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Theme.title', 'タイトル') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Theme.title', array('type' => 'text', 'size' => 20, 'maxlength' => 255, 'disabled' => $configDisabled)) ?>
				<?php echo $this->BcForm->error('Theme.title') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Theme.description', '説明') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Theme.description', array('type' => 'textarea', 'rows' => 5, 'cols' => 60, 'disabled' => $configDisabled)) ?>
				<?php echo $this->BcForm->error('Theme.description') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Theme.author', '制作者') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Theme.author', array('type' => 'text', 'size' => 20, 'maxlength' => 255, 'disabled' => $configDisabled)) ?>
				<?php echo $this->BcForm->error('Theme.author') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Theme.url', 'URL') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Theme.url', array('type' => 'text', 'size' => 60, 'maxlength' => 255, 'disabled' => $configDisabled)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpUrl', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('Theme.url') ?>
				<div id="helptextUrl" class="helptext">
					<ul>
						<li>制作者のWEBサイトのURL。</li>
						<li>半角英数字のみで入力してください。</li>
					</ul>
				</div>
			</td>
		</tr>
	</table>
</div>
<?php if (!$folderDisabled && $siteConfig['theme'] != $this->BcForm->value('Theme.name')): ?>
	<div class="submit">
		<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
		<?php $this->BcBaser->link('削除', array('action' => 'del', $this->BcForm->value('Theme.name')), array('class' => 'submit-token btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('Theme.name')), false); ?>
	</div>
	<?php endif; ?>

<?php echo $this->BcForm->end() ?>
