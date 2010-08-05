<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ウィジェットエリア編集
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
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<script type="text/javascript">
$(function() {
	$(".ui-widget-content").corner('10px');

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

			if(ui.item.attr("id").match(/^Setting/i)){
				widgetAreaUpdateSortedIds();
				return;
			}

			var baseId = 0;
			$("#Target .setting").each(function () {
				var _baseId = $(this).attr('id').replace('Setting','');
				if(_baseId > baseId){
					baseId = _baseId;
				}
			});
			baseId++;
			var id = ui.item.attr("id").replace('Widget','');
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

		}};
	$("#Target").sortable(sortableOptions
	).droppable(
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

	<?php if($this->action == 'admin_edit'): ?>
	$("#WidgetAreaUpdateTitleSubmit").click(function(){
		widgetAreaUpdateTitle();
		return false;
	});
	<?php endif ?>

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
		url: '<?php $baser->root() ?>admin/widget_areas/del_widget/<?php echo $formEx->value('WidgetArea.id') ?>/'+id,
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

<div id="flashMessage" class="message" style="display:none"></div>

<h2><?php $baser->contentsTitle() ?>&nbsp;<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>一つのウィジェットエリアは、左側の「利用できるウィジェット」からお好きなウィジェットを複数選択して作成する事ができます。</p>
	<ul>
		<li>まず、わかりやすい「ウィジェットエリア名」を決めて入力します。（例）サイドバー等</li>
		<li>「エリア名を保存する」ボタンをクリックすると「利用できるウィジェット」と「利用中のウィジェット」の二つの領域が表示されます</li>
		<li>「利用できるウィジェット」の中から利用したいウィジェットをドラッグして「利用中のウィジェット」の中でドロップします。</li>
		<li>ウィジェットの設定欄が開きますので必要に応じて入力し「保存」ボタンをクリックします。</li>
	</ul>
	<h5>ポイント</h5>
	<ul>
		<li>「利用中のウィジェット」はドラッグアンドドロップで並び替える事ができます。</li>
		<li>一時的に利用しない場合は、削除せずにウィジェット設定の「利用する」チェックを外しておくと同じ設定のまま後で利用する事ができます。</li>
	</ul>
</div>

<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->create('WidgetArea',array('action'=>'add')) ?>
<?php elseif($this->action == 'admin_edit'): ?>
	<?php echo $formEx->create('WidgetArea',array('action'=>'update_title')) ?>
<?php endif ?>
<?php echo $formEx->hidden('WidgetArea.id') ?>
<?php echo $formEx->label('WidgetArea.name','ウィジェットエリア名') ?>&nbsp;
<?php echo $formEx->text('WidgetArea.name',array('size'=>40)) ?>&nbsp;
<?php echo $formEx->end(array('label'=>'エリア名を保存する','div'=>false,'class'=>'button btn-red','id'=>'WidgetAreaUpdateTitleSubmit')) ?>
<?php $baser->img('ajax-loader-s.gif',array('style'=>'vertical-align:middle;display:none','id'=>'WidgetAreaUpdateTitleLoader')) ?>
<?php echo $formEx->error('WidgetArea.name') ?>

<?php if(!empty($widgetInfos)): ?>
<?php echo $formEx->create('WidgetArea',array('url'=>array($formEx->value('WidgetArea.id')),'action'=>'update_sort')) ?>
<?php echo $formEx->hidden('WidgetArea.sorted_ids') ?>
<?php echo $formEx->end() ?>
<div id="WidgetSetting" class="clearfix" >

	<div id="Source" class="corner10">

		<h3>利用できるウィジェット</h3>
		<?php foreach($widgetInfos as $widgetInfo) : ?>
		<h4><?php echo $widgetInfo['title'] ?></h4>
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
		$widget['name'] = basename($file,'.ctp');
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
				<?php echo $formEx->create('Widget',array('url'=>array('controller'=>'widget_areas',$formEx->value('WidgetArea.id')),'action'=>'update_widget','class'=>'form')) ?>
				<?php echo $formEx->hidden('Widget.id',array('class'=>'id')) ?>
				<?php echo $formEx->hidden('Widget.type',array('value'=>$widget['title'])) ?>
				<?php echo $formEx->hidden('Widget.element',array('value'=>$widget['name'])) ?>
				<?php echo $formEx->hidden('Widget.plugin',array('value'=>$widgetInfo['plugin'])) ?>
				<?php echo $formEx->hidden('Widget.sort') ?>
				<?php echo $formEx->label('Widget.name','タイトル') ?>&nbsp;
				<?php echo $formEx->text('Widget.name',array('class'=>'name')) ?><br />
				<?php echo $widget['setting'] ?><br />
				<?php $baser->img('ajax-loader-s.gif',array('style'=>'vertical-align:middle;display:none','id'=>'WidgetUpdateWidgetLoader','class'=>'loader')) ?>
				<?php echo $formEx->checkbox('Widget.use_title',array('label'=>'タイトルを表示','class'=>'use_title','checked'=>'checked')) ?>
				<?php echo $formEx->checkbox('Widget.status',array('label'=>'利用する','class'=>'status')) ?>
				<?php echo $formEx->end(array('label'=>'保　存','div'=>false,'id'=>'WidgetUpdateWidgetSubmit','class'=>'submit')) ?>
			</div>
		</div>
			<?php endforeach ?>
		<?php endforeach ?>
	</div>

	<div id="Target" class="corner10">
		<h3>利用中のウィジェット <?php $baser->img('ajax-loader-s.gif',array('style'=>'vertical-align:middle;display:none','id'=>'WidgetAreaUpdateSortLoader','class'=>'loader')) ?></h3>
		<?php if($formEx->value('WidgetArea.widgets')): ?>
			<?php foreach($formEx->value('WidgetArea.widgets') as $widget): ?>
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
					<?php echo $formEx->create('Widget',array('url'=>'/admin/widget_areas/update_widget/'.$formEx->value('WidgetArea.id'),'action'=>'update_widget','class'=>'form','id'=>'WidgetUpdateWidgetForm'.$widget[$key]['id'])) ?>
					<?php echo $formEx->hidden($key.'.id',array('class'=>'id')) ?>
					<?php echo $formEx->hidden($key.'.type') ?>
					<?php echo $formEx->hidden($key.'.element') ?>
					<?php echo $formEx->hidden($key.'.plugin') ?>
					<?php echo $formEx->hidden($key.'.sort') ?>
					<?php echo $formEx->label($key.'name','タイトル') ?>&nbsp;
					<?php echo $formEx->text($key.'.name',array('class'=>'name')) ?><br />
					<?php $baser->element('widgets/'.$widget[$key]['element'],array('key'=>$key,'plugin'=>$widget[$key]['plugin'])) ?><br />
					<?php $baser->img('ajax-loader-s.gif',array('style'=>'vertical-align:middle;display:none','id'=>'WidgetUpdateWidgetLoader'.$widget[$key]['id'],'class'=>'loader')) ?>
					<?php echo $formEx->checkbox($key.'.use_title',array('label'=>'タイトルを表示','class'=>'use_title')) ?>
					<?php echo $formEx->checkbox($key.'.status',array('label'=>'利用する','class'=>'status')) ?>
					<?php echo $formEx->end(array('label'=>'保　存','div'=>false,'id'=>'WidgetUpdateWidgetSubmit'.$widget[$key]['id'],'class'=>'submit')) ?>
				</div>
			</div>
			<?php endforeach ?>
		<?php endif ?>
	</div>
</div>
<?php endif ?>