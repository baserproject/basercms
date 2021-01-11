<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ページ登録・編集フォーム
 *
 * @var BcAppView $this
 */
$this->BcBaser->css('admin/ckeditor/editor', ['inline' => true]);
$this->BcBaser->js('admin/pages/edit', false);
?>


<div class="display-none">
	<div id="Action"><?php echo $this->request->action ?></div>
</div>

<?php echo $this->BcForm->create('Page') ?>
<?php echo $this->BcForm->input('Page.mode', ['type' => 'hidden']) ?>
<?php echo $this->BcForm->input('Page.id', ['type' => 'hidden']) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="section editor-area">
	<?php echo $this->BcForm->input('Page.contents', array_merge([
		'type' => 'editor',
		'editor' => @$siteConfig['editor'],
		'editorUseDraft' => true,
		'editorDraftField' => 'draft',
		'editorWidth' => 'auto',
		'editorHeight' => '480px',
		'editorEnterBr' => @$siteConfig['editor_enter_br']
	], $editorOptions)); ?>
	<?php echo $this->BcForm->error('Page.contents') ?>
	<?php echo $this->BcForm->error('Page.draft') ?>
</div>

<?php if (BcUtil::isAdminUser()): ?>
	<div class="section">
		<table cellpadding="0" cellspacing="0" class="form-table">
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('Page.page_template', __d('baser', '固定ページテンプレート')) ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->input('Page.page_template', ['type' => 'select', 'options' => $pageTemplateList]) ?>
					<div class="helptext">
						<?php echo __d('baser', 'テーマフォルダ内の、Pages/templates テンプレートを配置する事で、ここでテンプレートを選択できます。') ?>
					</div>
					<?php echo $this->BcForm->error('Page.page_template') ?>
				</td>
			</tr>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('Page.code', __d('baser', 'コード')) ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->input('Page.code', [
						'type' => 'textarea',
						'cols' => 36,
						'rows' => 5,
						'style' => 'font-size:14px;font-family:Verdana,Arial,sans-serif;'
					]); ?>
					<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
					<div class="helptext">
						<?php echo __d('baser', '固定ページの本文には、ソースコードに切り替えてPHPやJavascriptのコードを埋め込む事ができますが、ユーザーが間違って削除してしまわないようにこちらに入力しておく事もできます。<br />入力したコードは、自動的にコンテンツ本体の上部に差し込みます。') ?>
					</div>
					<?php echo $this->BcForm->error('Page.code') ?>
				</td>
			</tr>
			<?php echo $this->BcForm->dispatchAfterForm() ?>
		</table>
	</div>
<?php else: ?>
	<?php echo $this->BcForm->input('Page.code', ['type' => 'hidden']) ?>
<?php endif ?>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
</div>

<?php echo $this->BcForm->end(); ?>
