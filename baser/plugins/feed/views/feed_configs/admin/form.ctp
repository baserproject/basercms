<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] フィード設定 フォーム
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
<?php if($this->action == 'admin_edit'): ?>
<p style="text-align:right"><a href="#headHowTo">フィードの読み込み方法 &gt;&gt;</a></p>
<?php endif ?>

<h3>基本項目</h3>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $form->create('FeedConfig') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_view' || $this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('FeedConfig.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $freeze->text('FeedConfig.id', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('FeedConfig.name', 'フィード設定名') ?></th>
		<td class="col-input"><?php echo $freeze->text('FeedConfig.name', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $form->error('FeedConfig.name', '>> フィード設定名を入力して下さい') ?>
            &nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('FeedConfig.display_number', '表示件数') ?></th>
		<td class="col-input">
            <?php echo $freeze->text('FeedConfig.display_number', array('size'=>10,'maxlength'=>3)) ?>件
            <?php echo $form->error('FeedConfig.display_number') ?>&nbsp;
        </td>
	</tr>
</table>


<h3><a href="javascript:void(0)" id="formOption" class="slide-trigger">オプション</a></h3>


<table cellpadding="0" cellspacing="0" class="admin-row-table-01 slide-body" id="formOptionBody">
	<tr>
		<th class="col-head"><?php echo $form->label('FeedConfig.feed_title_index', 'フィードタイトルリスト') ?></th>
		<td class="col-input"><?php echo $freeze->textarea('FeedConfig.feed_title_index', array('cols'=>36,'rows'=>3)) ?>
            <?php echo $form->error('FeedConfig.feed_title_index', '>> フィードタイトルリストの形式が不正です') ?>
            <?php echo $html->image('help.png',array('id'=>'helpFeedTitleIndex','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextFeedTitleIndex" class="helptext">
                <ul>
                    <li>一つの表示フィードに対し、複数のフィードを読み込む際、フィードタイトルを表示させたい場合は、フィードタイトルを「|」で区切って入力して下さい。</li>
                    <li>テンプレート上で、「feed_title」として参照できるようになります。</li>
                    <li>また、先頭から順に「feed_title_no」としてインデックス番号が割り振られます。</li>
                </ul>
            </div>
            &nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('FeedConfig.category_index', 'カテゴリリスト') ?></th>
		<td class="col-input">
            <?php echo $freeze->textarea('FeedConfig.category_index', array('cols'=>36,'rows'=>3)) ?>
            <?php echo $form->error('FeedConfig.category_index', '>> カテゴリリストの形式が不正です') ?>
            <?php echo $html->image('help.png',array('id'=>'helpCategoryIndex','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextCategoryIndex" class="helptext">
            <ul>
                <li>カテゴリにインデックス番号を割り当てたい場合は、カテゴリ名を「|」で区切って入力して下さい。</li>
                <li>先頭から順に「category_no」としてインデックス番号が割り振られます。</li>
            </ul>
            </div>
            &nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('FeedConfig.template', 'テンプレート') ?></th>
		<td class="col-input">
            <?php echo $freeze->text('FeedConfig.template', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $form->error('FeedConfig.template', '>> テンプレートを入力して下さい') ?>
            <?php echo $html->image('help.png',array('id'=>'helpTemplate','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextTemplate" class="helptext">
                テンプレートを変更する場合は、/app/plugins/feed/views/feed/ 内にテンプレートを設置します。
            </div>
            &nbsp;
        </td>
	</tr>
</table>
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $form->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $form->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
<?php else: ?>
	<?php echo $html->link('編集する',array('action'=>'edit',$form->value('FeedConfig.id')),array('class'=>'btn-orange button'),null,false) ?>　
	<?php echo $html->link('削除する',array('action'=>'delete', $form->value('FeedConfig.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $form->value('FeedConfig.name')),false); ?>
<?php endif ?>
</div>

<?php if($this->action != 'admin_add'): ?>

<h2 id="headFeedDetail">読込フィード一覧</h2>

<small>複数のフィードを登録する事ができます</small>


<table cellpadding="0" cellspacing="0" class="admin-col-table-01">
  <tr>
  	<th scope="col">操作</th>
    <th scope="col">フィード名</th>
    <th scope="col">キャッシュ時間</th>
    <th scope="col">カテゴリフィルター</th>
    <th scope="col">登録日</th>
    <th scope="col">更新日</th>
  </tr>
<?php if(!empty($feedConfig['FeedDetail'])): ?>
<?php foreach($feedConfig['FeedDetail'] as $feedDetail): ?>
  <tr>
	<td class="operation-button">
        <?php echo $html->link('確認',$feedDetail['url'],array('target'=>'_blank','class'=>'btn-green-s button-s')) ?>
        <?php echo $html->link('編集',array('controller'=>'feed_details','action'=>'edit', $form->value('FeedConfig.id'),$feedDetail['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
		<?php echo $html->link('削除', array('controller'=>'feed_details','action'=>'delete', $form->value('FeedConfig.id'),$feedDetail['id']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？', $feedDetail['name']),false); ?>
	</td>
    <td>
	<?php if($feedDetail['url']): ?>
	<?php echo $html->link($feedDetail['name'],array('controller'=>'feed_details','action'=>'edit', $form->value('FeedConfig.id'),$feedDetail['id'])) ?>
	<?php else: ?>
	<?php echo $feedDetail['name'] ?>
	<?php endif; ?>
	</td>
    <td><?php echo $feedDetail['category_filter'] ?></td>
    <td><?php echo $textEx->listValue('FeedDetail.cache_time',$feedDetail['cache_time']) ?></td>
	<td><?php echo $timeEx->format('y-m-d',$feedDetail['created']) ?></td>
	<td><?php echo $timeEx->format('y-m-d',$feedDetail['modified']) ?></td>
  </tr>
<?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="6"><p class="no-data">データが見つかりませんでした。「追加する」ボタンをクリックしてフィード詳細を登録して下さい。</p></td></tr>
<?php endif; ?>
</table>


<div class="submit">
	<?php echo $html->link('追加する',array('controller'=>'feed_details','action'=>'add', $form->value('FeedConfig.id')),array('class'=>'btn-red button'),null,false) ?>　
</div>

<?php if($this->action == 'admin_edit'): ?>
<h2 id="headHowTo">フィードの読み込み方法</h2>
<p>以下のjavascriptを読み込みたい場所に貼り付けて下さい。</p>
<textarea cols="80" rows="1" onclick="this.select(0,this.value.length)" readonly="readonly">
<?php echo $javascript->link('/feed/ajax/'.$this->data['FeedConfig']['id']) ?>
</textarea>
<p>また、フィードの読み込みにはjQueryが必要です。以下のスクリプトの前に読み込んでおく必要があります。</p>
<p>jQuery（例）</p>
<textarea cols="80" rows="1" onclick="this.select(0,this.value.length)" readonly="readonly">
<?php echo $javascript->link('jquery-1.3.2.min') ?>
</textarea>

<?php endif ?>

<?php endif ?>