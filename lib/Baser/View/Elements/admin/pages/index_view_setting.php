<?php
$pageTypes = array();
if($reflectMobile || $reflectSmartphone) {
	$pageTypes = array('1' => 'PC');	
}
if($reflectMobile) {
	$pageTypes['2'] = 'モバイル';
}
if($reflectSmartphone) {
	$pageTypes['3'] = 'スマートフォン';
}
?>

<script type="text/javascript">
$(function(){
	$("input[name='data[ViewSetting][view_type]']").click(function(){
		var viewType = $("input[name='data[ViewSetting][view_type]']:checked").val();
		location.href = $("#PageIndexUrl").html()+'/view_type:'+viewType;
	});
	$("input[name='data[ViewSetting][page_type]']").click(function(){
		var pageType = $("input[name='data[ViewSetting][page_type]']:checked").val();
		$.ajax({
			type: "GET",
			url: $("#PageIndexUrl").html()+'/page_type:'+pageType,
			beforeSend: function() {
				$("#Waiting").show();
			},
			success: function(result){
				if(result) {
					$("#DataList").html(result);
					if($("input[name='data[ViewSetting][view_type]']:checked").val() == "1") {
						$.baserAjaxDataList.initList();
					}
				}
			},
			complete: function() {
				$("#Waiting").hide();
			}
		});
	});
});
</script>

<div id="PageIndexUrl" style="display:none"><?php $this->BcBaser->url(array('controller' => 'pages', 'action' => 'index')) ?></div>

<div class="panel-box">
<?php if($pageTypes): ?>
	<small>ページタイプ</small> <?php echo $this->BcForm->input('ViewSetting.page_type', array('type' => 'radio', 'options' => $pageTypes)) ?>　　／　
<?php endif ?>
	<small>表示形式</small> <?php echo $this->BcForm->input('ViewSetting.view_type', array('type' => 'radio', 'options' => array('1' => '表', '2' => 'ツリー'))) ?>
</div>
