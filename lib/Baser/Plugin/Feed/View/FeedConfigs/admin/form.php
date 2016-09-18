<?php
/**
 * [ADMIN] フィード設定 フォーム
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<script type="text/javascript">
$(window).load(function() {
	$("#FeedConfigName").focus();
});
$(function(){
	$("#EditTemplate").click(function(){
		if(confirm('フィード設定を保存して、テンプレート '+$("#FeedConfigTemplate").val()+' の編集画面に移動します。よろしいですか？')){
			$("#FeedConfigEditTemplate").val(true);
			$("#FeedConfigAdminEditForm").submit();
		}
	});
});
</script>

<?php echo $this->BcForm->create('FeedConfig') ?>

<div class="section">

	<h2>基本項目</h2>	

	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('FeedConfig.id', 'NO') ?>&nbsp;<span class="required">*</span></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('FeedConfig.id') ?>
					<?php echo $this->BcForm->input('FeedConfig.id', array('type' => 'hidden')) ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedConfig.name', 'フィード設定名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedConfig.name', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('FeedConfig.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>日本語が利用できます。</li>
						<li>識別でき、わかりやすい設定名を入力します。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedConfig.display_number', '表示件数') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedConfig.display_number', array('type' => 'text', 'size' => 10, 'maxlength' => 3)) ?>件
				<?php echo $this->BcForm->error('FeedConfig.display_number') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<div class="section">
	<h2 class="btn-slide-form"><a href="javascript:void(0)" id="FormOption">オプション</a></h2>
	<table cellpadding="0" cellspacing="0" class="form-table slide-body" id="FormOptionBody">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedConfig.feed_title_index', 'フィードタイトルリスト') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedConfig.feed_title_index', array('type' => 'textarea', 'cols' => 36, 'rows' => 3)) ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', array('id' => 'helpFeedTitleIndex', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('FeedConfig.feed_title_index') ?>
				<div id="helptextFeedTitleIndex" class="helptext">
					<ul>
						<li>一つの表示フィードに対し、複数のフィードを読み込む際、フィードタイトルを表示させたい場合は、フィードタイトルを「|」で区切って入力してください。</li>
						<li>テンプレート上で、「feed_title」として参照できるようになります。</li>
						<li>また、先頭から順に「feed_title_no」としてインデックス番号が割り振られます。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedConfig.category_index', 'カテゴリリスト') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedConfig.category_index', array('type' => 'textarea', 'cols' => 36, 'rows' => 3)) ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', array('id' => 'helpCategoryIndex', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('FeedConfig.category_index') ?>
				<div id="helptextCategoryIndex" class="helptext">
					<ul>
						<li>カテゴリにインデックス番号を割り当てたい場合は、カテゴリ名を「|」で区切って入力してください。</li>
						<li>先頭から順に「category_no」としてインデックス番号が割り振られます。</li>
					</ul>
				</div>

			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedConfig.template', 'テンプレート名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedConfig.template', array('type' => 'select', 'options' => $this->Feed->getTemplates())) ?>
				<?php echo $this->BcForm->input('FeedConfig.edit_template', array('type' => 'hidden')) ?>
				<?php if ($this->action == 'admin_edit'): ?>
					<?php $this->BcBaser->link('≫ 編集する', 'javascript:void(0)', array('id' => 'EditTemplate')) ?>
				<?php endif ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', array('id' => 'helpTemplate', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('FeedConfig.template') ?>
				<div id="helptextTemplate" class="helptext">
					<ul>
						<li>出力するフィードのテンプレートを指定します。</li>
						<li>「編集する」からテンプレートの内容を編集する事ができます。</li>
					</ul>
				</div>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm('option') ?>
	</table>
</div>

<!-- button -->
<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
	<?php if ($this->action == 'admin_edit'): ?>
	<?php $this->BcBaser->link('削除', array('action' => 'delete', $this->BcForm->value('FeedConfig.id')), array('class' => 'submit-token button'), sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('FeedConfig.name')), false); ?>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>


<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none"><div id="flashMessage" class="notice-message"></div></div>
<div id="DataList"><?php $this->BcBaser->element('feed_details/index_list') ?></div>
