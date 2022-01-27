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
 * コンテンツオプション
 * @var bool $editable コンテンツ編集不可かどうか
 * @var array $authors 作成者リスト
 * @var array $layoutTemplates レイアウトテンプレートリスト
 */
$editable = $this->BcContents->isEditable();
?>


<section id="ContentsOptionSetting" class="bca-section" data-bca-section-type="form-group">
	<div class="bca-collapse__action">
		<button type="button" class="bca-collapse__btn" data-bca-collapse="collapse"
				data-bca-target="#formContentsOptionBody" aria-expanded="false" aria-controls="formOptionBody"><?php echo __d('baser', 'オプション') ?>&nbsp;&nbsp;<i
				class="bca-icon--chevron-down bca-collapse__btn-icon"></i></button>
	</div>
	<div class="bca-collapse" id="formContentsOptionBody" data-bca-state="">
		<table class="form-table bca-form-table" data-bca-table-type="type2">
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Content.description', __d('baser', '説明文')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php if ($editable): ?>
						<?php echo $this->BcForm->input('Content.description', ['type' => 'textarea', 'cols' => 36, 'rows' => 4, 'data-input-text-size' => 'full-counter']) ?>
					<?php else: ?>
						<?php if ($this->BcForm->value('Content.exclude_search')): ?>
							<?php echo h($this->BcForm->value('Content.description')) ?>
						<?php else: ?>
							<?php echo h($this->BcBaser->siteConfig['description']) ?>
						<?php endif ?>
						<?php echo $this->BcForm->hidden('Content.description') ?>
					<?php endif ?>
					<?php echo $this->BcForm->error('Content.description') ?>
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Content.eyecatch', __d('baser', 'アイキャッチ')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php if ($editable): ?>
						<?php echo $this->BcForm->input('Content.eyecatch', ['type' => 'file', 'imgsize' => 'thumb']) ?>
					<?php else: ?>
						<?php echo $this->BcUpload->uploadImage('Content.eyecatch', $this->BcForm->value('Content.eyecatch'), ['imgsize' => 'thumb']) ?>
					<?php endif ?>
					<?php echo $this->BcForm->error('Content.eyecatch') ?>
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Content.author_id', __d('baser', '作成者')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php if ($editable): ?>
						<?php echo $this->BcForm->input('Content.author_id', ['type' => 'select', 'options' => $authors]) ?>　
						<small>[<?php echo __d('baser', '作成日') ?>
							]</small> <?php echo $this->BcForm->input('Content.created_date', ['type' => 'dateTimePicker', 'size' => 12, 'maxlength' => 10]) ?>　
						<small>[<?php echo __d('baser', '更新日') ?>
							]</small> <?php echo $this->BcForm->input('Content.modified_date', ['type' => 'dateTimePicker', 'size' => 12, 'maxlength' => 10]) ?>
					<?php else: ?>
						<?php echo h($this->BcText->arrayValue($this->BcForm->value('Content.author_id'), $authors)) ?>　

						<small>[<?php echo __d('baser', '作成日') ?>
							]</small> <?php echo $this->BcTime->format('Y/m/d H:i', $this->BcForm->value('Content.created_date')) ?>　
						<small>[<?php echo __d('baser', '更新日') ?>
							]</small> <?php echo $this->BcTime->format('Y/m/d H:i', $this->BcForm->value('Content.modified_date')) ?>
						<?php echo $this->BcForm->hidden('Content.author_id') ?>
						<?php echo $this->BcForm->hidden('Content.created_date') ?>
						<?php echo $this->BcForm->hidden('Content.modified_date') ?>
					<?php endif ?>
					<?php echo $this->BcForm->error('Content.author_id') ?>
					<?php echo $this->BcForm->error('Content.created_date') ?>
					<?php echo $this->BcForm->error('Content.modified_date') ?>
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Content.layout_template', __d('baser', 'レイアウトテンプレート')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('Content.layout_template', ['type' => 'select', 'options' => $layoutTemplates]) ?>
					　
					<?php echo $this->BcForm->error('Content.layout_template') ?>　
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Content.exclude_search', __d('baser', 'その他設定')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php if ($editable): ?>
						<span
							style="white-space: nowrap"><?php echo $this->BcForm->input('Content.exclude_search', ['type' => 'checkbox', 'label' => __d('baser', 'サイト内検索の検索結果より除外する')]) ?></span>　
						<span
							style="white-space: nowrap"><?php echo $this->BcForm->input('Content.exclude_menu', ['type' => 'checkbox', 'label' => __d('baser', '公開ページのメニューより除外する')]) ?></span>　
						<span
							style="white-space: nowrap"><?php echo $this->BcForm->input('Content.blank_link', ['type' => 'checkbox', 'label' => __d('baser', 'メニューのリンクを別ウィンドウ開く')]) ?></span>
					<?php else: ?>
						<?php if ($this->BcForm->value('Content.exclude_search')): ?>
							<span style="white-space: nowrap"><?php echo __d('baser', 'サイト内検索の検索結果より除外する') ?></span>　
						<?php else: ?>
							<span style="white-space: nowrap"><?php echo __d('baser', 'サイト内検索の検索結果より除外しない') ?></span>　
						<?php endif ?>
						<?php if ($this->BcForm->value('Content.exclude_menu')): ?>
							<span style="white-space: nowrap"><?php echo __d('baser', '公開ページのメニューより除外する') ?></span>　
						<?php else: ?>
							<span style="white-space: nowrap"><?php echo __d('baser', '公開ページのメニューより除外しない') ?></span>　
						<?php endif ?>
						<?php if ($this->BcForm->value('Content.blank_link')): ?>
							<span style="white-space: nowrap"><?php echo __d('baser', 'メニューのリンクを別ウィンドウ開く') ?></span>
						<?php else: ?>
							<span style="white-space: nowrap"><?php echo __d('baser', 'メニューのリンクを同じウィンドウに開く') ?></span>
						<?php endif ?>
						<?php echo $this->BcForm->hidden('Content.exclude_search') ?>
						<?php echo $this->BcForm->hidden('Content.exclude_menu') ?>
						<?php echo $this->BcForm->hidden('Content.blank_link') ?>
					<?php endif ?>
				</td>
			</tr>
		</table>
	</div>
</section>
