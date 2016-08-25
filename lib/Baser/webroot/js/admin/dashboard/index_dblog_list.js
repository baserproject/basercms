/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */


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
			success:function(response, status){
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