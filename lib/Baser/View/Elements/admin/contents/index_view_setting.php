<?php
if($this->action == 'admin_index') {
	echo $this->BcForm->hidden('ViewSetting.mode', array('value' => 'index'));
} elseif($this->action = 'admin_trash_index') {
	echo $this->BcForm->hidden('ViewSetting.mode', array('value' => 'trash'));
}
?>


<?php if($this->action == 'admin_index' && $sites): ?>
<div class="panel-box">
    <small>サイト</small> <?php echo $this->BcForm->input('ViewSetting.site_id', array('type' => 'radio', 'options' => $sites)) ?>
</div>
<?php endif ?>