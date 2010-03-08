<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] フィード詳細 フォーム
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.feed.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<h3>基本項目</h3>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php if($this->action == 'admin_add'): ?>
<?php echo $form->create('FeedDetail',array('url'=>"/admin/feed/feed_details/add/".$form->value('FeedDetail.feed_config_id'))) ?>
<?php elseif($this->action == 'admin_edit'): ?>
<?php echo $form->create('FeedDetail',array('url'=>"/admin/feed/feed_details/edit/".$form->value('FeedDetail.feed_config_id')."/".$form->value('FeedDetail.id'))) ?>
<?php endif; ?>

<?php echo $form->hidden('FeedDetail.feed_config_id') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_view' || $this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('FeedDetail.id', 'ID') ?></th>
		<td class="col-input">
			<?php echo $freeze->text('FeedDetail.id', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('FeedDetail.name', 'フィード詳細名') ?></th>
		<td class="col-input"><?php echo $freeze->text('FeedDetail.name', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $form->error('FeedDetail.name', '>> フィード詳細名を入力して下さい') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('FeedDetail.url', 'フィードURL') ?></th>
		<td class="col-input"><?php echo $freeze->text('FeedDetail.url', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $form->error('FeedDetail.url', '>> フィードURLの形式が不正です') ?>&nbsp;
		</td>
	</tr>
</table>


<h3><a href="javascript:void(0)" id="formOption" class="slide-trigger">オプション</a></h3>


<table cellpadding="0" cellspacing="0" class="admin-row-table-01 slide-body" id="formOptionBody">
	<tr>
		<th class="col-head"><?php echo $form->label('FeedDetail.cache_time', 'キャッシュ時間') ?></th>
		<td class="col-input">
            <?php echo $freeze->select('FeedDetail.cache_time', $formEx->getControlSource('cache_time')) ?>
            <?php echo $form->error('FeedDetail.cache_time', '>> キャッシュ時間を入力して下さい。') ?>
            <?php echo $html->image('help.png',array('id'=>'helpCacheTime','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextCacheTime" class="helptext">
                負荷を軽減させる為、フィード情報をキャッシュさせる時間を入力して下さい。<br />
            	(例) +30 minutes　※ 単数形、複数形に注意
            </div>
            &nbsp;
		</td>
	<tr>
		<th class="col-head"><?php echo $form->label('FeedDetail.category_filter', 'カテゴリフィルター') ?></th>
		<td class="col-input"><?php echo $freeze->text('FeedDetail.category_filter', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $form->error('FeedDetail.category_filter', '>> カテゴリフィルターの形式が不正です') ?>
            <?php echo $html->image('help.png',array('id'=>'helpCategoryFilter','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextCategoryFilter" class="helptext">
                特定のカテゴリのみ絞込みたい場合は、カテゴリ名を入力して下さい。
            </div>
            &nbsp;
		</td>
	</tr>
	</tr>
</table>
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $form->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $form->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
<?php endif ?>
</div>

