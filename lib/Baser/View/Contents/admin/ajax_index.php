<?php
header('Content-type: text/html; charset=utf-8');
?>


<script>
    $.getScript($.baseUrl + '/js/admin/contents/index_tree.js')
</script>

<?php if(!empty($datas)): ?>
<div id="ContentsTreeList" style="display:none">
<?php $this->BcBaser->element('contents/index_list'); ?>
</div>
<?php elseif($this->action == 'admin_trash_index'): ?>
<div class="em-box">ゴミ箱は空です</div>
<?php endif ?>