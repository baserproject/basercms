<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] フィード詳細 フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.feed.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<div class="section">
	<h2>基本項目</h2>
	<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->create('FeedDetail', array('url' => array('controller' => 'feed_details', 'action' => 'add', $formEx->value('FeedDetail.feed_config_id')))) ?>
	<?php elseif($this->action == 'admin_edit'): ?>
	<?php echo $formEx->create('FeedDetail', array('url' => array('controller' => 'feed_details', 'action' => 'edit', $formEx->value('FeedDetail.feed_config_id'), $formEx->value('FeedDetail.id'), 'id' => false))) ?>
	<?php endif; ?>
	<?php echo $formEx->input('FeedDetail.feed_config_id', array('type' => 'hidden')) ?>

	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
	<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('FeedDetail.id', 'ID') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->value('FeedDetail.id') ?>
				<?php echo $formEx->input('FeedDetail.id', array('type' => 'hidden')) ?>
			</td>
		</tr>
	<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('FeedDetail.name', 'フィード詳細名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('FeedDetail.name', array('type' => 'text', 'size'=>40,'maxlength'=>255)) ?>
				<?php echo $formEx->error('FeedDetail.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('FeedDetail.url', 'フィードURL') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('FeedDetail.url', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $formEx->error('FeedDetail.url') ?>
			</td>
		</tr>
	</table>
</div>

<div class="section">
	<h2 class="btn-slide-form"><a href="javascript:void(0)" id="FormOption">オプション</a></h2>
	<table cellpadding="0" cellspacing="0" class="form-table slide-body" id="FormOptionBody">
		<tr>
			<th class="col-head"><?php echo $formEx->label('FeedDetail.cache_time', 'キャッシュ時間') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('FeedDetail.cache_time', array(
					'type'		=> 'select',
					'options'	=> $formEx->getControlSource('cache_time'),
					'empty'		=> 'なし')) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpCacheTime', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('FeedDetail.cache_time') ?>
				<div id="helptextCacheTime" class="helptext"> 負荷を軽減させる為、フィード情報をキャッシュさせる時間を選択してください。</div>
			</td>
		<tr>
			<th class="col-head"><?php echo $formEx->label('FeedDetail.category_filter', 'カテゴリフィルター') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('FeedDetail.category_filter', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpCategoryFilter', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('FeedDetail.category_filter') ?>
				<div id="helptextCategoryFilter" class="helptext">
					<ul>
						<li>特定のカテゴリのみ絞込みたい場合は、カテゴリ名を入力してください。</li>
						<li>複数のカテゴリを指定する場合は、カテゴリ名を|（半角縦棒）で区切ります。</li>
					</ul>
				</div>
			</td>
		</tr>
	</table>
</div>

<!-- button -->
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->submit('登録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $formEx->submit('更新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削除', 
			array('action' => 'delete', $formEx->value('FeedConfig.id'), $formEx->value('FeedDetail.id')),
			array('class' => 'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('FeedConfig.name')),false); ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>