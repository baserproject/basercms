<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] グローバルメニューフォーム
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
if($this->action == 'admin_view'){
    $freeze->freeze();
}
?>
<script type="text/javascript">
    $(function(){
       $("#GlobalMenuMenuType").change(function(){
           $.ajax({
               'url': '/admin/global_menus/get_max_sort/'+$("#GlobalMenuMenuType").val(),
               'type': 'GET',
               'success': function(data){
                   if(data){
                       $("#GlobalMenuSort").val(data);
                   }
               }
           });
       });
    });
</script>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $freeze->create('GlobalMenu') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_view' || $this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $form->label('GlobalMenu.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $freeze->text('GlobalMenu.no', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('GlobalMenu.name', 'メニュー名') ?></th>
		<td class="col-input"><?php echo $freeze->text('GlobalMenu.name', array('size'=>40,'maxlength'=>255)) ?><?php echo $form->error('GlobalMenu.name') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('GlobalMenu.menu_type', 'タイプ') ?></th>
		<td class="col-input"><?php echo $freeze->select('GlobalMenu.menu_type', $formEx->getControlSource('GlobalMenu.menu_type'),null,array(),false) ?><?php echo $form->error('GlobalMenu.menu_type') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('GlobalMenu.link', 'リンクURL') ?></th>
		<td class="col-input">
            <?php echo $freeze->text('GlobalMenu.link', array('size'=>40,'maxlength'=>255)) ?><?php echo $form->error('GlobalMenu.link') ?>
            <?php echo $html->image('help.png',array('id'=>'helpLink','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextLink" class="helptext">
                先頭にスラッシュつけたルートパスで入力して下さい。<br />(例) /admin/global/index
            </div>
            &nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('GlobalMenu.status', '利用状態') ?></th>
		<td class="col-input">
            <?php echo $freeze->radio('GlobalMenu.status', $textEx->booleanDoList("利用"),array("legend"=>false,"separator"=>"&nbsp;&nbsp;")) ?>
            <?php echo $freeze->error('GlobalMenu.status') ?>
            &nbsp;
		</td>
	</tr>
</table>

<div class="submit">
    <?php if($this->action == 'admin_add'): ?>
        <?php echo $freeze->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
    <?php elseif ($this->action == 'admin_edit'): ?>
        <?php echo $freeze->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
    <?php else: ?>
        <?php $baser->link('編集する',array('action'=>'edit',$form->value('GlobalMenu.id')),array('class'=>'btn-orange button'),null,false) ?>　
        <?php $baser->link('削除する',array('action'=>'delete', $form->value('GlobalMenu.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $form->value('GlobalMenu.name')),false); ?>
    <?php endif ?>
</div>