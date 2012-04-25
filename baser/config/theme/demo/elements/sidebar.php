<?php
/**
 * サイドバー
 */
?>
<div id="beta">
	<?php if(!empty($widgetArea)): ?>
	<?php $bcBaser->element('widget_area',array('no'=>$widgetArea)) ?>
	<?php endif ?>
</div>
