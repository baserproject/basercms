<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] フィード詳細 フォーム
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

<!-- title -->
<h2><?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpAdmin', 'class' => 'slide-trigger', 'alt' => 'ヘルプ')) ?></h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>フィードの登録を行います。キャッシュ時間やカテゴリによる絞り込みの設定が行えます。</p>
	<ul>
		<li>他サイトのフィードも登録する事ができます。</li>
		<li>他サイトのフィードを読み込む場合は、他サイトのサーバー負荷軽減の為、画面下の「オプション」をクリックし、キャッシュ時間を多めに設定しておきましょう。（自サイトの場合でも負荷軽減を行う為には同様です）<br />
			<small>※ キャッシュとは、取得したデータを保持し、指定した時間の間、フィードのリクエストを抑制する仕組みです。指定した時間内は、新しい記事に更新されません</small> </li>
		<li>フィード内の特定のカテゴリの記事のみを取得するには、画面下の「オプション」をクリックし、カテゴリフィルターを登録します。</li>
	</ul>
</div>

<!-- form -->
<h3>基本項目</h3>
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php if($this->action == 'admin_add'): ?>
<?php echo $formEx->create('FeedDetail', array('url' => '/admin/feed/feed_details/add/' . $formEx->value('FeedDetail.feed_config_id'))) ?>
<?php elseif($this->action == 'admin_edit'): ?>
<?php echo $formEx->create('FeedDetail', array('url' => '/admin/feed/feed_details/edit/' . $formEx->value('FeedDetail.feed_config_id') . '/' . $formEx->value('FeedDetail.id'))) ?>
<?php endif; ?>
<?php echo $formEx->input('FeedDetail.feed_config_id', array('type' => 'hidden')) ?>

<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('FeedDetail.id', 'ID') ?></th>
		<td class="col-input">
			<?php echo $formEx->value('FeedDetail.id') ?>
			<?php echo $formEx->input('FeedDetail.id', array('type' => 'hidden')) ?>
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('FeedDetail.name', 'フィード詳細名') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('FeedDetail.name', array('type' => 'text', 'size'=>40,'maxlength'=>255)) ?>
			<?php echo $formEx->error('FeedDetail.name') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('FeedDetail.url', 'フィードURL') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('FeedDetail.url', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
			<?php echo $formEx->error('FeedDetail.url') ?>
		</td>
	</tr>
</table>
<h3><a href="javascript:void(0)" id="formOption" class="slide-trigger">オプション</a></h3>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01 slide-body" id="formOptionBody">
	<tr>
		<th class="col-head"><?php echo $formEx->label('FeedDetail.cache_time', 'キャッシュ時間') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('FeedDetail.cache_time', array(
				'type'		=> 'select',
				'options'	=> $formEx->getControlSource('cache_time'),
				'empty'		=> 'なし')) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpCacheTime', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('FeedDetail.cache_time') ?>
			<div id="helptextCacheTime" class="helptext"> 負荷を軽減させる為、フィード情報をキャッシュさせる時間を選択してください。</div>
		</td>
	<tr>
		<th class="col-head"><?php echo $formEx->label('FeedDetail.category_filter', 'カテゴリフィルター') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('FeedDetail.category_filter', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpCategoryFilter', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
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

<!-- button -->
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->submit('登　録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $formEx->submit('更　新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削除', 
			array('action' => 'delete', $formEx->value('FeedConfig.id'), $formEx->value('FeedDetail.id')),
			array('class' => 'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('FeedConfig.name')),false); ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>