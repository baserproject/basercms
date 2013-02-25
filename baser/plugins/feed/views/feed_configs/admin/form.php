<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] フィード設定 フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.feed.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
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
			$("#FeedConfigEditForm").submit();
		}
	});
});
</script>

<?php echo $bcForm->create('FeedConfig') ?>

<div class="section">
	
	<h2>基本項目</h2>	

	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
	<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('FeedConfig.id', 'NO') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->value('FeedConfig.id') ?>
				<?php echo $bcForm->input('FeedConfig.id', array('type' => 'hidden')) ?>
			</td>
		</tr>
	<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('FeedConfig.name', 'フィード設定名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('FeedConfig.name', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('FeedConfig.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>日本語が利用できます。</li>
						<li>識別でき、わかりやすい設定名を入力します。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('FeedConfig.display_number', '表示件数') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('FeedConfig.display_number', array('type' => 'text', 'size' => 10, 'maxlength' => 3)) ?>件
				<?php echo $bcForm->error('FeedConfig.display_number') ?>
			</td>
		</tr>
	</table>
</div>

<div class="section">
	<h2 class="btn-slide-form"><a href="javascript:void(0)" id="FormOption">オプション</a></h2>
	<table cellpadding="0" cellspacing="0" class="form-table slide-body" id="FormOptionBody">
		<tr>
			<th class="col-head"><?php echo $bcForm->label('FeedConfig.feed_title_index', 'フィードタイトルリスト') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('FeedConfig.feed_title_index', array('type' => 'textarea', 'cols' => 36, 'rows' => 3)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpFeedTitleIndex', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('FeedConfig.feed_title_index') ?>
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
			<th class="col-head"><?php echo $bcForm->label('FeedConfig.category_index', 'カテゴリリスト') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('FeedConfig.category_index', array('type' => 'textarea', 'cols'=>36,'rows'=>3)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpCategoryIndex', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('FeedConfig.category_index') ?>
				<div id="helptextCategoryIndex" class="helptext">
					<ul>
						<li>カテゴリにインデックス番号を割り当てたい場合は、カテゴリ名を「|」で区切って入力してください。</li>
						<li>先頭から順に「category_no」としてインデックス番号が割り振られます。</li>
					</ul>
				</div>

			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('FeedConfig.template', 'テンプレート名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('FeedConfig.template', array('type' => 'select', 'options' => $feed->getTemplates())) ?>
				<?php echo $bcForm->input('FeedConfig.edit_template', array('type' => 'hidden')) ?>
	<?php if($this->action == 'admin_edit'): ?>
				<?php $bcBaser->link('≫ 編集する', 'javascript:void(0)', array('id' => 'EditTemplate')) ?>
	<?php endif ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpTemplate', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('FeedConfig.template') ?>
				<div id="helptextTemplate" class="helptext">
					<ul>
						<li>出力するフィードのテンプレートを指定します。</li>
						<li>「編集する」からテンプレートの内容を編集する事ができます。</li>
					</ul>
				</div>
			</td>
		</tr>
	</table>
</div>

<!-- button -->
<div class="submit">
	<?php echo $bcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php if($this->action == 'admin_edit'): ?>
	<?php $bcBaser->link('削除', 
			array('action' => 'delete', $bcForm->value('FeedConfig.id')),
			array('class' => 'button'),
			sprintf('%s を本当に削除してもいいですか？', $bcForm->value('FeedConfig.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $bcForm->end() ?>


<div id="AlertMessage" class="message" style="display:none"></div>
<div id="DataList"><?php $bcBaser->element('feed_details/index_list') ?></div>
