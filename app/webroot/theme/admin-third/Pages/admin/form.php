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
 * @var BcAppView $this
 */
$this->BcBaser->css('admin/ckeditor/editor', ['inline' => true]);
$this->BcBaser->js('admin/pages/edit', false);
?>


<div hidden="hidden">
	<div id="Action"><?php echo $this->request->action ?></div>
</div>

<?php echo $this->BcForm->create('Page') ?>
<?php echo $this->BcForm->input('Page.mode', ['type' => 'hidden']) ?>
<?php echo $this->BcForm->input('Page.id', ['type' => 'hidden']) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="bca-section bca-section-editor-area">
	<?php echo $this->BcForm->editor('Page.contents', array_merge([
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
	<section class="bca-section" data-bca-section-type="form-group">
		<div class="bca-collapse__action">
			<button type="button" class="bca-collapse__btn" data-bca-collapse="collapse"
					data-bca-target="#pageSettingBody" aria-expanded="false" aria-controls="pageSettingBody"><?php echo __d('baser', '詳細設定') ?>&nbsp;&nbsp;<i
					class="bca-icon--chevron-down bca-collapse__btn-icon"></i></button>
		</div>
		<div class="bca-collapse" id="pageSettingBody" data-bca-state="">
			<table class="form-table bca-form-table" data-bca-table-type="type2">
	<?php if($pageTemplateList): ?>
				<tr>
					<th class="bca-form-table__label"><?php echo $this->BcForm->label('Page.page_template', __d('baser', '固定ページテンプレート')) ?></th>
					<td class="col-input bca-form-table__input">
						<?php echo $this->BcForm->input('Page.page_template', ['type' => 'select', 'options' => $pageTemplateList]) ?>
						<div
							class="helptext"><?php echo __d('baser', 'テーマフォルダ内の、Pages/templates テンプレートを配置する事で、ここでテンプレートを選択できます。') ?></div>
						<?php echo $this->BcForm->error('Page.page_template') ?>
					</td>
				</tr>
	<?php endif ?>
				<tr>
					<th class="bca-form-table__label"><?php echo $this->BcForm->label('Page.code', __d('baser', 'コード')) ?></th>
					<td class="col-input bca-form-table__input">
						<?php echo $this->BcForm->input('Page.code', [
							'type' => 'textarea',
							'cols' => 36,
							'rows' => 5,
							'style' => 'font-size:14px;font-family:Verdana,Arial,sans-serif;'
						]); ?>
						<i class="bca-icon--question-circle btn help bca-help"></i>
						<div
							class="helptext"><?php echo __d('baser', '固定ページの本文には、ソースコードに切り替えてPHPやJavascriptのコードを埋め込む事ができますが、ユーザーが間違って削除してしまわないようにこちらに入力しておく事もできます。<br>入力したコードは、自動的にコンテンツ本体の上部に差し込みます。') ?></div>
						<?php echo $this->BcForm->error('Page.code') ?>
					</td>
				</tr>
				<?php echo $this->BcForm->dispatchAfterForm() ?>
			</table>
		</div>
	</section>
<?php else: ?>
	<?php echo $this->BcForm->input('Page.code', ['type' => 'hidden']) ?>
<?php endif ?>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<?php echo $this->BcForm->submit(__d('baser', '保存'), [
	'div' => false,
	'class' => 'button bca-btn',
	'data-bca-btn-type' => 'save',
	'data-bca-btn-size' => 'lg',
	'data-bca-btn-width' => 'lg',
	'id' => 'BtnSave'
]) ?>

<?php echo $this->BcForm->end(); ?>
