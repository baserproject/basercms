<?php
/**
 * [ADMIN] アクセス制限管理（ポップアップ）
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
?>


<script type="text/javascript">
$(function(){
		
	$("#BtnMenuPermission").click(function(){
		var y = $(this).position().top;
		var x = $(this).position().left-500;
		
		$("#PermissionDialog").dialog('option', 'position', [x, y]);
		$('#PermissionDialog').dialog('open');
		return false;
	});
/**
 * バリデーション
 */
	$("#PermissionAjaxAddForm").validate();
	$("#PermissionAjaxAddForm").submit(function(){return false});
/**
 * ダイアログを初期化
 */
	$("#PermissionDialog").dialog({
		bgiframe: true,
		autoOpen: false,
		position: [250, 150],
		width: 'auto',
		modal: true,
		open: function(event, ui){
			$("#PermissionName").val($("#CurrentPageName").html());
			$("#PermissionUrl").val($("#CurrentPageUrl").html().replace($("#PermissionAdmin").html(), ''));
			$("#PermissionAjaxAddForm").submit();
			$("#PermissionName").focus();
		},
		close: function() {
			$("#PermissionName").val('');
			$("#PermissionUrl").val('');
		},
		buttons: {
			'キャンセル': function() {
				$(this).dialog('close');
			},
			'保存': function() {
				
				$("#PermissionAjaxAddForm").submit();
				if($("#PermissionAjaxAddForm").valid()) {
					$("#PermissionAjaxAddForm").ajaxSubmit({
						beforeSend: function() {
							$("#Waiting").show();
						},
						success: function(response, status) {
							if(response) {
								$("#PermissionDialog").dialog('close');
							} else {
								alert('保存に失敗しました。');
							}
						},
						error: function() {
							alert('保存に失敗しました。');
						},
						complete: function(){
							$("#Waiting").hide();
						}
					});
				}
			}
		}

	});
});
</script>


<div id="PermissionDialog" title="アクセス制限登録" style="display:none">
	<?php echo $this->BcForm->create('Permission', array('action' => 'ajax_add', 'url' => array('plugin' => null))) ?>
	<?php echo $this->BcForm->input('Permission.id') ?>
	<dl>
		<dt><?php echo $this->BcForm->label('Permission.user_group_id', 'ユーザーグループ') ?></dt>
		<dd class="col-input">
			<?php echo $this->BcForm->input('Permission.user_group_id', array('type' => 'select', 'options' => $this->BcForm->getControlSource('Permission.user_group_id'))) ?>
		</dd>
		<dt><h4><?php echo $this->BcForm->label('Permission.name', 'ルール名') ?></h4></dt>
		<dd><?php echo $this->BcForm->input('Permission.name', array('type' => 'text', 'size' => 30, 'class' => 'required')) ?></dd>
		<dt><?php echo $this->BcForm->label('Permission.url', 'URL設定') ?></dt>
		<dd><strong id="PermissionAdmin">/<?php echo Configure::read('Routing.prefixes.0') ?>/</strong><?php echo $this->BcForm->input('Permission.url', array('type' => 'text', 'size' => 30, 'class' => 'required')) ?></dd>
		<dt><?php echo $this->BcForm->label('Permission.auth', 'アクセス') ?></dt>
		<dd>
			<?php
			echo $this->BcForm->input('Permission.auth', array(
				'type' => 'radio',
				'options' => $this->BcForm->getControlSource('Permission.auth'),
				'legend' => false,
				'value' => 0,
				'separator' => '　'))
			?>
	<?php echo $this->BcForm->error('Permission.auth') ?>
		</dd>
	</dl>
<?php echo $this->BcForm->end() ?>
</div>