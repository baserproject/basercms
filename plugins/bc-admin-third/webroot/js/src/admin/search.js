/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */


$(function() {
    var script = $("#AdminSearchScript");
    var adminSearchOpened = script.attr('data-adminSearchOpened');
    var adminSearchOpenedSaveUrl = script.attr('data-adminSearchOpenedSaveUrl');

    changeSearchBox(adminSearchOpened);

	$('#BtnMenuSearch').click(function(){
		if($('#Search').css('display') === 'none'){
			changeSearchBox(true);
		} else {
			changeSearchBox(false);
		}
	});

	$('#CloseSearch').click(function(){
		changeSearchBox(false);
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
    function changeSearchBox(open) {
        var url = adminSearchOpenedSaveUrl;
        if(open){
            $('#Search').slideDown(300);
            url += '/1';
        } else {
            $('#Search').slideUp(300);
            url += '/';
        }
        $.ajax({type: "GET", url: url});
    }

});
