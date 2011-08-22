<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] 検索インデックス一覧
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$priorities = array('0.1' => '0.1', '0.2' => '0.2', '0.3' => '0.3', '0.4' => '0.4', '0.5' => '0.5',
					'0.6' => '0.6', '0.7' => '0.7', '0.8' => '0.8', '0.9' => '0.9', '1.0' => '1.0');
$categories = am(array('none' => 'カテゴリなし'), unserialize($baser->siteConfig['content_categories']));
$types = unserialize($baser->siteConfig['content_types']);
?>
<script type="text/javascript">
$(function(){
	if($("#ContentOpen").html()) {
		$("#ContentFilterBody").show();
	}
	$(".priority").change(function() {
		var id = this.id.replace('ContentPriority', '');
		var data = {
			'data[Content][id]':id,
			'data[Content][priority]':$(this).val()
		};
		$.ajax({
			type: "POST",
			url: $("#AjaxChangePriorityUrl").html()+'/'+id,
			data: data,
			beforeSend: function() {
				$("#flashMessage").slideUp();
				$("#PriorityAjaxLoader"+id).show();
			},
			success: function(result){
				if(!result) {
					$("#flashMessage").html('処理中にエラーが発生しました。');
					$("#flashMessage").slideDown();
				}
			},
			error: function() {
				$("#flashMessage").html('処理中にエラーが発生しました。');
				$("#flashMessage").slideDown();
			},
			complete: function() {
				$("#PriorityAjaxLoader"+id).hide();
			}
		});
	});
});
</script>

<div id="flashMessage" class="error-message" style="display:none"></div>
<div id="AjaxChangePriorityUrl" class="display-none"><?php echo $baser->url(array('action' => 'ajax_change_priority')) ?></div>
<div id="ContentOpen" class="display-none"><?php echo $formEx->value('Content.open') ?></div>

<h2><?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>

<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>baserCMSでは、サイト内検索の対象とするデータを「検索インデックス」として管理しており、
	Webページやプラグインコンテンツの追加、更新時に自動的に更新されるようになっています。<br />
	また、サイト内検索での検索結果の表示順は、優先度、更新日によって確定する事となっており、ここでは次の処理を行う事ができます。
	</p>
	<ul>
		<li>優先度の変更（0.1〜1.0）</li>
		<li>検索結果に表示されるコンテンツの削除</li>
		<li>baserCMSで管理できないコンテンツの検索インデックスへの登録</li>
	</ul>
</div>

<!-- search -->
<h3><a href="javascript:void(0);" class="slide-trigger" id="ContentFilter">検索</a></h3>
<div class="function-box corner10" id="ContentFilterBody" style="display:none">
	<?php echo $formEx->create('Content', array('url' => array('action' => 'index'))) ?>
	<?php echo $formEx->hidden('Content.open', array('value' => true)) ?>
	<p>
		<span><small>タイプ</small> <?php echo $formEx->input('Content.type', array('type' => 'select', 'options' => $types, 'empty' => '指定なし')) ?></span>
		<span><small>カテゴリ</small> <?php echo $formEx->input('Content.category', array('type' => 'select', 'options' => $categories, 'empty' => '指定なし')) ?></span>
		<span><small>キーワード</small> <?php echo $formEx->input('Content.keyword', array('type' => 'text', 'size' => '30')) ?></span>
		<span><small>公開状態</small>
		<?php echo $formEx->input('Content.status', array('type' => 'select', 'options' => $textEx->booleanMarkList(), 'empty' => '指定なし')) ?></span>　
		<span><small>優先度</small>
		<?php echo $formEx->input('Content.priority', array('type' => 'select', 'options' => $priorities, 'empty' => '指定なし')) ?></span>
	</p>
	<div class="align-center"><?php echo $formEx->submit('検　索', array('div' => false, 'class' => 'btn-orange button')) ?></div> 
	<?php $formEx->end() ?>
</div>

<!-- list-num -->
<?php $baser->element('list_num') ?>

<!-- pagination -->
<?php $baser->pagination('default', array(), null, false) ?>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01 sort-table" id="TableContents">
	<tr>
		<th>操作</th>
		<th>NO</th>
		<th>タイプ<br />カテゴリ</th>
		<th>タイトル</th>
		<th>コンテンツ内容</th>
		<th>公開状態</th>
		<th>登録日<br />更新日</th>
	</tr>
<?php if(!empty($datas)): ?>
	<?php $count=0; ?>
	<?php foreach($datas as $data): ?>
		<?php if (!$data['Content']['status']): ?>
			<?php $class=' class="disablerow"' ?>
		<?php elseif ($count%2 === 0): ?>
			<?php $class=' class="altrow"' ?>
		<?php else: ?>
			<?php $class='' ?>
		<?php endif; ?>
	<tr id="Row<?php echo $count+1 ?>" <?php echo $class; ?>>
		<td class="operation-button" style="width:22%">
			<?php $baser->img('ajax-loader-s.gif', array('id' => 'PriorityAjaxLoader'.$data['Content']['id'], 'style'=>"vertical-align:middle;display:none")) ?>
			<?php echo $formEx->input('Content.priority'.'_'.$data['Content']['id'], array(
					'type'		=> 'select', 
					'options'	=> $priorities, 
					'empty'		=> '優先度', 
					'class'		=> 'priority',
					'value'		=> $data['Content']['priority'])) ?>
			<?php $baser->link('削除', 
					array('action' => 'delete', $data['Content']['id']),
					array('class' => 'btn-gray-s button-s'),
					sprintf('%s を本当に削除してもいいですか？', $data['Content']['id']),
					false); ?>
		</td>
		<td><?php echo $data['Content']['id'] ?></td>
		<td style="width:15%"><?php echo $data['Content']['type'] ?><br /><?php echo $data['Content']['category'] ?></td>
		<td style="width:15%">
			<?php echo $baser->link($textEx->noValue($data['Content']['title'], '設定なし'), $data['Content']['url'], array('target' => '_blank')) ?></td>
		<td><?php echo $textEx->mbTruncate($data['Content']['detail'], 50) ?></td>
		<td style="width:10%;text-align:center">
			<?php echo $textEx->booleanMark($data['Content']['status']); ?><br />
		</td>
		<td style="width:10%;white-space: nowrap">
			<?php echo $timeEx->format('y-m-d',$data['Content']['created']) ?><br />
			<?php echo $timeEx->format('y-m-d',$data['Content']['modified']) ?>
		</td>
	</tr>
		<?php $count++; ?>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
	<?php endif; ?>
</table>

<!-- pagination -->
<?php $baser->pagination('default', array(), null, false) ?>

<div class="align-center">
	<?php $baser->link('新規登録', array('action' => 'add'), array('class' => 'btn-red button')) ?>
</div>