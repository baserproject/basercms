<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * コンテンツ一覧
 */
?>


<?php echo $this->BcForm->create('Content', ['url' => ['action' => 'index']]) ?>
<?php echo $this->BcForm->hidden('Content.open', ['value' => true]) ?>
<p>
	<span>
		<?php echo $this->BcForm->label('Content.folder_id', __d('baser', 'フォルダ')) ?>
		<?php echo $this->BcForm->input('Content.folder_id', ['type' => 'select', 'options' => $folders, 'empty' => __d('baser', '指定しない')]) ?>　
	</span>
	<span>
		<?php echo $this->BcForm->label('Content.name', __d('baser', 'コンテンツ名')) ?>
		<?php echo $this->BcForm->input('Content.name', ['type' => 'text', 'size' => 20]) ?>
	</span>
	<span>
		<?php echo $this->BcForm->label('Content.type', __d('baser', 'コンテンツタイプ')) ?>
		<?php echo $this->BcForm->input('Content.type', ['type' => 'select', 'options' => $contentTypes, 'empty' => __d('baser', '指定しない')]) ?>　
	</span>
	<span>
		<?php echo $this->BcForm->label('Content.self_status', __d('baser', '公開状態')) ?>
		<?php echo $this->BcForm->input('Content.self_status', ['type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => __d('baser', '指定しない')]) ?>　
	</span>
	<span>
		<?php echo $this->BcForm->label('Content.author_id', __d('baser', '作成者')) ?>
		<?php echo $this->BcForm->input('Content.author_id', ['type' => 'select', 'options' => $authors, 'empty' => __d('baser', '指定しない')]) ?>　
	</span>
	<?php echo $this->BcSearchBox->dispatchShowField() ?>
</p>
<div class="submit">
	<?php echo $this->BcForm->button(__d('baser', '検索'), ['class' => 'button', 'id' => 'BtnSearchSubmit']) ?>
	<?php echo $this->BcForm->button(__d('baser', 'クリア'), ['class' => 'button', 'id' => 'BtnSearchClear']) ?>
</div>
<?php echo $this->BcForm->end() ?>
