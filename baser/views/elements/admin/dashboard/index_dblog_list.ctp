<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ダッシュボード　(検索ログ一覧)
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<script type="text/javascript">
$ (function (){
	$('.pagination a, .list-num a').click(function(){
		var ajaxurl = $(this).attr('href');
		$.ajax({
			url:ajaxurl,
			type:'GET',
			dataType:'html',
			beforeSend:function(){
				$('#Waiting').show();
			},
			complete:function(){
				$('#Waiting').hide();
			},
			error:function(){
				$('#AlertMessage').html('データの取得に失敗しました。');
				$('#AlertMessage').fadeIn(500);
			},
			success:function(response,stuts){
				if(response){
					$('#DblogList').html(response);
				}else{
					$('#AlertMessage').html('データの取得に失敗しました。');
					$('#AlertMessage').fadeIn(500);
				}
			}
		});
		return false;
	});
});

</script>

<?php if($viewDblogs): ?>
<?php $this->passedArgs['action'] = 'ajax_dblog_index' ?>
<?php $baser->element('pagination', array('modules' => 4)) ?>
<ul class="clear">
	<?php foreach ($viewDblogs as $record): ?>
	<li><span class="date"><?php echo $time->format('Y.m.d',$record['Dblog']['created']) ?></span>
		<small><?php echo $time->format('H:i:s',$record['Dblog']['created']) ?>&nbsp;
			<?php if(!empty($record['User']['real_name_1'])): ?>
			[<?php echo $record['User']['real_name_1'] . $record['User']['real_name_2'] ?>]
			<?php endif ?>
		</small><br />
		<?php echo $record['Dblog']['name'] ?></li>
	<?php endforeach; ?>
</ul>
<?php $baser->element('list_num') ?>
<div class="submit clear">
	<?php $baser->link('削除',
			array('action' => 'del'),
			array('class'=>'btn-gray button'),
			'最近の動きのログを削除します。いいですか？') ?>
</div>
<?php endif; ?>