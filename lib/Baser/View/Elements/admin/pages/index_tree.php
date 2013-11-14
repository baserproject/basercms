<script type="text/javascript">
$(function(){
	$("#TreeList").treeview({
		persist: "cookie",
		collapsed: true,
		animated: "fast"
	});
});
</script>

<div id="PageTreeList">
<?php echo $this->BcPage->treeList($datas) ?>
</div>