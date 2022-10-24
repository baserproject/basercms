/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */


$(function() {
    var script = $("#AdminSearchScript");
    var adminSearchOpened = script.attr('data-adminSearchOpened');
    var adminSearchOpenedTarget = script.attr('data-adminSearchOpenedTarget');

    changeSearchBox(adminSearchOpenedTarget, adminSearchOpened, 0);

	$('#BtnMenuSearch').click(function(){
		if($('#Search').css('display') === 'none'){
			changeSearchBox(adminSearchOpenedTarget, true);
		} else {
			changeSearchBox(adminSearchOpenedTarget, false);
		}
	});

	$('#CloseSearch').click(function(){
		changeSearchBox(adminSearchOpenedTarget, false);
	});

    $('#BtnSearchClear').click(function () {
        $('#Search input[type="text"]').val("");
        $('#Search input[type="radio"], #Search input[type="checkbox"]').removeAttr('checked');
        $('#Search select').val('');
        return false;
    });

    /**
     * 検索ボックスの開閉切り替え
     */
    function changeSearchBox(target, open, time) {
        if(time === undefined) time = 300;
        var url = $.bcUtil.apiBaseUrl + 'baser-core/utilities/save_search_opened/' + target;
        if(open){
            $('#Search').slideDown(time);
            url += '/1.json';
        } else {
            $('#Search').slideUp(time);
            url += '.json';
        }
        $.ajax({
            type: "POST",
            url: url,
            headers: {
                "Authorization": $.bcJwt.accessToken,
            },
        });
    }

});
