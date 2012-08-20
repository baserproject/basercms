<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ウィジェットエリア編集
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<div id="DelWidgetUrl" style="display:none"><?php $bcBaser->url(array('controller' => 'widget_areas', 'action' => 'del_widget', $bcForm->value('WidgetArea.id'))) ?></div>
<div id="CurrentAction" style="display:none"><?php echo $this->action ?></div>

<script type="text/javascript">
$(window).load(function() {
	$("#WidgetAreaName").focus();
});
$(function() {

	var sortableOptions = {
		scroll: true,
		items: 'div.sortable',
		opacity: 0.80,
		zIndex: 55,
		containment: 'body',
		tolerance: 'intersect',
		distance: 5,
		cursor: 'move',
		placeholder: 'ui-widget-content placeholder',
		deactivate: function(event,ui){
			// 新しいウィジェットを追加しようとしてやめた場合
			// 再度追加する際に原因不明のエラーが連続で出現してしまうので、
			// 一旦リセットするようにした。
			$("#Target").sortable("destroy");
			$("#Target").sortable(sortableOptions
			).droppable(
			{
				hoverClass: 'topDrop',
				accept: 'div.sortable',
				tolderance: 'intersect'
			});
		},
		update: function(event, ui){
			// jQueryUI 1.8.14 より、 ui.item.attr("id")で id が取得できない
			if($(ui.item.context).attr("id").match(/^Setting/i)){
				widgetAreaUpdateSortedIds();
				return;
			}

			var baseId = 0;
			$("#Target .setting").each(function () {
				var _baseId = parseInt($(this).attr('id').replace('Setting',''));
				if(_baseId > baseId){
					baseId = _baseId;
				}
			});

			baseId++;
			var id = $(ui.item.context).attr("id").replace('Widget','');
			var sourceId = id.replace('Widget','');
			var settingId = 'Setting' + (baseId);
			var tmpId = 'Tmp'+(baseId);

			/* ターゲットにテンプレートを追加 */
			ui.item.attr('id',tmpId);
			$("#"+tmpId).after($("#"+sourceId).clone().attr('id',settingId));
			$("#"+tmpId).remove();
			$("#"+settingId).addClass('setting');
			$("#"+settingId).removeClass('template');

			/* フィールドIDのリネーム */
			renameWidget(baseId);

			/* 値の設定 */
			var widgetname = $("#"+settingId+' .widget-name').html();
			$("#"+settingId+' .head').html($("#"+settingId+' .head').html()+$("#Target ."+widgetname).length);
			$("#WidgetId"+baseId).val(baseId);
			$("#WidgetName"+baseId).val($("#"+settingId+' .head').html());

			/* イベント登録 */
			registWidgetEvent(baseId);

			/* sortable をリフレッシュ */
			$("#Target").sortable("refresh");

			$("#"+settingId+" .content").slideDown('fast');

			/* ウィジェットを保存 */
			updateWidget(baseId);

		},
		activate: function(event, ui) {
			// ドラッグ時の幅を元の幅に合わせる
			$("#Source div:last").width(ui.item.width()-20);
		}
	};
	$("#Target").sortable(sortableOptions).droppable(
	{
		hoverClass: 'topDrop',
		accept: 'div.draggable',
		tolderance: 'intersect'
	});
	$("div.draggable").draggable(
	{
		scroll: true,
		helper: 'clone',
		opacity: 0.80,
		revert: 'invalid',
		cursor: 'move',
		connectToSortable: '#Target',
		containment: 'body'
	});

	$("#Target .sortable").each(function(k,v){
		registWidgetEvent($(this).attr('id').replace('Setting',''));
	});

	if($("#CurrentAction").html() == 'admin_edit') {
		$("#WidgetAreaUpdateTitleSubmit").click(function(){
			widgetAreaUpdateTitle();
			return false;
		});
	}

});
/**
 * ウィジェットごとにid/nameをリネームする
 */
function renameWidget(baseId){

	var settingId = 'Setting'+baseId;
	$("#"+settingId+' .form').attr('id','WidgetUpdateWidgetForm'+baseId);
	$("#WidgetUpdateWidgetForm"+baseId).children().each(function(){
		if($(this).attr('id')){
			$(this).attr('id',$(this).attr('id')+baseId);
		}
		if($(this).attr('name')!=undefined){
			if($(this).attr('name').match(/data\[Widget\]/i)){
				$(this).attr('name',$(this).attr('name').replace('data[Widget]','data[Widget'+baseId+']'));
			}
		}
	});
	$("#"+settingId+" label[for=WidgetStatus]").attr('for','WidgetStatus'+baseId);

}
/**
 * ウィジェットイベントを登録
 */
function registWidgetEvent(baseId){

	var settingId = 'Setting'+baseId;
	$("#WidgetUpdateWidgetSubmit"+baseId).click(function (){
		updateWidget(baseId);
		return false;
	});
	$("#"+settingId+" .action").click(function(){
		if($("#"+settingId+" .content").is(":hidden")){
			$("#"+settingId+" .content").slideDown('fast');
		}else{
			$("#"+settingId+" .content").slideUp('fast');
		}
	});
	$("#"+settingId+" .status").click(function(){
		if($("#"+settingId+" .status").attr('checked')){
			$("#"+settingId).addClass('enabled');
		}else{
			$("#"+settingId).removeClass('enabled');
		}
	});
	$("#"+settingId+" .del").click(function(){
		if(!confirm('設定内容も削除されますが、本当に削除してもいいですか？\n')){
			return;
		}
		delWidget(baseId);
	});

}
/**
 * ウィジェットを削除
 */
function delWidget(id){

	$.ajax({
		url: $("#DelWidgetUrl").html()+'/'+id,
		type: 'GET',
		dataType: 'text',
		beforeSend: function() {
			$("#WidgetAreaUpdateSortLoader").show();
			$("#flashMessage").slideUp();
		},
		success: function(result){
			if(result != '1'){
				$("#flashMessage").html('ウィジェッの削除に失敗しました。');
				$("#flashMessage").slideDown();
			} else {
				$("#Setting"+id+"").slideUp(200, function(){
					$("#Setting"+id).remove();
					widgetAreaUpdateSortedIds();
				});
			}
		},
		error: function(){
			$("#flashMessage").html('ウィジェットの削除に失敗しました。');
			$("#flashMessage").slideDown();
		},
		complete: function(xhr, textStatus) {
			$("#WidgetAreaUpdateSortLoader").hide();
		}

	});

}
/**
 * 並び順を更新する
 */
function widgetAreaUpdateSortedIds(){

	var ids = [];
	$("#Target .sortable").each(function(k,v){
		ids.push($(this).attr('id').replace('Setting',''));
	});
	$("#WidgetAreaSortedIds").val(ids.join(','));
	$.ajax({
		url: $("#WidgetAreaUpdateSortForm").attr('action'),
		type: 'POST',
		data: $("#WidgetAreaUpdateSortForm").serialize(),
		dataType: 'text',
		beforeSend: function() {
			$("#flashMessage").slideUp();
			$("#WidgetAreaUpdateSortLoader").show();
		},
		success: function(result){
			if(result != '1'){
				$("#flashMessage").html('ウィジェットエリアの並び替えの保存に失敗しました。');
				$("#flashMessage").slideDown();
			}
		},
		error: function(){
			$("#flashMessage").html('ウィジェットエリアの並び替えの保存に失敗しました。');
			$("#flashMessage").slideDown();
		},
		complete: function(xhr, textStatus) {
			$("#WidgetAreaUpdateSortLoader").hide();
		}
	});

}
/**
 * タイトルを更新する
 */
function widgetAreaUpdateTitle(){

	$.ajax({
		url: $("#WidgetAreaUpdateTitleForm").attr('action'),
		type: 'POST',
		data: $("#WidgetAreaUpdateTitleForm").serialize(),
		dataType: 'text',
		beforeSend: function() {
			$("#WidgetAreaUpdateTitleSubmit").attr('disabled', 'disabled');
			$("#flashMessage").slideUp();
			$("#WidgetAreaUpdateTitleLoader").show();
		},
		success: function(result){
			if(result){
				$("#flashMessage").html('ウィジェットエリア名を保存しました。');
			}else{
				$("#flashMessage").html('ウィジェットエリア名の保存に失敗しました。');
			}
			$("#flashMessage").slideDown();
		},
		error: function(){
			$("#flashMessage").html('ウィジェットエリア名の保存に失敗しました。');
			$("#flashMessage").slideDown();
		},
		complete: function(xhr, textStatus) {
			$("#WidgetAreaUpdateTitleSubmit").removeAttr('disabled');
			$("#WidgetAreaUpdateTitleLoader").hide();
		}
	});

}
/**
 * ウィジェットを更新する
 */
function updateWidget(id) {

	$.ajax({
		url: $("#WidgetUpdateWidgetForm"+id).attr('action'),
		type: 'POST',
		data: $("#WidgetUpdateWidgetForm"+id).serialize(),
		dataType: 'text',
		beforeSend: function() {
			$("#WidgetUpdateWidgetSubmit"+id).attr('disabled', 'disabled');
			$("#WidgetUpdateWidgetLoader"+id).show();
			$("#flashMessage").slideUp();
		},
		success: function(result){
			if(result != '1'){
				$("#flashMessage").html('ウィジェッの保存に失敗しました。');
				$("#flashMessage").slideDown();
			}else{
				$("#Setting"+id+' .head').html($("#Setting"+id+' .name').val());
			}
		},
		error: function(){
			$("#flashMessage").html('ウィジェットの保存に失敗しました。');
			$("#flashMessage").slideDown();
		},
		complete: function(xhr, textStatus) {
			$("#WidgetUpdateWidgetSubmit"+id).removeAttr('disabled');
			$("#WidgetUpdateWidgetLoader"+id).hide();
			widgetAreaUpdateSortedIds();
		}

	});

}
</script>
<?php if($this->action == 'admin_add'): ?>
	<?php echo $bcForm->create('WidgetArea', array('url' => array('action' => 'add'))) ?>
<?php elseif($this->action == 'admin_edit'): ?>
	<?php echo $bcForm->create('WidgetArea', array('action' => 'update_title', 'url' => array('action' => 'update_title', 'id' => false))) ?>
<?php endif ?>

<?php echo $bcForm->hidden('WidgetArea.id') ?>

<?php echo $bcForm->label('WidgetArea.name', 'ウィジェットエリア名') ?>&nbsp;
<?php echo $bcForm->input('WidgetArea.name', array('type' => 'text', 'size' => 40)) ?>&nbsp;
<span class="submit"><?php echo $bcForm->end(array('label' => 'エリア名を保存する', 'div' => false, 'class' => 'button btn-red', 'id' => 'WidgetAreaUpdateTitleSubmit')) ?></span>
<?php $bcBaser->img('ajax-loader-s.gif', array('style' => 'vertical-align:middle;display:none', 'id' => 'WidgetAreaUpdateTitleLoader')) ?>
<?php echo $bcForm->error('WidgetArea.name') ?>

<?php if(!empty($widgetInfos)): ?>

	<?php echo $bcForm->create('WidgetArea', array('action' => 'update_sort', 'url' => array('action' => 'update_sort', $bcForm->value('WidgetArea.id'), 'id' => false))) ?>
	<?php echo $bcForm->input('WidgetArea.sorted_ids', array('type' => 'hidden')) ?>
	<?php echo $bcForm->end() ?>

<div id="WidgetSetting" class="clearfix" >

	<!-- 利用できるウィジェット -->
	<div id="SourceOuter">
		<div id="Source">

			<h2>利用できるウィジェット</h2>
		<?php foreach($widgetInfos as $widgetInfo) : ?>
			<h3><?php echo $widgetInfo['title'] ?></h3>
<?php
		$widgets = array();
		foreach($widgetInfo['paths'] as $path){
			$Folder = new Folder($path);
			$files = $Folder->read(true,true,true);
			$widgets = array();
			foreach($files[1] as $file) {
				$widget = array('name'=>'','title'=>'','description'=>'','setting'=>'');
				ob_start();
				$key='Widget';
				// タイトルや説明文を取得する為、elementを使わず、includeする。
				// コントローラーでインクルードした場合、コントローラー内でヘルパ等が読み込まれていないのが原因で
				// エラーとなるのでここで読み込む
				include $file;
				$widget['name'] = basename($file, $this->ext);
				$widget['title'] = $title;
				$widget['description'] = $description;
				$widget['setting'] = ob_get_contents();
				$widgets[] = $widget;
				ob_end_clean();
			}
		}
?>
			<?php foreach($widgets as $widget): ?>

			<div class="ui-widget-content draggable widget" id="Widget<?php echo Inflector::camelize($widget['name']) ?>">
				<div class="head"><?php echo $widget['title'] ?></div>
			</div>

			<div class="description"><?php echo $widget['description'] ?></div>

			<div class="ui-widget-content sortable widget template <?php echo $widget['name'] ?>" id="<?php echo Inflector::camelize($widget['name']) ?>">
				<div class="clearfix">
					<div class="widget-name display-none"><?php echo $widget['name'] ?></div>
					<div class="del">削除</div>
					<div class="action">設定</div>
					<div class="head"><?php echo $widget['title'] ?></div>
				</div>
				<div class="content" style="text-align:right">
					<p class="widget-name"><small><?php echo $widget['title'] ?></small></p>
					<?php echo $bcForm->create('Widget', array('url' => array('controller' => 'widget_areas', 'action'=> 'update_widget', $bcForm->value('WidgetArea.id')), 'class' => 'form')) ?>
					<?php echo $bcForm->input('Widget.id', array('type' => 'hidden', 'class'=>'id')) ?>
					<?php echo $bcForm->input('Widget.type', array('type' => 'hidden', 'value' => $widget['title'])) ?>
					<?php echo $bcForm->input('Widget.element', array('type' => 'hidden', 'value' => $widget['name'])) ?>
					<?php echo $bcForm->input('Widget.plugin',array('type' => 'hidden', 'value' => $widgetInfo['plugin'])) ?>
					<?php echo $bcForm->input('Widget.sort', array('type' => 'hidden')) ?>
					<?php echo $bcForm->label('Widget.name','タイトル') ?>&nbsp;
					<?php echo $bcForm->input('Widget.name', array('type' => 'text', 'class' => 'name')) ?><br />
					<?php echo $widget['setting'] ?><br />
					<?php $bcBaser->img('ajax-loader-s.gif', array('style' => 'vertical-align:middle;display:none', 'id' => 'WidgetUpdateWidgetLoader', 'class' => 'loader')) ?>
					<?php echo $bcForm->input('Widget.use_title', array('type' => 'checkbox', 'label' => 'タイトルを表示', 'class' => 'use_title', 'checked' => 'checked')) ?>
					<?php echo $bcForm->input('Widget.status', array('type' => 'checkbox', 'label' => '利用する', 'class' => 'status')) ?>
					<?php echo $bcForm->end(array('label' => '保存', 'div' => false, 'id' => 'WidgetUpdateWidgetSubmit', 'class' => 'button')) ?>
				</div>
			</div>
			<?php endforeach ?>
		<?php endforeach ?>
		</div>
	</div>
	
	<!-- 利用中のウィジェット -->
	<div id="TargetOuter">
		<div id="Target">

			<h2>利用中のウィジェット <?php $bcBaser->img('ajax-loader-s.gif', array(
				'style' => 'vertical-align:middle;display:none',
				'id'	=> 'WidgetAreaUpdateSortLoader',
				'class' => 'loader')) ?></h2>

		<?php if($bcForm->value('WidgetArea.widgets')): ?>
			<?php foreach($bcForm->value('WidgetArea.widgets') as $widget): ?>

				<?php $key = key($widget) ?>
				<?php $enabled = '' ?>
				<?php if($widget[$key]['status']): ?>
					<?php $enabled = ' enabled' ?>
				<?php endif ?>

				<div class="ui-widget-content sortable widget setting <?php echo $widget[$key]['element'] ?><?php echo $enabled ?>" id="Setting<?php echo $widget[$key]['id'] ?>">
					<div class="clearfix">
						<div class="widget-name display-none"><?php echo $widget[$key]['element'] ?></div>
						<div class="del">削除</div>
						<div class="action">設定</div>
						<div class="head"><?php echo $widget[$key]['name'] ?></div>
					</div>
					<div class="content" style="text-align:right">
						<p><small><?php echo $widget[$key]['type'] ?></small></p>
						<?php echo $bcForm->create('Widget', array('url' => array('controller' => 'widget_areas', 'action' => 'update_widget', $bcForm->value('WidgetArea.id'), 'id' => false), 'class' => 'form', 'id' => 'WidgetUpdateWidgetForm'.$widget[$key]['id'])) ?>
						<?php echo $bcForm->input($key.'.id', array('type' => 'hidden', 'class' => 'id')) ?>
						<?php echo $bcForm->input($key.'.type', array('type' => 'hidden')) ?>
						<?php echo $bcForm->input($key.'.element', array('type' => 'hidden')) ?>
						<?php echo $bcForm->input($key.'.plugin', array('type' => 'hidden')) ?>
						<?php echo $bcForm->input($key.'.sort', array('type' => 'hidden')) ?>
						<?php echo $bcForm->label($key.'name','タイトル') ?>&nbsp;
						<?php echo $bcForm->input($key.'.name', array('type' => 'text', 'class'=>'name')) ?><br />
						<?php $bcBaser->element('widgets/'.$widget[$key]['element'], array('key' => $key, 'plugin' => $widget[$key]['plugin'], 'mode' => 'edit')) ?><br />
						<?php $bcBaser->img('ajax-loader-s.gif', array('style' => 'vertical-align:middle;display:none', 'id' => 'WidgetUpdateWidgetLoader'.$widget[$key]['id'], 'class' => 'loader')) ?>
						<?php echo $bcForm->input($key.'.use_title', array('type' => 'checkbox', 'label' => 'タイトルを表示', 'class' => 'use_title')) ?>
						<?php echo $bcForm->input($key.'.status',array('type' => 'checkbox', 'label' => '利用する', 'class' => 'status')) ?>
						<?php echo $bcForm->end(array('label' => '保存', 'div' => false, 'id' => 'WidgetUpdateWidgetSubmit'.$widget[$key]['id'], 'class' => 'button')) ?>
					</div>
				</div>
				<?php endforeach ?>
			<?php endif ?>
		</div>
	</div>
</div>
<?php endif ?>