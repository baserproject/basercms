<?php
$this->BcBaser->css('/js/admin/jquery.treeview/jquery.treeview', array('inline' => false));
$this->BcBaser->js('admin/jquery.cookie', false);
$this->BcBaser->js('admin/jquery.treeview/jquery.treeview', false);
?>


<?php $this->BcBaser->element('pages/index_view_setting') ?>
<div id="DataList"><?php $this->BcBaser->element('pages/index_tree') ?></div>
