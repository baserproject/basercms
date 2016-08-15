<?php
/**
 * ウィジェットエリア（スマホ用）
 *
 * BcBaserHelper::widgetArea() で呼び出す
 * <?php $this->BcBaser->widgetArea() ?>
 */
if (Configure::read('BcRequest.isMaintenance') || empty($no)) {
	return;
}
if (!isset($subDir)) {
	$subDir = true;
}
?>


<div class="widget-area widget-area-<?php echo $no ?>">
	<?php $this->BcWidgetArea->show($no, array('subDir' => $subDir)) ?>
</div>
