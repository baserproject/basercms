<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Feed.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] フィード設定 フォーム
 *
 * @var BcAppView $this
 */
$this->BcBaser->i18nScript([
	'confirmMessage1' => __d('baser', 'フィード設定を保存して、テンプレート %s の編集画面に移動します。よろしいですか？')
]);
$this->BcBaser->js('Feed.admin/feed_configs/form', false);
?>


<?php echo $this->BcForm->create('FeedConfig') ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="section">

	<h2><?php echo __d('baser', '基本項目') ?></h2>

	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('FeedConfig.id', 'NO') ?>&nbsp;<span
						class="required">*</span></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('FeedConfig.id') ?>
					<?php echo $this->BcForm->input('FeedConfig.id', ['type' => 'hidden']) ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedConfig.name', __d('baser', 'フィード設定名')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedConfig.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true]) ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpName', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('FeedConfig.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li><?php echo __d('baser', '日本語が利用できます。') ?></li>
						<li><?php echo __d('baser', '識別でき、わかりやすい設定名を入力します。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedConfig.display_number', __d('baser', '表示件数')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedConfig.display_number', ['type' => 'text', 'size' => 10, 'maxlength' => 3]) ?><?php echo __d('baser', '件') ?>
				<?php echo $this->BcForm->error('FeedConfig.display_number') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<div class="section">
	<h2 class="btn-slide-form"><a href="javascript:void(0)" id="FormOption"><?php echo __d('baser', 'オプション') ?></a></h2>
	<table cellpadding="0" cellspacing="0" class="form-table slide-body" id="FormOptionBody">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedConfig.feed_title_index', __d('baser', 'フィードタイトルリスト')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedConfig.feed_title_index', ['type' => 'textarea', 'cols' => 36, 'rows' => 3]) ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpFeedTitleIndex', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('FeedConfig.feed_title_index') ?>
				<div id="helptextFeedTitleIndex" class="helptext">
					<ul>
						<li><?php echo __d('baser', '一つの表示フィードに対し、複数のフィードを読み込む際、フィードタイトルを表示させたい場合は、フィードタイトルを「|」で区切って入力してください。') ?></li>
						<li><?php echo __d('baser', 'テンプレート上で、「feed_title」として参照できるようになります。') ?></li>
						<li><?php echo __d('baser', 'また、先頭から順に「feed_title_no」としてインデックス番号が割り振られます。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedConfig.category_index', __d('baser', 'カテゴリリスト')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedConfig.category_index', ['type' => 'textarea', 'cols' => 36, 'rows' => 3]) ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpCategoryIndex', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('FeedConfig.category_index') ?>
				<div id="helptextCategoryIndex" class="helptext">
					<ul>
						<li><?php echo __d('baser', 'カテゴリにインデックス番号を割り当てたい場合は、カテゴリ名を「|」で区切って入力してください。') ?></li>
						<li><?php echo __d('baser', '先頭から順に「category_no」としてインデックス番号が割り振られます。') ?></li>
					</ul>
				</div>

			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedConfig.template', __d('baser', 'テンプレート名')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedConfig.template', ['type' => 'select', 'options' => $this->Feed->getTemplates()]) ?>
				<?php echo $this->BcForm->input('FeedConfig.edit_template', ['type' => 'hidden']) ?>
				<?php if ($this->action == 'admin_edit'): ?>
					&nbsp;<?php $this->BcBaser->link(__d('baser', '編集する'), 'javascript:void(0)', ['id' => 'EditTemplate', 'class' => 'button-small']) ?>&nbsp;
				<?php endif ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpTemplate', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('FeedConfig.template') ?>
				<div id="helptextTemplate" class="helptext">
					<ul>
						<li><?php echo __d('baser', '出力するフィードのテンプレートを指定します。') ?></li>
						<li><?php echo __d('baser', '「編集する」からテンプレートの内容を編集する事ができます。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm('option') ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<!-- button -->
<div class="submit">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
	<?php if ($this->action == 'admin_edit'): ?>
		<?php $this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $this->BcForm->value('FeedConfig.id')], ['class' => 'submit-token button'], sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('FeedConfig.name')), false); ?>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>


<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
	<div id="flashMessage" class="notice-message"></div>
</div>
<div id="DataList"><?php $this->BcBaser->element('feed_details/index_list') ?></div>
